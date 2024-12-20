<?php

namespace App\Services;

use App\Library\WeatherApiClientLibrary;

class WeatherService
{
    protected $weatherApiClient;

    public function __construct(WeatherApiClientLibrary $weatherApiClient)
    {
        $this->weatherApiClient = $weatherApiClient;
    }

    public function addWeatherToLocations($locations)
    {
        foreach ($locations as $location) {
            $weatherData = $this->weatherApiClient->getCurrentWeatherByTimeZone($location->lat_new, $location->lon_new);
            $location->current_temp_from_api = $weatherData['current_tmp'] ?? null;
            $location->current_weather_from_api = $weatherData['weather'] ?? null;
        }

        return $locations;
    }
}
