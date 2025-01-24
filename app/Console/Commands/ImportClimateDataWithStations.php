<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportClimateDataWithStations extends Command
{
    protected $signature = 'climate:import-station {year : The year for which climate data should be fetched}';

    protected $description = 'Update station IDs for locations and import NOAA climate data';

    public function handle()
    {
        $year = $this->argument('year');

        if (!is_numeric($year) || strlen($year) != 4) {
            $this->error("Invalid year format. Please provide a valid year (e.g., 2023).\n");
            return;
        }

        $startDate = "$year-01-01";
        $endDate = "$year-12-31";
        $apiToken = 'TLSrRdpnWglkEWOqaxkInLcmyokcpmWd';

        $locations = DB::table('wwde_locations')->get();

        if ($locations->isEmpty()) {
            $this->error("No locations found in the database.");
            return;
        }

        foreach ($locations as $location) {
            // Update station_id if not already set
            if (!$location->station_id) {
                $this->info("Fetching station for location: {$location->title} (ID: {$location->id})");

                try {
                    $stationResponse = Http::withHeaders([
                        'token' => $apiToken,
                    ])->get("https://www.ncei.noaa.gov/cdo-web/api/v2/stations", [
                        'latitude' => $location->lat,
                        'longitude' => $location->lon,
                        'limit' => 1,
                    ]);

                    if ($stationResponse->failed() || empty($stationResponse->json('results'))) {
                        $this->warn("No station found for location: {$location->title}");
                        continue;
                    }

                    $stationId = $stationResponse->json('results')[0]['id'];

                    DB::table('wwde_locations')->where('id', $location->id)->update([
                        'station_id' => $stationId,
                    ]);

                    $this->info("Updated station ID: {$stationId} for location: {$location->title}");
                } catch (\Exception $e) {
                    $this->error("Error fetching station for location: {$location->title}. {$e->getMessage()}");
                    continue;
                }
            }

            // Fetch climate data for the location using its station_id
            $stationId = $location->station_id;

            $this->info("Fetching climate data for location: {$location->title} (Station ID: {$stationId})");

            try {
                $climateResponse = Http::withHeaders([
                    'token' => $apiToken,
                ])->get("https://www.ncei.noaa.gov/cdo-web/api/v2/data", [
                    'datasetid' => 'GSOM',
                    'stationid' => $stationId,
                    'startdate' => $startDate,
                    'enddate' => $endDate,
                    'limit' => 1000,
                ]);

                if ($climateResponse->failed() || !isset($climateResponse->json()['results'])) {
                    $this->warn("No data available for location: {$location->title}");
                    continue;
                }

                foreach ($climateResponse->json()['results'] as $entry) {
                    DB::table('climate_monthly_data')->insert([
                        'location_id' => $location->id,
                        'datatype' => $entry['datatype'],
                        'value' => $entry['value'],
                        'date' => $entry['date'],
                        'station' => $entry['station'],
                        'attributes' => $entry['attributes'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $this->info("Climate data imported successfully for location: {$location->title}");
            } catch (\Exception $e) {
                $this->error("Error fetching climate data for location: {$location->title}. {$e->getMessage()}");
            }
        }

        $this->info("Climate data import completed for all locations.");
    }
}
