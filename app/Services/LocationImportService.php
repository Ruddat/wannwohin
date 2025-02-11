<?php

namespace App\Services;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LocationImportService
{
    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        $this->geocodeService = $geocodeService;
    }

    public function import(string $filePath, bool $skipImages = false, bool $exportFailed = false): bool
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $failedRows = []; // Speichert fehlgeschlagene Zeilen für den Export
            $headerRow = $rows[0]; // Header-Zeile für den Export

            // Fortschritt aus dem Cache holen
            $lastProcessedRow = Cache::get('last_processed_row', 0);

            foreach ($rows as $index => $row) {
                if ($index <= $lastProcessedRow) {
                    continue; // Überspringe bereits verarbeitete Zeilen
                }

                if ($index === 0) {
                    continue; // Überspringe die Header-Zeile
                }

                $cityName = trim($row[3] ?? ''); // Stadtname aus der Excel-Datei

                if (empty($cityName)) {
                    Log::warning("Fehlender Stadtname in Zeile {$index}");
                    $failedRows[] = $row; // Füge fehlgeschlagene Zeile hinzu
                    continue;
                }

                try {
                    // Geocoding-Daten holen
                    $result = $this->geocodeService->searchByAddress($cityName);

                    if (empty($result)) {
                        Log::warning("Kein Ergebnis für Stadt '{$cityName}' in Zeile {$index}");
                        $this->savePendingLocation($row, $cityName, $skipImages); // Speichere als pending
                        $failedRows[] = $row; // Füge fehlgeschlagene Zeile hinzu
                        continue;
                    }

                    $countryName = $result[0]['address']['country'] ?? null;
                    $countryCode = $result[0]['address']['country_code'] ?? null;
                    $countryCodeIso3 = $result[0]['address']['ISO3166-2-lvl4'] ?? null;

                    if (!$countryName) {
                        Log::warning("Kein Land gefunden für Stadt '{$cityName}' in Zeile {$index}");
                        $this->savePendingLocation($row, $cityName, $skipImages); // Speichere als pending
                        $failedRows[] = $row; // Füge fehlgeschlagene Zeile hinzu
                        continue;
                    }

                    // Finde das passende Land in der Datenbank
                    $country = WwdeCountry::where('country_code', strtoupper($countryCode))
                        ->orWhere('title', $countryName)
                        ->first();

                    if (!$country) {
                        Log::warning("Kein Land in der Datenbank gefunden für '{$countryName}' (Code: {$countryCode})");
                        $this->savePendingLocation($row, $cityName, $skipImages); // Speichere als pending
                        $failedRows[] = $row; // Füge fehlgeschlagene Zeile hinzu
                        continue;
                    }

                    // Verarbeite `best_traveltime_json`
                    $bestTravelTime = $this->parseBestTravelTimeJson($row[52]);

                    // Bilder herunterladen (falls nicht übersprungen)
                    $textPic1 = $skipImages ? null : $this->getCityImage($cityName, 1, $status);
                    $textPic2 = $skipImages ? null : $this->getCityImage($cityName, 2, $status);
                    $textPic3 = $skipImages ? null : $this->getCityImage($cityName, 3, $status);

                    // Berechne die `list_*` Felder
                    $listFields = $this->calculateListFields($row, $result[0]['lat'], $result[0]['lon']);

                    // Speichere die Location
                    WwdeLocation::updateOrCreate(
                        ['title' => $cityName, 'country_id' => $country->id], // Eindeutiger Schlüssel
                        [
                            'iata_code' => $row[5] ?? null,
                            'continent_id' => $country->continent_id,
                            'lat' => $result[0]['lat'] ?? null,
                            'lon' => $result[0]['lon'] ?? null,
                            'iso2' => $countryCode ?? null,
                            'iso3' => $countryCodeIso3 ?? null,
                            'old_id' => $row[0],
                            'currency_code' => $row[10],
                            'alias' => $row[4],
                            'flight_hours' => $row[6],
                            'stop_over' => $row[7],
                            'dist_from_FRA' => $row[8],
                            'dist_type' => $row[9],
                            'bundesstaat_long' => $row[12],
                            'bundesstaat_short' => $row[13],
                            'no_city_but' => $row[14],
                            'population' => 0,
                            'list_beach' => $listFields['list_beach'],
                            'list_citytravel' => $listFields['list_citytravel'],
                            'list_sports' => $listFields['list_sports'],
                            'list_island' => $listFields['list_island'],
                            'list_culture' => $listFields['list_culture'],
                            'list_nature' => $listFields['list_nature'],
                            'list_watersport' => $listFields['list_watersport'],
                            'list_wintersport' => $listFields['list_wintersport'],
                            'list_mountainsport' => $listFields['list_mountainsport'],
                            'list_biking' => $listFields['list_biking'],
                            'list_fishing' => $listFields['list_fishing'],
                            'list_amusement_park' => $listFields['list_amusement_park'],
                            'list_water_park' => $listFields['list_water_park'],
                            'best_traveltime' => $row[29],
                            'pic1_text' => $row[30],
                            'pic2_text' => $row[31],
                            'pic3_text' => $row[32],
                            'text_pic1' => $textPic1,
                            'text_pic2' => $textPic2,
                            'text_pic3' => $textPic3,
                            'text_headline' => isset($row[33]) ? substr($row[33], 0, 255) : null,
                            'text_short' => $row[34],
                            'text_location_climate' => $row[35],
                            'text_what_to_do' => $row[36],
                            'text_best_traveltime' => $row[37],
                            'text_sports' => $row[38],
                            'text_amusement_parks' => $row[39],
                            'climate_lnam' => $row[41],
                            'climate_details_lnam' => $row[42],
                            'price_flight' => $row[43],
                            'range_flight' => $row[44],
                            'price_hotel' => $row[45],
                            'range_hotel' => $row[46],
                            'price_rental' => $row[47] ?? null,
                            'range_rental' => $row[48] ?? null,
                            'price_travel' => $row[49],
                            'range_travel' => $row[50],
                            'finished' => 1,
                            'best_traveltime_json' => $bestTravelTime['json'],
                            'best_traveltime' => $bestTravelTime['range'],
                            'panorama_text_and_style' => $row[53],
                            'time_zone' => $row[54],
                            'lat_new' => $result[0]['lat'] ?? null,
                            'lon_new' => $result[0]['lon'] ?? null,
                            'status' => 'active', // Erfolgreich importierte Locations
                        ]
                    );

                    Log::info("Location erfolgreich importiert: {$cityName} (Land: {$countryName})");

                } catch (\Exception $e) {
                    Log::error("Fehler bei Geocoding für Stadt '{$cityName}' in Zeile {$index}: " . $e->getMessage());
                    $this->savePendingLocation($row, $cityName, $skipImages); // Speichere als pending
                    $failedRows[] = $row; // Füge fehlgeschlagene Zeile hinzu
                }

                // Speichere den Fortschritt im Cache
                Cache::put('last_processed_row', $index, now()->addHours(1));
            }

            // Exportiere fehlgeschlagene Zeilen in eine Excel-Datei
            if ($exportFailed && !empty($failedRows)) {
                $this->exportFailedRows($headerRow, $failedRows);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Fehler beim Import: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Speichert eine Location mit dem Status "pending".
     *
     * @param array $row Die Daten der Location aus der Excel-Datei.
     * @param string $cityName Der Name der Stadt.
     * @param bool $skipImages Bilder überspringen.
     */
    private function savePendingLocation(array $row, string $cityName, bool $skipImages)
    {
        // Bilder herunterladen (falls nicht übersprungen)
        $textPic1 = $skipImages ? null : $this->getCityImage($cityName, 1, $status);
        $textPic2 = $skipImages ? null : $this->getCityImage($cityName, 2, $status);
        $textPic3 = $skipImages ? null : $this->getCityImage($cityName, 3, $status);
//dd($row);
        // Speichere die Location mit Status "pending"
        WwdeLocation::updateOrCreate(
            ['iata_code' => $row[5] ?? null], // IATA-Code als eindeutiger Wert
            [
                'title' => $cityName,
                'country_id' => null, // Kein Land gefunden
                'continent_id' => null, // Kein Kontinent gefunden
                'lat' => null,
                'lon' => null,
                'iso2' => null,
                'iso3' => null,
                'old_id' => $row[0],
                'currency_code' => $row[10],
                'alias' => $row[4],
                'flight_hours' => $row[6],
                'stop_over' => $row[7],
                'dist_from_FRA' => $row[8],
                'dist_type' => $row[9],
                'bundesstaat_long' => $row[12],
                'bundesstaat_short' => $row[13],
                'no_city_but' => $row[14],
                'population' => 0,
                'list_beach' => $row[15],
                'list_citytravel' => $row[16],
                'list_sports' => $row[17],
                'list_island' => $row[18],
                'list_culture' => $row[19],
                'list_nature' => $row[20],
                'list_watersport' => $row[21],
                'list_wintersport' => $row[22],
                'list_mountainsport' => $row[23],
                'list_biking' => $row[24],
                'list_fishing' => $row[25],
                'list_amusement_park' => $row[26],
                'list_water_park' => $row[27],
                'best_traveltime' => $row[29],
                'pic1_text' => $row[30],
                'pic2_text' => $row[31],
                'pic3_text' => $row[32],
                'text_pic1' => $textPic1,
                'text_pic2' => $textPic2,
                'text_pic3' => $textPic3,
                'text_headline' => isset($row[33]) ? substr($row[33], 0, 255) : null,
                'text_short' => $row[34],
                'text_location_climate' => $row[35],
                'text_what_to_do' => $row[36],
                'text_best_traveltime' => $row[37],
                'text_sports' => $row[38],
                'text_amusement_parks' => $row[39],
                'climate_lnam' => $row[41],
                'climate_details_lnam' => $row[42],
                'price_flight' => $row[43],
                'range_flight' => $row[44],
                'price_hotel' => $row[45],
                'range_hotel' => $row[46],
                'price_rental' => $row[47] ?? null,
                'range_rental' => $row[48] ?? null,
                'price_travel' => $row[49],
                'range_travel' => $row[50],
                'finished' => 1,
                'best_traveltime_json' => $this->parseBestTravelTimeJson($row[52])['json'],
                'best_traveltime' => $this->parseBestTravelTimeJson($row[52])['range'],
                'panorama_text_and_style' => $row[53],
                'time_zone' => $row[54],
                'lat_new' => null,
                'lon_new' => null,
                'status' => 'pending', // Status auf pending setzen
            ]
        );

        Log::info("Location als pending gespeichert: {$cityName}");
    }

    /**
     * Exportiert fehlgeschlagene Zeilen in eine Excel-Datei.
     *
     * @param array $headerRow Die Header-Zeile der Excel-Datei.
     * @param array $failedRows Die fehlgeschlagenen Zeilen.
     */
    private function exportFailedRows(array $headerRow, array $failedRows)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Füge die Header-Zeile hinzu
        $sheet->fromArray([$headerRow], null, 'A1');

        // Füge die fehlgeschlagenen Zeilen hinzu
        $sheet->fromArray($failedRows, null, 'A2');

        // Speichere die Excel-Datei
        $writer = new Xlsx($spreadsheet);
        $fileName = 'failed_locations_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);
        $writer->save($filePath);

        Log::info("Fehlgeschlagene Zeilen wurden exportiert: {$filePath}");
    }


        /**
     * Verarbeite den Wert für `best_traveltime_json` und ersetze Zahlen durch Monatsnamen.
     *
     * @param mixed $value Der Wert aus der Excel-Datei.
     * @return string|null Gültiges JSON-Array mit Monatsnamen oder null, falls ungültig.
     */
    private function parseBestTravelTimeJson($value): ?array
    {
        $validMonths = range(1, 12); // Erlaubte Werte von 1 bis 12
        $months = [];

        if (is_string($value)) {
            // Versuche, den Wert als JSON zu dekodieren
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $months = array_map('intval', $decoded);
            } else {
                // Falls der Wert eine durch Komma getrennte Liste ist ("1,2,3,4")
                $months = array_map('intval', explode(',', $value));
            }
        } elseif (is_array($value)) {
            $months = array_map('intval', $value);
        }

        // Filtere ungültige Werte und sortiere die Monate
        $months = array_filter($months, function ($month) use ($validMonths) {
            return in_array($month, $validMonths, true);
        });

        sort($months);

        if (empty($months)) {
            return [
                'json' => json_encode([]), // Leeres JSON-Array mit Zahlen
                'range' => null, // Kein Bereich verfügbar
            ];
        }

        // Bereich bestimmen (z. B. "4 - 10" für April bis Oktober)
        $firstMonth = reset($months);
        $lastMonth = end($months);
        $range = (count($months) > 1 && ($lastMonth - $firstMonth === count($months) - 1))
            ? "$firstMonth - $lastMonth"
            : implode(', ', $months);

        return [
            'json' => json_encode(array_values($months)), // JSON-Array mit reinen Zahlen
            'range' => $range, // Bereich der Monate in Zahlen
        ];
    }


    private function getCityImage($city, $index, &$status)
    {
        // Cache-Schlüssel für das Bild
        $cacheKey = "city_image_{$city}_{$index}";

        // Überprüfen, ob das Bild bereits im Cache ist
        if (cache()->has($cacheKey)) {
            Log::info('Using cached image for city: ' . $city . ', index: ' . $index);
            return cache($cacheKey);
        }

        // Pixabay API-Schlüssel aus der Konfiguration holen
        $apiKey = config('services.pixabay.api_key', env('PIXABAY_API_KEY'));

        if (empty($apiKey)) {
            Log::error('Pixabay API key is missing.');
            $status = 'inactive'; // Kein API-Key, also Status auf inactive
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }

        // Pixabay API-URL
        $url = 'https://pixabay.com/api/';

        try {
            // API-Anfrage senden (nur 1 Bild pro Anfrage)
            $response = Http::get($url, [
                'key' => $apiKey,
                'q' => $city,
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'safesearch' => 'true',
                'per_page' => 5, // Maximal 5 Bilder abrufen
                //'per_page' => $index, // Nur so viele Bilder abrufen, wie der Index angibt
            ]);

            if ($response->successful()) {
                $images = $response->json()['hits'] ?? [];

                if (empty($images) || !isset($images[$index - 1])) {
                    Log::warning("No images found for city: {$city}, index: {$index}");
                    $status = 'pending'; // Keine Bilder gefunden
                    return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
                }

                // Bild-URL extrahieren
                $imageUrl = $images[$index - 1]['webformatURL'];

                // Speicherpfad erstellen (sanitize city name)
                $safeCityName = str_replace([' ', '/'], ['_', '-'], iconv('UTF-8', 'ASCII//TRANSLIT', $city));
                $directory = "uploads/images/locations/{$safeCityName}/";
                $fileName = "city_image_{$index}.jpg";

                // Verzeichnis erstellen, falls nicht vorhanden
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                // Bild speichern
                $imageContents = Http::get($imageUrl)->body();
                Storage::put($directory . $fileName, $imageContents);

                // Öffentliche URL generieren
                $storedImageUrl = Storage::url($directory . $fileName);

                // Bild-URL im Cache speichern (für 24 Stunden)
                cache([$cacheKey => $storedImageUrl], now()->addDay());

                Log::info("Image found, saved, and cached for city: {$city}, index: {$index}");
                return $storedImageUrl;
            } else {
                Log::error("Failed to fetch images from Pixabay API for city: {$city}, index: {$index}");
                $status = 'inactive'; // API-Fehler
                return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
            }
        } catch (\Exception $e) {
            Log::error("Error fetching image from Pixabay API for city: {$city}, index: {$index}. Error: {$e->getMessage()}");
            $status = 'inactive'; // Ausnahme, Status auf inactive
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }
    }


/**
 * Füllt die Felder `list_beach`, `list_citytravel`, usw. basierend auf der Nähe zu einem Freizeitpark.
 *
 * @param array $row Die Daten der Location aus der Excel-Datei.
 * @param float $latitude Die Breitengrad-Koordinate der Location.
 * @param float $longitude Die Längengrad-Koordinate der Location.
 * @return array Die aktualisierten Felder.
 */
private function calculateListFields(array $row, float $latitude, float $longitude): array
{
    // Standardwerte für die Felder
    $listFields = [
        'list_beach' => $row[15] ?? 0,
        'list_citytravel' => $row[16] ?? 0,
        'list_sports' => $row[17] ?? 0,
        'list_island' => $row[18] ?? 0,
        'list_culture' => $row[19] ?? 0,
        'list_nature' => $row[20] ?? 0,
        'list_watersport' => $row[21] ?? 0,
        'list_wintersport' => $row[22] ?? 0,
        'list_mountainsport' => $row[23] ?? 0,
        'list_biking' => $row[24] ?? 0,
        'list_fishing' => $row[25] ?? 0,
        'list_amusement_park' => $row[26] ?? 0,
        'list_water_park' => $row[27] ?? 0,
    ];

    // Prüfe, ob ein Freizeitpark in der Nähe ist (150 km Umkreis)
    $hasAmusementParkNearby = $this->hasAmusementParkNearby($latitude, $longitude);

    // Wenn ein Freizeitpark in der Nähe ist, setze `list_amusement_park` auf 1
    if ($hasAmusementParkNearby) {
        $listFields['list_amusement_park'] = 1;
    }

    // Prüfe, ob die Location für Städtereisen geeignet ist
    //$isCityTravel = $this->isCityTravelLocation($latitude, $longitude);

    // Wenn die Location für Städtereisen geeignet ist, setze `list_citytravel` auf 1
    //if ($isCityTravel) {
    //    $listFields['list_citytravel'] = 1;
    //}

    // Prüfe, ob die Location für Bergsport geeignet ist
    //$isMountainSport = $this->isMountainSportLocation($latitude, $longitude);
    //if ($isMountainSport) {
    //    $listFields['list_mountainsport'] = 1;
   // }

    // Prüfe, ob die Location für Radfahren geeignet ist
    //$isBiking = $this->isBikingLocation($latitude, $longitude);
    //if ($isBiking) {
    //    $listFields['list_biking'] = 1;
    //}


    return $listFields;
}




/**
 * Prüft, ob die Location für Städtereisen geeignet ist.
 *
 * @param float $latitude Die Breitengrad-Koordinate der Location.
 * @param float $longitude Die Längengrad-Koordinate der Location.
 * @return bool True, wenn die Location für Städtereisen geeignet ist, sonst false.
 */
private function isCityTravelLocation(float $latitude, float $longitude): bool
{
    // Radius in Kilometern
    $radius = 50; // 50 km Umkreis

    // Berechne die Grenzen des Umkreises
    $earthRadius = 6371; // Erdradius in km
    $maxLat = $latitude + rad2deg($radius / $earthRadius);
    $minLat = $latitude - rad2deg($radius / $earthRadius);
    $maxLon = $longitude + rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));
    $minLon = $longitude - rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));

    // Suche nach kulturellen Sehenswürdigkeiten oder größeren Städten in der Nähe
    $cityTravelLocations = DB::table('cultural_attractions') // Annahme: Tabelle mit kulturellen Sehenswürdigkeiten
        ->whereBetween('latitude', [$minLat, $maxLat])
        ->whereBetween('longitude', [$minLon, $maxLon])
        ->exists();

    return $cityTravelLocations;
}

/**
 * Prüft, ob die Location für Bergsport geeignet ist.
 *
 * @param float $latitude Die Breitengrad-Koordinate der Location.
 * @param float $longitude Die Längengrad-Koordinate der Location.
 * @return bool True, wenn die Location für Bergsport geeignet ist, sonst false.
 */
private function isMountainSportLocation(float $latitude, float $longitude): bool
{
    // Radius in Kilometern
    $radius = 50; // 50 km Umkreis

    // Berechne die Grenzen des Umkreises
    $earthRadius = 6371; // Erdradius in km
    $maxLat = $latitude + rad2deg($radius / $earthRadius);
    $minLat = $latitude - rad2deg($radius / $earthRadius);
    $maxLon = $longitude + rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));
    $minLon = $longitude - rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));

    // Suche nach Bergregionen oder Skigebieten in der Nähe
    $mountainLocations = DB::table('mountain_regions') // Annahme: Tabelle mit Bergregionen
        ->whereBetween('latitude', [$minLat, $maxLat])
        ->whereBetween('longitude', [$minLon, $maxLon])
        ->exists();

    return $mountainLocations;
}


/**
 * Prüft, ob die Location für Radfahren geeignet ist.
 *
 * @param float $latitude Die Breitengrad-Koordinate der Location.
 * @param float $longitude Die Längengrad-Koordinate der Location.
 * @return bool True, wenn die Location für Radfahren geeignet ist, sonst false.
 */
private function isBikingLocation(float $latitude, float $longitude): bool
{
    // Radius in Kilometern
    $radius = 50; // 50 km Umkreis

    // Berechne die Grenzen des Umkreises
    $earthRadius = 6371; // Erdradius in km
    $maxLat = $latitude + rad2deg($radius / $earthRadius);
    $minLat = $latitude - rad2deg($radius / $earthRadius);
    $maxLon = $longitude + rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));
    $minLon = $longitude - rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));

    // Suche nach Radwegen oder Mountainbike-Strecken in der Nähe
    $bikingLocations = DB::table('biking_routes') // Annahme: Tabelle mit Radwegen
        ->whereBetween('latitude', [$minLat, $maxLat])
        ->whereBetween('longitude', [$minLon, $maxLon])
        ->exists();

    return $bikingLocations;
}



/**
 * Prüft, ob ein Freizeitpark innerhalb eines 150 km Umkreises liegt.
 *
 * @param float $latitude Die Breitengrad-Koordinate der Location.
 * @param float $longitude Die Längengrad-Koordinate der Location.
 * @return bool True, wenn ein Freizeitpark in der Nähe ist, sonst false.
 */
private function hasAmusementParkNearby(float $latitude, float $longitude): bool
{
    // Radius in Kilometern
    $radius = 150;

    // Berechne die Grenzen des Umkreises
    $earthRadius = 6371; // Erdradius in km
    $maxLat = $latitude + rad2deg($radius / $earthRadius);
    $minLat = $latitude - rad2deg($radius / $earthRadius);
    $maxLon = $longitude + rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));
    $minLon = $longitude - rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));

    // Suche nach Freizeitparks innerhalb des Umkreises
    $parks = DB::table('amusement_parks')
        ->whereBetween('latitude', [$minLat, $maxLat])
        ->whereBetween('longitude', [$minLon, $maxLon])
        ->exists();

    return $parks;
}

}
