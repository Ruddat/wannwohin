<?php

namespace App\Services;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LocationImportService
{
    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        $this->geocodeService = $geocodeService;
    }

    public function import(string $filePath, bool $skipImages = false): bool
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
//dd($rows);


            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue; // Überspringe die Header-Zeile
                }

                // Hole den Stadtnamen aus der Datei
                $cityName = trim($row[3] ?? ''); // Spalte mit dem Stadtnamen

//dd($cityName);

                if (empty($cityName)) {
                    Log::warning("Fehlender Stadtname in Zeile {$index}");
                    continue; // Überspringe, wenn kein Stadtname vorhanden ist
                }

                try {
                    // Verwende den GeocodeService, um das Land basierend auf dem Stadtnamen zu finden
                    $result = $this->geocodeService->searchByAddress($cityName);
//dd($result);
                    if (empty($result)) {
                        Log::warning("Kein Ergebnis für Stadt '{$cityName}' in Zeile {$index}");
                        continue;
                    }

                    // Hole Land und Länder-Code aus dem Ergebnis
                    $countryName = $result[0]['address']['country'] ?? null;
                    $countryCode = $result[0]['address']['country_code'] ?? null;
//dd($countryName, $countryCode);
                    if (!$countryName) {
                        Log::warning("Kein Land gefunden für Stadt '{$cityName}' in Zeile {$index}");
                        continue;
                    }

                    // Verarbeite `best_traveltime_json` und ersetze Zahlen durch Monatsnamen
                    $bestTravelTime = $this->parseBestTravelTimeJson($row[52]);

                    // Fetch images if not skipped
                    $textPic1 = $skipImages ? null : $this->getCityImage($cityName, 1, $status);
                    $textPic2 = $skipImages ? null : $this->getCityImage($cityName, 2, $status);
                    $textPic3 = $skipImages ? null : $this->getCityImage($cityName, 3, $status);


                    // Finde das passende Land in der Datenbank
                    $country = WwdeCountry::where('country_code', strtoupper($countryCode))
                        ->orWhere('title', $countryName)
                        ->first();
                        //dd($country);

                    if (!$country) {
                        Log::warning("Kein Land in der Datenbank gefunden für '{$countryName}' (Code: {$countryCode})");
                        continue;
                    }

                    // Hole die Kontinent-ID aus der Beziehung
                    $continentId = $country->continent_id;
//dd($continentId);
                    // Speichere die Location
                    WwdeLocation::updateOrCreate(
                        ['iata_code' => $row[5] ?? null], // IATA-Code als eindeutiger Wert
                        [
                            'title' => $cityName,
                            'country_id' => $country->id,
                            'continent_id' => $continentId,
                            'lat' => $result[0]['lat'] ?? null,
                            'lon' => $result[0]['lon'] ?? null,
                            // Weitere Felder ...
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
                         //   'list_animal_park' => $listAnimalPark, // Verwende den Standardwert
                            'best_traveltime' => $row[29],

                            'pic1_text' => $row[30],
                            'pic2_text' => $row[31],
                            'pic3_text' => $row[32],
                            // bilder einfuegen
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
                           // 'climate_details_id' => $climateDetailsId, // Konvertierter Wert
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
                            'finished' => 1, // Immer auf 1 setzen
                            'best_traveltime_json' => $bestTravelTime['json'], // JSON-Wert speichern
                            'best_traveltime' => $bestTravelTime['range'], // Bereich der Monate, falls vorhanden

                            'panorama_text_and_style' => $row[53],
                            'time_zone' => $row[54],
                            'lat_new' => $result[0]['lat'] ?? null,
                            'lon_new' => $result[0]['lon'] ?? null,
                            'status' => 'active', // Standardstatus
                        ]
                    );

                    Log::info("Location erfolgreich importiert: {$cityName} (Land: {$countryName})");

                } catch (\Exception $e) {
                    Log::error("Fehler bei Geocoding für Stadt '{$cityName}' in Zeile {$index}: " . $e->getMessage());
                }

                // Pause von 1 Sekunde, um API-Limits einzuhalten
               // sleep(1);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Fehler beim Import: " . $e->getMessage());
            return false;
        }
    }

        /**
     * Verarbeite den Wert für `best_traveltime_json` und ersetze Zahlen durch Monatsnamen.
     *
     * @param mixed $value Der Wert aus der Excel-Datei.
     * @return string|null Gültiges JSON-Array mit Monatsnamen oder null, falls ungültig.
     */
    private function parseBestTravelTimeJson($value): ?array
    {
        // Mapping von Zahlen zu Monatsnamen
        $monthNames = [
            1 => 'Januar',
            2 => 'Februar',
            3 => 'März',
            4 => 'April',
            5 => 'Mai',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'August',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Dezember',
        ];

        $months = [];

        if (is_string($value)) {
            // Versuche, den Wert als JSON zu dekodieren
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $months = array_map('intval', $decoded);
            } else {
                // Wenn der Wert ein String ist, der Zahlen enthält (z. B. "1,2,3,4")
                $numbers = array_map('intval', explode(',', $value));
                if (!empty($numbers)) {
                    $months = $numbers;
                }
            }
        } elseif (is_array($value)) {
            $months = $value;
        }

        // Entferne ungültige Werte und sortiere die Monate
        $months = array_filter($months, function ($monthNumber) use ($monthNames) {
            return isset($monthNames[$monthNumber]);
        });
        sort($months);

        if (empty($months)) {
            return [
                'json' => json_encode([]), // Leeres JSON-Array
                'range' => null, // Kein Bereich verfügbar
            ];
        }

        // Ersetze Zahlen durch Monatsnamen
        $monthNamesList = array_map(function ($monthNumber) use ($monthNames) {
            return $monthNames[$monthNumber];
        }, $months);

        // Bereich bestimmen (z. B. "April - Oktober")
        $firstMonth = reset($months);
        $lastMonth = end($months);

        $range = null;
        if ($lastMonth - $firstMonth === count($months) - 1) {
            // Wenn die Monate zusammenhängend sind
            $range = "{$monthNames[$firstMonth]} - {$monthNames[$lastMonth]}";
        }

        return [
            'json' => json_encode($monthNamesList), // Gültiges JSON-Array mit Monatsnamen
            'range' => $range, // Bereich der Monate
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




}
