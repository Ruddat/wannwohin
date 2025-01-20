<?php

namespace App\Console\Commands;

use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use App\Library\WeatherApiClientLibrary;

class UpdateClimateData extends Command
{
    protected $signature = 'climate:update';
    protected $description = 'Update climate data for all locations.';

    public function handle()
    {
        $locations = WwdeLocation::all();
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
