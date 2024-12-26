<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class ImportWorldCities extends Command
{
    protected $signature = 'locations:import-world-cities {--format=csv : The file format to process (csv or xlsx)}';
    protected $description = 'Download and import world cities for existing countries from SimpleMaps ZIP (contains CSV and XLSX)';

    public function handle()
    {
        $this->info('Starting to import world cities...');

        // Dateiformat auswählen (CSV oder XLSX)
        $format = $this->option('format');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $this->error('Invalid format. Use "csv" or "xlsx".');
            return;
        }

        // ZIP herunterladen
        $zipPath = $this->downloadZip();

        if (!$zipPath) {
            $this->error('Failed to download the ZIP file.');
            return;
        }

        // ZIP extrahieren
        $extractedPath = $this->unzipFile($zipPath);

        if (!$extractedPath) {
            $this->error('Failed to extract the ZIP file.');
            return;
        }

        // CSV oder XLSX verarbeiten
        $filePath = $extractedPath . '/worldcities.' . $format;

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return;
        }

        if ($format === 'csv') {
            $this->importCSV($filePath);
        } else {
            $this->importXLSX($filePath);
        }

        $this->info('World cities imported successfully.');
    }

    private function downloadZip()
    {
        $url = 'https://simplemaps.com/static/data/world-cities/basic/simplemaps_worldcities_basicv1.75.zip';
        $destinationPath = storage_path('app/worldcities.zip');

        try {
            $this->info('Downloading ZIP...');
            $response = Http::get($url);

            if ($response->failed()) {
                $this->error('Failed to download the file.');
                return null;
            }

            file_put_contents($destinationPath, $response->body());
            $this->info('ZIP file downloaded successfully.');

            return $destinationPath;
        } catch (\Exception $e) {
            $this->error('Error while downloading the file: ' . $e->getMessage());
            Log::error('Error downloading ZIP: ' . $e->getMessage());
            return null;
        }
    }

    private function unzipFile($zipPath)
    {
        $extractTo = storage_path('app/worldcities');

        if (!is_dir($extractTo)) {
            mkdir($extractTo, 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractTo);
            $zip->close();

            $this->info('ZIP file extracted successfully.');
            return $extractTo;
        } else {
            $this->error('Failed to extract ZIP file.');
            Log::error('Failed to extract ZIP file.');
            return null;
        }
    }

    private function importCSV($filePath)
    {
        $this->info('Processing CSV file...');

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Kopfzeile lesen

        $this->info('Loading countries from the database...');
        $countries = DB::table('wwde_countries')
            ->select('country_code', 'id', 'continent_id')
            ->get()
            ->keyBy('country_code'); // Assoziatives Array mit country_code als Schlüssel

        $this->info('Countries loaded into memory.');

        $count = 0;
        $batchSize = 100;
        $batchData = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                $data = array_combine($header, $row);

                // Überprüfen, ob der Ländercode in der Liste ist
                if (!isset($countries[$data['iso2']])) {
                    $this->info("Skipping city {$data['city']} as country code {$data['iso2']} is not in the database.");
                    continue;
                }

                $country = $countries[$data['iso2']];
                $population = is_numeric($data['population']) ? (int)$data['population'] : null;

                // Überspringe Städte ohne Flughafen und mit kleiner Bevölkerung
                if (empty($data['iata']) && $population < 100000) {
                    continue;
                }

                // Berechnung der Reisezeit
                $bestTravelTimeArray = $this->getBestTravelTimeByLatitude($data['lat']);
                //$panorama = $this->panorama_text_and_style($data['city'], 'spring', 'parks');
                $panorama = $this->panorama_text_and_style($data['city'], $bestTravelTimeArray, 'parks');

                $status = 'active'; // Standardstatus

                // Bilder abrufen und Status dynamisch anpassen
                $textPic1 = $this->getCityImage($data['city'], 1, $status);
                $textPic2 = $this->getCityImage($data['city'], 2, $status);
                $textPic3 = $this->getCityImage($data['city'], 3, $status);


                $batchData[] = [
                    'title' => $data['city'],
                    'country_id' => $country->id,
                    'continent_id' => $country->continent_id,
                    'alias' => Str::slug($data['city']),
                    'iata_code' => $data['iata'] ?? null,
                    'lat' => $data['lat'],
                    'lon' => $data['lng'],
                    'population' => $population,
                    'list_beach' => $this->isNearBeach($data['lat'], $data['lng']),
                    'list_citytravel' => $population > 1000000,
                    'list_sports' => $this->hasSportsActivities($data['city']),
                    'list_culture' => $this->isCulturalDestination($data['city']),
                    'text_short' => $this->generateShortText($data['city']),
                    'text_headline' => $this->generateHeadline($data['city']),

                    'text_pic1' => $textPic1,
                    'text_pic2' => $textPic2,
                    'text_pic3' => $textPic3,
                    'status' => $status, // Dynamisch gesetzter Status

                    'best_traveltime' => implode(' - ', [$bestTravelTimeArray[0], end($bestTravelTimeArray)]), // Kompakte Anzeige
                    'best_traveltime_json' => json_encode($bestTravelTimeArray), // JSON als Array
                    'panorama_text_and_style' => json_encode($panorama), // Kombinierter Text und Stil als JSON
                    'finished' => 1, // Fertiggestellt
                    'created_at' => now(), // Zum Erstellen erforderlich
                    'updated_at' => now(), // Zum Aktualisieren erforderlich
                ];

                // Batch-Insert, wenn die Batch-Größe erreicht ist
                if (count($batchData) >= $batchSize) {
                    $this->upsertLocations($batchData);
                    $batchData = [];
                }

                $count++;

                // Stop nach 10 Einträgen
                if ($count >= 100) {
                    $this->info('Test limit reached: 10 cities processed.');
                    break; // Entferne diesen Break, um alle Städte zu verarbeiten
                }
            }

            // Restliche Daten einfügen
            if (!empty($batchData)) {
                $this->upsertLocations($batchData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error importing CSV: ' . $e->getMessage());
            Log::error('Error importing CSV: ' . $e->getMessage());
        }

        fclose($file);
        $this->info("CSV data imported into the database. Total processed: {$count}");
    }

    private function importXLSX($filePath)
    {
        $this->info('Processing XLSX file...');

        // PhpSpreadsheet verwenden, um die XLSX-Datei zu lesen
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = array_shift($rows); // Erste Zeile als Header verwenden

        // Länderdaten vorab laden
        $this->info('Loading countries from the database...');
        $countries = DB::table('wwde_countries')
            ->select('country_code', 'id', 'continent_id')
            ->get()
            ->keyBy('country_code'); // Assoziatives Array mit country_code als Schlüssel

        $this->info('Countries loaded into memory.');

        $count = 0;
        $batchSize = 1000;
        $batchData = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $data = array_combine($header, $row);

                // Überprüfen, ob der Ländercode in der Liste ist
                if (!isset($countries[$data['iso2']])) {
                    $this->info("Skipping city {$data['city']} as country code {$data['iso2']} is not in the database.");
                    continue;
                }

                $country = $countries[$data['iso2']];
                $population = is_numeric($data['population']) ? (int)$data['population'] : null;

                // Berechnung der Reisezeit
                $bestTravelTimeArray = $this->getBestTravelTimeByLatitude($data['lat']);


                // Überspringe Städte ohne Flughafen und mit kleiner Bevölkerung
                if (empty($data['iata']) && $population < 100000) {
                    continue;
                }

                $batchData[] = [
                    'title' => $data['city'],
                    'country_id' => $country->id,
                    'continent_id' => $country->continent_id,
                    'alias' => Str::slug($data['city']),
                    'iata_code' => $data['iata'] ?? null,
                    'lat' => $data['lat'],
                    'lon' => $data['lng'],
                    'population' => $population,

                    'list_beach' => $this->isNearBeach($data['lat'], $data['lng']),
                    'list_citytravel' => $population > 1000000,
                    'list_sports' => $this->hasSportsActivities($data['city']),
                    'list_culture' => $this->isCulturalDestination($data['city']),
                    'text_short' => $this->generateShortText($data['city']),
                    'text_headline' => $this->generateHeadline($data['city']),
                    'best_traveltime' => $this->getBestTravelTime($data['lat'], $data['lng']),
                    'text_pic1' => $this->getCityImage($data['city'], 1),
                    'text_pic2' => $this->getCityImage($data['city'], 2),
                    'text_pic3' => $this->getCityImage($data['city'], 3),
                    'best_traveltime' => implode(' - ', [$bestTravelTimeArray[0], end($bestTravelTimeArray)]), // Kompakte Anzeige
                    'best_traveltime_json' => json_encode($bestTravelTimeArray), // JSON als Array
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Batch-Insert, wenn die Batch-Größe erreicht ist
                if (count($batchData) >= $batchSize) {
                    DB::table('wwde_locations')->upsert($batchData, ['title', 'country_id'], ['updated_at']);
                    $batchData = [];
                }

                $count++;

                // Stop nach 10 Einträgen
                if ($count >= 10) {
                    $this->info('Test limit reached: 10 cities processed.');
                    break; // Entferne diesen Break, um alle Städte zu verarbeiten
                }
            }

            // Restliche Daten einfügen
            if (!empty($batchData)) {
                DB::table('wwde_locations')->upsert($batchData, ['title', 'country_id'], ['updated_at']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error importing XLSX: ' . $e->getMessage());
            Log::error('Error importing XLSX: ' . $e->getMessage());
        }

        $this->info("XLSX data imported into the database. Total imported: {$count}");
    }

    private function upsertLocations(array $batchData)
    {
        foreach ($batchData as $data) {
            // Prüfen, ob der Eintrag existiert
            $exists = DB::table('wwde_locations')
                ->where('title', $data['title'])
                ->where('country_id', $data['country_id'])
                ->exists();

            if ($exists) {
                // Aktualisieren
                DB::table('wwde_locations')
                    ->where('title', $data['title'])
                    ->where('country_id', $data['country_id'])
                    ->update($data);
            } else {
                // Neu einfügen
                DB::table('wwde_locations')->insert($data);
            }
        }
    }

    private function panorama_text_and_style(string $location, array $bestTravelTimeArray, ?string $highlight = null): array
    {
        // Text basierend auf den Monaten der besten Reisezeit generieren
        $text = $this->generatePanoramaText($location, $bestTravelTimeArray, $highlight);

        // Stil basierend auf den Monaten generieren
        $style = $this->generatePanoramaStyle($bestTravelTimeArray);

        return [
            'text' => $text,
            'style' => $style,
        ];
    }

    private function generatePanoramaText(string $location, array $bestTravelTimeArray, ?string $highlight): string
    {
        $themes = config('panorama.texts');

        // Standardtext
        $baseText = "Discover the beauty of {$location}";
        $themeKey = $this->determineThemeFromMonths($bestTravelTimeArray);
        $themeText = $themes[$themeKey] ?? $themes['default'];

        $highlightText = $highlight ? ", featuring stunning {$highlight}" : '';

        return "{$baseText} {$themeText}{$highlightText}.";
    }

    private function generatePanoramaStyle(array $bestTravelTimeArray): array
    {
        $styles = config('panorama.styles');

        // Bestimme das Thema
        $themeKey = $this->determineThemeFromMonths($bestTravelTimeArray);

        return $styles[$themeKey] ?? $styles['default'];
    }

    private function determineThemeFromMonths(array $months): string
    {
        if (in_array('December', $months) || in_array('January', $months)) {
            return 'winter';
        } elseif (in_array('June', $months) || in_array('July', $months)) {
            return 'summer';
        } elseif (in_array('November', $months) || in_array('March', $months)) {
            return 'spring_autumn';
        } elseif (in_array('April', $months) || in_array('May', $months)) {
            return 'rainy';
        } elseif (in_array('October', $months)) {
            return 'night';
        } elseif (in_array('September', $months)) {
            return 'sunset';
        }

        return 'default';
    }

    // Hilfsmethoden (unverändert)
    private function isNearBeach($lat, $lon) { return false; }
    private function hasSportsActivities($city) { return in_array($city, ['Munich', 'Innsbruck']); }
    private function isCulturalDestination($city) { return in_array($city, ['Berlin', 'Paris', 'Rome']); }
    private function generateShortText($city) { return "{$city} is a wonderful destination to explore!"; }
    private function generateHeadline($city) { return "Explore the wonders of {$city}"; }
    private function getBestTravelTime($lat, $lon) { return 'May - September'; }

    private function getBestTravelTimeByLatitude($lat)
    {
        if ($lat >= -23.5 && $lat <= 23.5) {
            // Tropen: Trockenzeit
            return ['November', 'December', 'January', 'February', 'March'];
        } elseif (($lat > 23.5 && $lat < 66.5) || ($lat < -23.5 && $lat > -66.5)) {
            // Gemäßigt: Sommermonate
            return ['May', 'June', 'July', 'August', 'September'];
        } else {
            // Polargebiete: milde Monate
            return ['June', 'July', 'August'];
        }
    }

    private function getCityImage($city, $index, &$status)
    {
        // Cache-Schlüssel für das Bild
        $cacheKey = "city_image_{$city}_{$index}";

        // Überprüfen, ob das Bild bereits im Cache ist
        if (cache()->has($cacheKey)) {
            $this->info('Using cached image for city: ' . $city);
            return cache($cacheKey);
        }

        // Pixabay API-Schlüssel aus der Konfiguration holen
        $apiKey = config('services.pixabay.api_key', env('PIXABAY_API_KEY'));

        if (empty($apiKey)) {
            $this->error('Pixabay API key is missing.');
            Log::error('Pixabay API key is missing.');
            $status = 'inactive'; // Kein API-Key, also Status auf inactive
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }

        // Pixabay API-URL
        $url = 'https://pixabay.com/api/';

        try {
            // API-Anfrage senden
            $response = Http::get($url, [
                'key' => $apiKey,
                'q' => $city,
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'safesearch' => 'true',
                'per_page' => 5, // Maximal 5 Bilder abrufen
            ]);

            if ($response->successful()) {
                $images = $response->json()['hits'] ?? [];

                if (empty($images)) {
                    $this->error('No images found for city: ' . $city);
                    $status = 'pending'; // Keine Bilder gefunden, also Status auf pending
                    return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
                }

                // Überprüfen, ob ein Bild für den angegebenen Index vorhanden ist
                if (isset($images[$index - 1])) {
                    $imageUrl = $images[$index - 1]['webformatURL'];

                    // Bild-URL im Cache speichern (für 24 Stunden)
                    cache([$cacheKey => $imageUrl], now()->addDay());

                    $this->info('Image found and cached for city: ' . $city);
                    return $imageUrl;
                } else {
                    $this->error('Image index out of bounds for city: ' . $city);
                    $status = 'pending'; // Bild-Index außerhalb des Bereichs
                    return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
                }
            } else {
                $this->error('Failed to fetch images from Pixabay API: ' . $response->body());
                Log::error('Failed to fetch images from Pixabay API: ' . $response->body());
                $status = 'inactive'; // API-Fehler, Status auf inactive
                return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
            }
        } catch (\Exception $e) {
            $this->error('Error fetching images from Pixabay API: ' . $e->getMessage());
            Log::error('Error fetching images from Pixabay API: ' . $e->getMessage());
            $status = 'inactive'; // Ausnahme, Status auf inactive
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }
    }

}
