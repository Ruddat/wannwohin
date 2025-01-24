<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DownloadAndAssignWeatherStations extends Command
{
    protected $signature = 'weather:assign-stations';
    protected $description = 'Download weather stations, import them into the database, and find the nearest station for each location.';

    public function handle()
    {
        $this->info('Step 1: Downloading weather station data...');
        $downloaded = $this->downloadWeatherStations();

        if (!$downloaded) {
            $this->error('Failed to download or extract weather station data.');
            return;
        }

        $this->info('Step 2: Importing weather station data into the database...');
        $imported = $this->importWeatherStations();

        if (!$imported) {
            $this->error('Failed to import weather station data.');
            return;
        }

        $this->info('Step 3: Assigning the nearest station to each location...');
        $this->assignStationsToLocations();

        $this->info('All steps completed successfully!');
    }

    private function downloadWeatherStations()
    {
        $url = 'https://bulk.meteostat.net/v2/stations/lite.json.gz'; // oder full.json.gz
        $gzPath = storage_path('app/weather_stations.json.gz');
        $jsonPath = storage_path('app/weather_stations.json');

        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                $this->error('Failed to download weather station data.');
                return false;
            }

            file_put_contents($gzPath, $response->body());

            $this->info('Extracting the GZ file...');
            $gzip = gzopen($gzPath, 'rb');
            $jsonFile = fopen($jsonPath, 'wb');

            while (!gzeof($gzip)) {
                fwrite($jsonFile, gzread($gzip, 4096));
            }

            gzclose($gzip);
            fclose($jsonFile);

            $this->info("Weather station data extracted to: $jsonPath");
            return true;
        } catch (\Exception $e) {
            $this->error('An error occurred while downloading or extracting data: ' . $e->getMessage());
            return false;
        }
    }

    private function importWeatherStations()
    {
        $jsonPath = storage_path('app/weather_stations.json');

        if (!file_exists($jsonPath)) {
            $this->error("File not found at: $jsonPath");
            return false;
        }

        try {
            $this->info('Reading weather station data...');
            $stations = json_decode(file_get_contents($jsonPath), true);

            if (!$stations) {
                $this->error('Failed to decode weather station data.');
                return false;
            }

            $this->info('Inserting weather station data into the database...');
            foreach ($stations as $station) {
                DB::table('weather_stations')->updateOrInsert(
                    ['station_id' => $station['id']],
                    [
                        'name' => $station['name']['en'] ?? null,
                        'country' => $station['country'],
                        'region' => $station['region'] ?? null,
                        'latitude' => $station['location']['latitude'],
                        'longitude' => $station['location']['longitude'],
                        'elevation' => $station['location']['elevation'] ?? null,
                        'timezone' => $station['timezone'] ?? null,
                        'inventory' => json_encode($station['inventory'] ?? []),
                    ]
                );
            }

            $this->info('Weather station data imported successfully.');
            return true;
        } catch (\Exception $e) {
            $this->error('An error occurred while importing data: ' . $e->getMessage());
            return false;
        }
    }

    private function assignStationsToLocations()
    {
        $locations = DB::table('wwde_locations')->get();

        if ($locations->isEmpty()) {
            $this->error('No locations found in the database.');
            return;
        }

        foreach ($locations as $location) {
            $this->info("Processing location: {$location->title} (ID: {$location->id})");

            $nearestStation = DB::table('weather_stations')
                ->selectRaw("
                    station_id, name,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )) AS distance
                ", [$location->lat, $location->lon, $location->lat])
                ->orderBy('distance')
                ->first();

            if ($nearestStation) {
                DB::table('wwde_locations')->where('id', $location->id)->update([
                    'station_id' => $nearestStation->station_id,
                ]);

                $this->info("Assigned station: {$nearestStation->name} (ID: {$nearestStation->station_id}) to location: {$location->title}");
            } else {
                $this->warn("No nearby weather station found for location: {$location->title}");
            }
        }
    }
}
