<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateClimateData extends Command
{
    protected $signature = 'climate:update';
    protected $description = 'Update climate data for all locations.';

    public function handle()
    {
        $locations = Location::all();
        $weatherClient = new WeatherApiClientLibrary();

        foreach ($locations as $location) {
            if ($location->lat && $location->lon) {
                $climateData = $weatherClient->getClimateData($location->lat, $location->lon);

                if (!isset($climateData['error'])) {
                    $location->update([
                        'text_location_climate' => json_encode($climateData)
                    ]);

                    $this->info("Updated climate data for location: {$location->title}");
                } else {
                    $this->error("Failed to update climate data for location: {$location->title}. Error: {$climateData['error']}");
                }
            }
        }

        return 0;
    }
}
