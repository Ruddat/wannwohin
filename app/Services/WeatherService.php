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
        if (is_null($locations) || count($locations) === 0) {
            dd('No locations passed to WeatherService');
        }

        foreach ($locations as $location) {
            // Wetterdaten abrufen
            $weatherData = $this->weatherApiClient->getCurrentWeatherByTimeZone($location->lat_new, $location->lon_new);
            $location->current_temp_from_api = $weatherData['current_tmp'] ?? null;
            $location->current_weather_from_api = $weatherData['weather'] ?? null;

            // Country-Flag hinzufügen
            if ($location->country) {
                $location->country_flag = "https://flagcdn.com/w40/" . strtolower($location->country->country_code) . ".png";
            }

//dd($weatherData);



        // Wetter-Icon hinzufügen
        if (isset($weatherData['icon'])) {
            $location->weather_icon = "https://openweathermap.org/img/wn/{$weatherData['icon']}@2x.png";

  //     dd($location->weather_icon);
        } else {
            $location->weather_icon = "https://openweathermap.org/img/wn/01d@2x.png"; // Standard-Icon

    //        dd($location->weather_icon);
        }

        }

        return $locations;
    }
}
