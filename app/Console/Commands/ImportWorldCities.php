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

        // Offset-Handling
        $offset = DB::table('job_offsets')->where('job_name', 'import_world_cities')->value('offset') ?? 0;
        $batchSize = 50;

        if ($format === 'csv') {
            $this->importCSV($filePath, $offset, $batchSize);
        } else {
            $this->importXLSX($filePath, $offset, $batchSize);
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

    private function importCSV($filePath, $offset, $batchSize)
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

        $continents = DB::table('wwde_continents')
        ->select('id', 'title')
        ->get()
        ->keyBy('id'); // Assoziatives Array mit continent_id als Schlüssel

        $this->info('Continents loaded into memory.');


        $currentOffset = 0;
        $batchData = [];

        while (($row = fgetcsv($file)) !== false) {
            // Überspringe bis zum aktuellen Offset
            if ($currentOffset < $offset) {
                $currentOffset++;
                continue;
            }

            $data = array_combine($header, $row);

            if (!isset($countries[$data['iso2']])) {
                $this->info("Skipping city {$data['city']} as country code {$data['iso2']} is not in the database.");
                continue;
            }

            $country = $countries[$data['iso2']];
            $continentName = $continents[$country->continent_id]->title ?? 'Unbekannter Kontinent';

           //dd($country, $continentName);
            $population = is_numeric($data['population']) ? (int)$data['population'] : null;

            if (empty($data['iata']) && $population < 100000) {
                continue;
            }

            // Berechnung der Reisezeit
            $bestTravelTimeArray = $this->getBestTravelTimeByLatitude($data['lat']);
            $panorama = $this->panorama_text_and_style($data['city'], $bestTravelTimeArray, 'parks');

            $calculatedTimeZone = $this->calculateTimeZone($data['lat'], $data['lng']);


            $status = 'active';

            // Bilder abrufen
            $textPic1 = $this->getCityImage($data['city'], 1, $status);
            $textPic2 = $this->getCityImage($data['city'], 2, $status);
            $textPic3 = $this->getCityImage($data['city'], 3, $status);

            $flags = $this->determineFlags($data);

            $batchData[] = [
                'title' => $data['city'],
                'country_id' => $country->id,
                'continent_id' => $country->continent_id,
                'alias' => Str::slug($data['city']),
                'iata_code' => $data['iata'] ?? null,
                'lat' => $data['lat'],
                'lon' => $data['lng'],
                'iso2' => $data['iso2'],
                'iso3' => $data['iso3'],
                
                'population' => $population,
                'list_beach' => $this->isNearBeach($data['lat'], $data['lng']),
                'list_citytravel' => $population > 1000000,
                'list_sports' => $this->hasSportsActivities($data['city']),
                'list_culture' => $this->isCulturalDestination($data['city']),
                //'text_short' => $this->generateShortText($data['city']),
                'text_pic1' => $textPic1,
                'text_pic2' => $textPic2,
                'text_pic3' => $textPic3,
                'status' => $status,
                'time_zone' => $calculatedTimeZone,
                'best_traveltime' => implode(' - ', [$bestTravelTimeArray[0], end($bestTravelTimeArray)]),
                'best_traveltime_json' => json_encode($bestTravelTimeArray),
                'panorama_text_and_style' => json_encode($panorama),

                'text_headline' => $this->generateHeadline(
                    $data['city'],
                    $this->determineFlags($data),
                    $population,
                    $continentName
                 //   $countries[$data['iso2']]->continent_id ?? 'Unbekannter Kontinent'
                ),

                'text_what_to_do' => $this->generateWhatToDoText(
                    $data['city'],
                    $flags,
                    $population,
                    $continentName
                    //$country->continent_id // Falls Kontinent-Name direkt verfügbar ist
                ),

                'text_short' => $this->generateShortText(
                    $data['city'],
                    $flags,
                    $population,
                    $continentName
                    //$countries[$data['iso2']]->continent_id ?? 'Unbekannter Kontinent'
                ),

                'text_amusement_parks' => $this->generateAmusementParksText($data['city'], $data['lat'], $data['lng']),

                'finished' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $currentOffset++;

            if (count($batchData) >= $batchSize) {
                $this->upsertLocations($batchData);
                $batchData = [];

                DB::table('job_offsets')->updateOrInsert(
                    ['job_name' => 'import_world_cities'],
                    ['offset' => $currentOffset]
                );

                $this->info("Processed batch of {$batchSize} entries. Offset is now {$currentOffset}.");
                break; // Batchgröße erreicht, Schleife verlassen
            }
        }

        if (!empty($batchData)) {
            $this->upsertLocations($batchData);

            DB::table('job_offsets')->updateOrInsert(
                ['job_name' => 'import_world_cities'],
                ['offset' => $currentOffset]
            );
        }

        fclose($file);
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
                //dd($country);

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
                    'best_traveltime' => $this->getBestTravelTime($data['lat'], $data['lng']),
                    'text_pic1' => $this->getCityImage($data['city'], 1),
                    'text_pic2' => $this->getCityImage($data['city'], 2),
                    'text_pic3' => $this->getCityImage($data['city'], 3),
                    'best_traveltime' => implode(' - ', [$bestTravelTimeArray[0], end($bestTravelTimeArray)]), // Kompakte Anzeige
                    'best_traveltime_json' => json_encode($bestTravelTimeArray), // JSON als Array

                    //'panorama_text_and_style' => json_encode($this->panorama_text_and_style($data['city'], $bestTravelTimeArray, 'parks')),
                    'text_what_to_do' => $this->generateWhatToDoText(
                        $data['city'],
                        $flags,
                        $population,
                        $country->continent_id // Falls Kontinent-Name direkt verfügbar ist
                    ),

                    'text_headline' => $this->generateHeadline(
                        $data['city'],
                        $flags,
                        $population,
                        $countries[$data['iso2']]->continent_id ?? 'Unbekannter Kontinent'
                    ),

                    'text_amusement_parks' => $this->generateAmusementParksText($data['city'], $data['lat'], $data['lng']),

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
                if ($count >= 50) {
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
        try {
            DB::table('wwde_locations')->upsert($batchData, ['title', 'country_id'], ['updated_at']);
        } catch (\Exception $e) {
            Log::error('Error during upsert: ' . $e->getMessage());
            throw $e;
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
   // private function isNearBeach($lat, $lon) { return false; }
   // private function hasSportsActivities($city) { return in_array($city, ['Munich', 'Innsbruck']); }
  //  private function isCulturalDestination($city) { return in_array($city, ['Berlin', 'Paris', 'Rome']); }
  //  private function generateShortText($city) { return "{$city} is a wonderful destination to explore!"; }
    // private function getBestTravelTime($lat, $lon) { return 'May - September'; }



    private function generateAmusementParksText($city, $latitude, $longitude)
    {
        $parks = $this->findNearbyAmusementParks($latitude, $longitude);

        if ($parks->isEmpty()) {
            return "<p>Leider gibt es keine Freizeitparks in der Nähe von {$city}. Vielleicht möchten Sie einen Ausflug in eine andere Region unternehmen!</p>";
        }

        $text = "<h3>Freizeitparks in der Nähe von {$city}</h3><ul>";

        foreach ($parks as $park) {
            $text .= "<li><strong>{$park->name}</strong> in {$park->country}</li>";
        }

        $text .= "</ul>";
        return $text;
    }

    public function findNearbyAmusementParks($latitude, $longitude)
    {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException("Ungültige Koordinaten: ({$latitude}, {$longitude})");
        }

        $parks = DB::table('amusement_parks')
            ->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', 500) // Erhöhe den Radius
            ->orderBy('distance', 'asc')
            ->get();

        if ($parks->isEmpty()) {
            Log::warning("Keine Freizeitparks gefunden für ({$latitude}, {$longitude})");
        }

        return $parks;
    }

    private function getBestTravelTime($lat, $lon)
    {
        // Tropische Gebiete (nahe Äquator)
        if ($lat >= -23.5 && $lat <= 23.5) {
            return 'November - March'; // Trockenzeit
        }

        // Gemäßigte Zonen
        if (($lat > 23.5 && $lat < 66.5) || ($lat < -23.5 && $lat > -66.5)) {
            return 'May - September'; // Sommer in den nördlichen Breitengraden
        }

        // Polargebiete
        if ($lat >= 66.5 || $lat <= -66.5) {
            return 'June - August'; // Milde Monate
        }

        // Fallback, falls keine spezifische Region erkannt wird
        return 'All year round'; // Standardwert
    }


    private function determineFlags(array $data): array
    {
        $flags = [
            'list_beach' => $this->isNearBeach($data['lat'], $data['lng']),
            'list_citytravel' => $data['population'] > 1000000,
            'list_sports' => $this->hasSportsActivities($data['city']),
            'list_island' => $this->isOnIsland($data['city']),
            'list_culture' => $this->isCulturalDestination($data['city']),
            'list_nature' => $this->isNatureDestination($data['lat'], $data['lng']),
            'list_watersport' => $this->isWatersportDestination($data['lat'], $data['lng']),
            'list_wintersport' => $this->isWintersportDestination($data['lat']),
            'list_mountainsport' => $this->isMountainDestination($data['lat'], $data['lng']),
            'list_biking' => $this->isBikingFriendly($data['city']),
            'list_fishing' => $this->isFishingDestination($data['lat'], $data['lng']),
            'list_amusement_park' => $this->hasAmusementPark($data['city']),
            'list_water_park' => $this->hasWaterPark($data['city']),
            'list_animal_park' => $this->hasAnimalPark($data['city']),
        ];

        return $flags;
    }


    private function isNearBeach($lat, $lon)
    {
        // Dummy-Koordinaten für Küstenregionen
        $coastalRegions = [
            ['minLat' => -10, 'maxLat' => 10, 'minLon' => -180, 'maxLon' => -170], // Beispielregion
            ['minLat' => 30, 'maxLat' => 40, 'minLon' => 120, 'maxLon' => 130], // Beispielregion
        ];

        foreach ($coastalRegions as $region) {
            if (
                $lat >= $region['minLat'] &&
                $lat <= $region['maxLat'] &&
                $lon >= $region['minLon'] &&
                $lon <= $region['maxLon']
            ) {
                return true;
            }
        }

        return false;
    }

    private function isOnIsland($city)
    {
        $islandCities = ['Honolulu', 'Male', 'Reykjavik', 'Ibiza', 'Palma de Mallorca'];
        return in_array($city, $islandCities);
    }

    private function hasSportsActivities($city)
    {
        $sportsCities = ['Munich', 'Rio de Janeiro', 'Melbourne', 'Barcelona', 'London'];
        return in_array($city, $sportsCities);
    }

    private function isCulturalDestination($city)
    {
        $culturalCities = ['Berlin', 'Paris', 'Rome', 'Kyoto', 'Istanbul'];
        return in_array($city, $culturalCities);
    }


    private function isNatureDestination($lat, $lon)
    {
        // Koordinatenbereiche für Naturregionen
        $natureRegions = [
            ['minLat' => -5, 'maxLat' => 5, 'minLon' => 100, 'maxLon' => 110], // Tropische Wälder in Südostasien
            ['minLat' => -20, 'maxLat' => -10, 'minLon' => 120, 'maxLon' => 130], // Australisches Outback
            ['minLat' => 50, 'maxLat' => 60, 'minLon' => -10, 'maxLon' => 10], // Skandinavische Natur
            ['minLat' => -10, 'maxLat' => 0, 'minLon' => -70, 'maxLon' => -60], // Amazonasgebiet
            ['minLat' => 30, 'maxLat' => 40, 'minLon' => -120, 'maxLon' => -110], // Yosemite und westamerikanische Natur
        ];

        foreach ($natureRegions as $region) {
            if (
                $lat >= $region['minLat'] &&
                $lat <= $region['maxLat'] &&
                $lon >= $region['minLon'] &&
                $lon <= $region['maxLon']
            ) {
                return true;
            }
        }

        return false;
    }

    private function isWatersportDestination($lat, $lon)
    {
        // Beispielhafte Koordinatenbereiche für Wassersport
        $watersportRegions = [
            ['minLat' => -30, 'maxLat' => -10, 'minLon' => 150, 'maxLon' => 180], // Südlicher Pazifik
            ['minLat' => 20, 'maxLat' => 30, 'minLon' => -90, 'maxLon' => -80], // Karibik
        ];

        foreach ($watersportRegions as $region) {
            if (
                $lat >= $region['minLat'] &&
                $lat <= $region['maxLat'] &&
                $lon >= $region['minLon'] &&
                $lon <= $region['maxLon']
            ) {
                return true;
            }
        }

        return false;
    }

    private function isWintersportDestination($lat)
    {
        // Regionen basierend auf Breitengrad für Wintersport
        return $lat > 45 || $lat < -45; // Beispiel: Regionen in höheren Breitengraden
    }

    private function isMountainDestination($lat, $lon)
    {
        // Koordinatenbereiche für Berge
        $mountainRegions = [
            ['minLat' => 30, 'maxLat' => 50, 'minLon' => -110, 'maxLon' => -100], // Rocky Mountains
            ['minLat' => 40, 'maxLat' => 50, 'minLon' => 5, 'maxLon' => 15], // Alpen
        ];

        foreach ($mountainRegions as $region) {
            if (
                $lat >= $region['minLat'] &&
                $lat <= $region['maxLat'] &&
                $lon >= $region['minLon'] &&
                $lon <= $region['maxLon']
            ) {
                return true;
            }
        }

        return false;
    }

    private function isBikingFriendly($city)
    {
        $bikingCities = ['Amsterdam', 'Copenhagen', 'Portland', 'Berlin', 'Barcelona'];
        return in_array($city, $bikingCities);
    }

    private function isFishingDestination($lat, $lon)
    {
        // Koordinatenbereiche für beliebte Fischereiregionen
        $fishingRegions = [
            ['minLat' => 10, 'maxLat' => 20, 'minLon' => 100, 'maxLon' => 110], // Südostasien
            ['minLat' => 40, 'maxLat' => 50, 'minLon' => -10, 'maxLon' => 0], // Nordsee
        ];

        foreach ($fishingRegions as $region) {
            if (
                $lat >= $region['minLat'] &&
                $lat <= $region['maxLat'] &&
                $lon >= $region['minLon'] &&
                $lon <= $region['maxLon']
            ) {
                return true;
            }
        }

        return false;
    }

    private function hasAmusementPark($city)
    {
        $amusementParkCities = ['Orlando', 'Los Angeles', 'Tokyo', 'Paris'];
        return in_array($city, $amusementParkCities);
    }

    private function hasWaterPark($city)
    {
        $waterParkCities = ['Orlando', 'Dubai', 'Singapore'];
        return in_array($city, $waterParkCities);
    }

    private function hasAnimalPark($city)
    {
        $animalParkCities = ['San Diego', 'Berlin', 'Singapore'];
        return in_array($city, $animalParkCities);
    }



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


    private function calculateTimeZone(float $latitude, float $longitude): string
    {
        // Berechnung des GMT-Offsets
        $gmtOffset = round($longitude / 15);

        // Handling für GMT+-Zonen
        if ($gmtOffset > 0) {
            $timeZone = "GMT+{$gmtOffset}";
        } elseif ($gmtOffset < 0) {
            $timeZone = "GMT{$gmtOffset}";
        } else {
            $timeZone = "GMT+0";
        }

        // Optional: Sommerzeit oder Spezialfälle ergänzen
        // Beispiel: Falls Breitengrad spezifische Anpassungen benötigt
        if ($latitude > 66.5 || $latitude < -66.5) {
            // Arktische/Antarktische Regionen - keine präzisen Regeln
            $timeZone .= " (Polar Region)";
        }

        return $timeZone;
    }

    /// Texte generiert
    private function generateWhatToDoText($city, $flags, $population, $continent)
    {
        // Einführung
        $intro = "<h2>Entdecken Sie {$city}</h2>";
        $intro .= "<p>{$city} ist ein faszinierendes Reiseziel, das für jeden etwas zu bieten hat. Von malerischen Landschaften bis hin zu pulsierenden Stadtzentren – hier finden Sie alles, was Ihr Herz begehrt.</p>";

        // Aktivitäten basierend auf Flags
        $activities = [];
        if ($flags['list_beach']) {
            $activities[] = "<li>Genießen Sie die <strong>sonnigen Strände</strong> von {$city} und entspannen Sie sich beim Rauschen der Wellen.</li>";
        }
        if ($flags['list_culture']) {
            $activities[] = "<li>Entdecken Sie die <strong>kulturellen Highlights</strong> von {$city}, darunter historische Stätten, Museen und Festivals.</li>";
        }
        if ($flags['list_sports']) {
            $activities[] = "<li>Erleben Sie <strong>sportliche Abenteuer</strong> wie Radfahren, Wandern oder Wassersport in und um {$city}.</li>";
        }
        if ($flags['list_island']) {
            $activities[] = "<li>Die idyllische <strong>Inselstadt {$city}</strong> lädt Sie ein, ihre atemberaubenden Landschaften zu erkunden.</li>";
        }
        if ($flags['list_nature']) {
            $activities[] = "<li>Tauchen Sie ein in die <strong>unberührte Natur</strong> rund um {$city}, ideal für Wanderungen und Naturliebhaber.</li>";
        }

        // Standardaktivität, falls keine Flags gesetzt sind
        if (empty($activities)) {
            $activities[] = "<li>{$city} bietet eine Vielzahl von Möglichkeiten für Abenteuer, Entspannung oder kulturelle Erlebnisse.</li>";
        }

        // Aktivitätenliste
        $activitiesList = "<h3>Was können Sie in {$city} unternehmen?</h3>";
        $activitiesList .= "<ul>" . implode('', $activities) . "</ul>";

        // Zusätzliche Informationen basierend auf Bevölkerung und Kontinent
        $additionalInfo = $population > 1000000
            ? "<p>{$city} ist eine pulsierende Metropole mit über einer Million Einwohnern, die Ihnen zahlreiche Möglichkeiten bietet, die Stadt zu entdecken.</p>"
            : "<p>{$city} ist eine charmante Stadt mit einer einzigartigen Atmosphäre, die zum Erkunden einlädt.</p>";
        $additionalInfo .= "<p>Gelegen in <strong>{$continent}</strong>, verbindet {$city} Tradition und Moderne auf faszinierende Weise.</p>";

        // Gesamter Text
        return $intro . $activitiesList . $additionalInfo;
    }

    private function generateHeadline($city, $flags, $population, $continent)
    {
        // Basistexte für Überschriften
        $baseHeadlines = [
            'default' => "Entdecken Sie die faszinierenden Seiten von {$city}",
            'beach' => "{$city}: Paradies für Strandliebhaber",
            'culture' => "Tauchen Sie ein in die kulturellen Schätze von {$city}",
            'sports' => "Sport und Abenteuer erwarten Sie in {$city}",
            'island' => "Erleben Sie die idyllische Inselstadt {$city}",
            'nature' => "Natur pur: Erkunden Sie die Landschaften von {$city}",
            'metropolis' => "Pulsierendes Leben in der Metropole {$city}",
        ];

        // Individuelle Überschrift basierend auf Flags und Kontext
        if ($flags['list_beach']) {
            return $baseHeadlines['beach'];
        } elseif ($flags['list_culture']) {
            return $baseHeadlines['culture'];
        } elseif ($flags['list_sports']) {
            return $baseHeadlines['sports'];
        } elseif ($flags['list_island']) {
            return $baseHeadlines['island'];
        } elseif ($flags['list_nature']) {
            return $baseHeadlines['nature'];
        }

        // Wenn die Stadt eine Metropole ist
        if ($population > 1000000) {
            return $baseHeadlines['metropolis'];
        }

        // Standardüberschrift
        return $baseHeadlines['default'];
    }


    private function generateShortText($city, $flags, $population, $continent)
    {
        // Basisinformationen
        $text = "{$city} ist ein faszinierendes Reiseziel";

        // Zusätzliche Informationen basierend auf Flags
        if ($flags['list_beach']) {
            $text .= ", das für seine wunderschönen Strände bekannt ist";
        } elseif ($flags['list_culture']) {
            $text .= ", das reich an kulturellen Sehenswürdigkeiten ist";
        } elseif ($flags['list_sports']) {
            $text .= ", das Sport- und Abenteuerliebhaber begeistert";
        } elseif ($flags['list_island']) {
            $text .= ", das als idyllische Insel bekannt ist";
        } elseif ($flags['list_nature']) {
            $text .= ", das von atemberaubender Natur umgeben ist";
        }

        // Bevölkerung hinzufügen
        $text .= $population > 1000000
            ? ". Mit einer Bevölkerung von über einer Million ist {$city} eine lebendige Metropole"
            : ". Diese charmante Stadt bietet eine ruhige und einladende Atmosphäre";

        // Kontinent hinzufügen
        $text .= " und liegt auf dem Kontinent {$continent}.";

        // Rückgabe des gekürzten Texts (maximal 1000 Zeichen)
        return Str::limit($text, 1000, '...');
    }



}
