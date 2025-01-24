<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportClimateData extends Command
{
    protected $signature = 'climate:import {year : The year for which climate data should be fetched}';
    protected $description = 'Import NOAA climate data for a specific year and always update station_id in locations table';

    public function handle()
    {
        $year = $this->argument('year');

        if (!is_numeric($year) || strlen($year) != 4) {
            $this->error("Invalid year format. Please provide a valid year (e.g., 2023).");
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
            $this->info("Processing location: {$location->title} (ID: {$location->id})");

            try {
                // Fetch stations for the location (always update station_id)
                $response = Http::withHeaders([
                    'token' => $apiToken,
                ])->get("https://www.ncei.noaa.gov/cdo-web/api/v2/stations", [
                    'latitude' => $location->lat,
                    'longitude' => $location->lon,
                    'limit' => 1, // Only get the closest station
                ]);

                if ($response->failed()) {
                    $this->error("Failed to fetch stations for location: {$location->title}");
                    Log::error("NOAA API request failed for stations", [
                        'location' => $location->title,
                        'response' => $response->body(),
                    ]);
                    continue;
                }

                $stations = $response->json();

                if (!isset($stations['results']) || empty($stations['results'])) {
                    $this->warn("No stations found for location: {$location->title}");
                    continue;
                }

                $station = $stations['results'][0]; // Use the first station
                $stationId = $station['id'];

                // Always update station_id in the database
                DB::table('wwde_locations')->where('id', $location->id)->update([
                    'station_id' => $stationId,
                ]);

                $this->info("Updated station_id for location: {$location->title} (Station: $stationId)");

                // Fetch climate data for the updated station
                $response = Http::withHeaders([
                    'token' => $apiToken,
                ])->get("https://www.ncei.noaa.gov/cdo-web/api/v2/data", [
                    'datasetid' => 'GSOM',
                    'stationid' => $stationId,
                    'startdate' => $startDate,
                    'enddate' => $endDate,
                    'limit' => 1000,
                ]);

                if ($response->failed()) {
                    $this->error("Failed to fetch data for station: $stationId");
                    Log::error("NOAA API request failed for climate data", [
                        'station_id' => $stationId,
                        'response' => $response->body(),
                    ]);
                    continue;
                }

                $data = $response->json();

                if (!isset($data['results']) || empty($data['results'])) {
                    $this->warn("No climate data available for station: $stationId");
                    continue;
                }

                foreach ($data['results'] as $entry) {
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
                $this->info("Climate data imported for location: {$location->title}");
            } catch (\Exception $e) {
                $this->error("An error occurred while processing location: {$location->title}. Error: {$e->getMessage()}");
                Log::error("Exception during processing", [
                    'location' => $location->title,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Climate data import completed for all locations.");
    }
}
