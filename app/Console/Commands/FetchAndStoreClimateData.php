<?php

namespace App\Console\Commands;

use App\Library\WeatherApiClientLibrary;
use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;

class FetchAndStoreClimateData extends Command
{
    protected $signature = 'climate:fetch';
    protected $description = 'Fetch weather data for locations and store it in the climate table';

    public function handle()
    {
        $weatherClient = new WeatherApiClientLibrary();
        $locations = WwdeLocation::whereNotNull('lat')->whereNotNull('lon')->get();

        foreach ($locations as $location) {
            $this->info("Fetching data for location: {$location->title}");

            $weatherData = $weatherClient->fetchDailyWeather($location->lat, $location->lon);

            if ($weatherData) {
                WwdeClimate::create([
                    'location_id' => $location->id,
                    'month_id' => now()->month,
                    'month' => now()->format('F'),
                    'daily_temperature' => $weatherData['daily_temperature'],
                    'night_temperature' => $weatherData['night_temperature'],
                    'sunshine_per_day' => $weatherData['sunshine_per_day'],
                    'humidity' => $weatherData['humidity'],
                    'rainy_days' => $weatherData['rainy_days'],
                    'water_temperature' => null, // API bietet dies eventuell nicht an
                ]);

                $this->info("Climate data stored for location: {$location->title}");
            } else {
                $this->error("Failed to fetch data for location: {$location->title}");
            }
        }

        return 0;
    }
}
