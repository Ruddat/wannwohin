<?php

namespace App\Services;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LocationImportService
{
    protected const BATCH_SIZE = 50;
    protected const IMAGE_CACHE_DURATION = 1440; // 24 Stunden in Minuten

    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        $this->geocodeService = $geocodeService;
    }

    public function import(string $filePath, bool $skipImages = false, bool $exportFailed = false): bool
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $rows = $spreadsheet->getActiveSheet()->toArray(); // Wie im alten Skript
            $headerRow = $rows[0];
            $lastProcessedRow = Cache::get('last_processed_row', 0);
            $locationsToInsert = [];
            $failedRows = [];

            foreach ($rows as $index => $row) {
                if ($index <= $lastProcessedRow || $index === 0) {
                    continue; // Wie im alten Skript
                }

                $cityName = trim($row[3] ?? '');
                if (empty($cityName)) {
                    Log::warning("Fehlender Stadtname in Zeile {$index}");
                    $failedRows[] = $row;
                    continue;
                }

                try {
                    $result = $this->geocodeService->searchByAddress($cityName);
                    if (empty($result)) {
                        Log::warning("Kein Ergebnis für Stadt '{$cityName}' in Zeile {$index}");
                        $this->savePendingLocation($row, $cityName, $skipImages);
                        $failedRows[] = $row;
                        continue;
                    }

                    $countryName = $result[0]['address']['country'] ?? null;
                    $countryCode = $result[0]['address']['country_code'] ?? null;
                    $countryCodeIso3 = $result[0]['address']['ISO3166-2-lvl4'] ?? null;

                    if (!$countryName) {
                        Log::warning("Kein Land gefunden für Stadt '{$cityName}' in Zeile {$index}");
                        $this->savePendingLocation($row, $cityName, $skipImages);
                        $failedRows[] = $row;
                        continue;
                    }

                    $country = WwdeCountry::where('country_code', strtoupper($countryCode))
                        ->orWhere('title', $countryName)
                        ->first();

                    if (!$country) {
                        Log::warning("Kein Land in der Datenbank gefunden für '{$countryName}' (Code: {$countryCode})");
                        $this->savePendingLocation($row, $cityName, $skipImages);
                        $failedRows[] = $row;
                        continue;
                    }

                    $bestTravelTime = $this->parseBestTravelTimeJson($row[52] ?? null);
                    $status = 'active'; // Standardwert
                    $textPic1 = $skipImages ? null : $this->getCityImage($cityName, 1, $status);
                    $textPic2 = $skipImages ? null : $this->getCityImage($cityName, 2, $status);
                    $textPic3 = $skipImages ? null : $this->getCityImage($cityName, 3, $status);
                    $listFields = $this->calculateListFields($row, $result[0]['lat'] ?? 0, $result[0]['lon'] ?? 0);

                    $locationData = [
                        'title' => $cityName,
                        'country_id' => $country->id,
                        'iata_code' => $row[5] ?? null,
                        'continent_id' => $country->continent_id,
                        'lat' => $result[0]['lat'] ?? null,
                        'lon' => $result[0]['lon'] ?? null,
                        'iso2' => $countryCode ?? null,
                        'iso3' => $countryCodeIso3 ?? null,
                        'old_id' => $row[0] ?? null,
                        'currency_code' => $row[10] ?? null,
                        'alias' => $row[4] ?? null,
                      //  'flight_hours' => $row[6] ?? null,
                        'flight_hours' => isset($row[6]) ? round((float)$row[6], 2) : null, // Auf 2 Nachkommastellen gerundet
                        'stop_over' => $row[7] ?? null,
                        'dist_from_FRA' => $row[8] ?? null,
                        'dist_type' => $row[9] ?? null,
                        'bundesstaat_long' => $row[12] ?? null,
                        'bundesstaat_short' => $row[13] ?? null,
                        'no_city_but' => $row[14] ?? null,
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
                        'best_traveltime' => $row[29] ?? null,
                        'pic1_text' => $row[30] ?? null,
                        'pic2_text' => $row[31] ?? null,
                        'pic3_text' => $row[32] ?? null,
                        'text_pic1' => $textPic1,
                        'text_pic2' => $textPic2,
                        'text_pic3' => $textPic3,
                        'text_headline' => isset($row[33]) ? substr($row[33], 0, 255) : null,
                        'text_short' => $row[34] ?? null,
                        'text_location_climate' => $row[35] ?? null,
                        'text_what_to_do' => $row[36] ?? null,
                        'text_best_traveltime' => $row[37] ?? null,
                        'text_sports' => $row[38] ?? null,
                        'text_amusement_parks' => $row[39] ?? null,
                        'climate_lnam' => $row[41] ?? null,
                        'climate_details_lnam' => $row[42] ?? null,
                        'price_flight' => $row[43] ?? null,
                        'range_flight' => $row[44] ?? null,
                        'price_hotel' => $row[45] ?? null,
                        'range_hotel' => $row[46] ?? null,
                        'price_rental' => $row[47] ?? null,
                        'range_rental' => $row[48] ?? null,
                        'price_travel' => $row[49] ?? null,
                        'range_travel' => $row[50] ?? null,
                        'finished' => 1,
                        'best_traveltime_json' => $bestTravelTime['json'],
                        'best_traveltime' => $bestTravelTime['range'],
                        'panorama_text_and_style' => $row[53] ?? null,
                        'time_zone' => $row[54] ?? null,
                        'lat_new' => $result[0]['lat'] ?? null,
                        'lon_new' => $result[0]['lon'] ?? null,
                        'status' => $status,
                    ];

                    $locationsToInsert[] = $locationData;

                    if (count($locationsToInsert) >= self::BATCH_SIZE) {
                        $this->upsertLocations($locationsToInsert);
                        $locationsToInsert = [];
                    }

                    Log::info("Location erfolgreich importiert: {$cityName} (Land: {$countryName})");
                } catch (\Exception $e) {
                    Log::error("Fehler bei Geocoding für Stadt '{$cityName}' in Zeile {$index}: " . $e->getMessage());
                    $this->savePendingLocation($row, $cityName, $skipImages);
                    $failedRows[] = $row;
                }

                Cache::put('last_processed_row', $index, now()->addHours(1));
            }

            if (!empty($locationsToInsert)) {
                $this->upsertLocations($locationsToInsert);
            }

            if ($exportFailed && !empty($failedRows)) {
                $this->exportFailedRows($headerRow, $failedRows);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Fehler beim Import: " . $e->getMessage());
            return false;
        }
    }

    private function upsertLocations(array $locations): void
    {
        try {
            DB::transaction(function () use ($locations) {
                foreach ($locations as $location) {
                    WwdeLocation::updateOrCreate(
                        ['title' => $location['title'], 'country_id' => $location['country_id']],
                        $location
                    );
                }
            });
        } catch (\Exception $e) {
            Log::error("Fehler beim Upsert: " . $e->getMessage());
            throw $e;
        }
    }

    private function savePendingLocation(array $row, string $cityName, bool $skipImages): void
    {
        $status = 'pending';
        $textPic1 = $skipImages ? null : $this->getCityImage($cityName, 1, $status);
        $textPic2 = $skipImages ? null : $this->getCityImage($cityName, 2, $status);
        $textPic3 = $skipImages ? null : $this->getCityImage($cityName, 3, $status);
        $bestTravelTime = $this->parseBestTravelTimeJson($row[52] ?? null);

        WwdeLocation::updateOrCreate(
            ['iata_code' => $row[5] ?? null],
            [
                'title' => $cityName,
                'country_id' => null,
                'continent_id' => null,
                'lat' => null,
                'lon' => null,
                'iso2' => null,
                'iso3' => null,
                'old_id' => $row[0] ?? null,
                'currency_code' => $row[10] ?? null,
                'alias' => $row[4] ?? null,
                // 'flight_hours' => $row[6] ?? null,
                'flight_hours' => isset($row[6]) ? round((float)$row[6], 2) : null, // Auf 2 Nachkommastellen gerundet
                'stop_over' => $row[7] ?? null,
                'dist_from_FRA' => $row[8] ?? null,
                'dist_type' => $row[9] ?? null,
                'bundesstaat_long' => $row[12] ?? null,
                'bundesstaat_short' => $row[13] ?? null,
                'no_city_but' => $row[14] ?? null,
                'population' => 0,
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
                'best_traveltime' => $row[29] ?? null,
                'pic1_text' => $row[30] ?? null,
                'pic2_text' => $row[31] ?? null,
                'pic3_text' => $row[32] ?? null,
                'text_pic1' => $textPic1,
                'text_pic2' => $textPic2,
                'text_pic3' => $textPic3,
                'text_headline' => isset($row[33]) ? substr($row[33], 0, 255) : null,
                'text_short' => $row[34] ?? null,
                'text_location_climate' => $row[35] ?? null,
                'text_what_to_do' => $row[36] ?? null,
                'text_best_traveltime' => $row[37] ?? null,
                'text_sports' => $row[38] ?? null,
                'text_amusement_parks' => $row[39] ?? null,
                'climate_lnam' => $row[41] ?? null,
                'climate_details_lnam' => $row[42] ?? null,
                'price_flight' => $row[43] ?? null,
                'range_flight' => $row[44] ?? null,
                'price_hotel' => $row[45] ?? null,
                'range_hotel' => $row[46] ?? null,
                'price_rental' => $row[47] ?? null,
                'range_rental' => $row[48] ?? null,
                'price_travel' => $row[49] ?? null,
                'range_travel' => $row[50] ?? null,
                'finished' => 1,
                'best_traveltime_json' => $bestTravelTime['json'],
                'best_traveltime' => $bestTravelTime['range'],
                'panorama_text_and_style' => $row[53] ?? null,
                'time_zone' => $row[54] ?? null,
                'lat_new' => null,
                'lon_new' => null,
                'status' => $status,
            ]
        );

        Log::info("Location als pending gespeichert: {$cityName}");
    }

    private function exportFailedRows(array $headerRow, array $failedRows): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([$headerRow], null, 'A1');
        $sheet->fromArray($failedRows, null, 'A2');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'failed_locations_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path("app/{$fileName}");
        $writer->save($filePath);

        Log::info("Fehlgeschlagene Zeilen exportiert: {$filePath}");
    }

    private function parseBestTravelTimeJson($value): array
    {
        $validMonths = range(1, 12);
        $months = [];

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $months = array_map('intval', $decoded);
            } else {
                $months = array_map('intval', explode(',', $value));
            }
        } elseif (is_array($value)) {
            $months = array_map('intval', $value);
        }

        $months = array_filter($months, fn($month) => in_array($month, $validMonths, true));
        sort($months);

        if (empty($months)) {
            return ['json' => json_encode([]), 'range' => null];
        }

        $firstMonth = reset($months);
        $lastMonth = end($months);
        $range = (count($months) > 1 && ($lastMonth - $firstMonth === count($months) - 1))
            ? "$firstMonth - $lastMonth"
            : implode(', ', $months);

        return ['json' => json_encode(array_values($months)), 'range' => $range];
    }

    private function getCityImage($city, $index, &$status)
    {
        $cacheKey = "city_image_{$city}_{$index}";
        if (Cache::has($cacheKey)) {
            Log::info("Using cached image for city: {$city}, index: {$index}");
            return Cache::get($cacheKey);
        }

        $apiKey = config('services.pixabay.api_key', env('PIXABAY_API_KEY'));
        if (empty($apiKey)) {
            Log::error('Pixabay API key is missing.');
            $status = 'inactive';
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }

        $url = 'https://pixabay.com/api/';
        try {
            $response = Http::get($url, [
                'key' => $apiKey,
                'q' => $city,
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'safesearch' => 'true',
                'per_page' => 5,
            ]);

            if ($response->successful()) {
                $images = $response->json()['hits'] ?? [];
                if (empty($images) || !isset($images[$index - 1])) {
                    Log::warning("No images found for city: {$city}, index: {$index}");
                    $status = 'pending';
                    return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
                }

                $imageUrl = $images[$index - 1]['webformatURL'];
                $safeCityName = str_replace([' ', '/'], ['_', '-'], iconv('UTF-8', 'ASCII//TRANSLIT', $city));
                $directory = "uploads/images/locations/{$safeCityName}/";
                $fileName = "city_image_{$index}.jpg";

                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                Storage::put($directory . $fileName, Http::get($imageUrl)->body());
                $storedImageUrl = Storage::url($directory . $fileName);

                Cache::put($cacheKey, $storedImageUrl, now()->addDay());
                Log::info("Image found, saved, and cached for city: {$city}, index: {$index}");
                return $storedImageUrl;
            } else {
                Log::error("Failed to fetch images from Pixabay API for city: {$city}, index: {$index}");
                $status = 'inactive';
                return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
            }
        } catch (\Exception $e) {
            Log::error("Error fetching image from Pixabay API for city: {$city}, index: {$index}. Error: {$e->getMessage()}");
            $status = 'inactive';
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }
    }

    private function calculateListFields(array $row, float $latitude, float $longitude): array
    {
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

        if ($this->hasAmusementParkNearby($latitude, $longitude)) {
            $listFields['list_amusement_park'] = 1;
        }

        return $listFields;
    }

    private function hasAmusementParkNearby(float $latitude, float $longitude): bool
    {
        $radius = 150;
        $earthRadius = 6371;
        $maxLat = $latitude + rad2deg($radius / $earthRadius);
        $minLat = $latitude - rad2deg($radius / $earthRadius);
        $maxLon = $longitude + rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));
        $minLon = $longitude - rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));

        return DB::table('amusement_parks')
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLon, $maxLon])
            ->exists();
    }
}
