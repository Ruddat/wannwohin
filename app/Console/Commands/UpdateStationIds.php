<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateStationIds extends Command
{
    protected $signature = 'locations:update-stations';
    protected $description = 'Update station IDs for all locations';

    public function handle()
    {
        $apiToken = 'TLSrRdpnWglkEWOqaxkInLcmyokcpmWd';
        $locations = DB::table('wwde_locations')->whereNull('station_id')->get();

        if ($locations->isEmpty()) {
            $this->info("All locations already have station IDs.");
            return;
        }

        foreach ($locations as $location) {
            $this->info("Fetching station for location: {$location->title} (ID: {$location->id})");

            try {
                $response = Http::withHeaders([
                    'token' => $apiToken,
                ])->get("https://www.ncei.noaa.gov/cdo-web/api/v2/stations", [
                    'latitude' => $location->lat,
                    'longitude' => $location->lon,
                    'limit' => 1, // Hol nur die nächste Station
                ]);

                if ($response->failed()) {
                    $this->warn("Failed to fetch station for location: {$location->title}");
                    continue;
                }

                $data = $response->json();

                if (isset($data['results'][0]['id'])) {
                    $stationId = $data['results'][0]['id'];

                    DB::table('wwde_locations')->where('id', $location->id)->update([
                        'station_id' => $stationId,
                    ]);

                    $this->info("Station ID {$stationId} updated for location: {$location->title}");
                } else {
                    $this->warn("No station found for location: {$location->title}");
                }
            } catch (\Exception $e) {
                $this->error("Error fetching station for location: {$location->title}. {$e->getMessage()}");
            }
        }

        $this->info("Station IDs updated for all locations.");
    }
}
