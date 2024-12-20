<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;

class WeatherApiClientLibrary
{
    protected $apiMainUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiMainUrl = config('custom.weather.apiurl');
        $this->apiKey = config('custom.weather.appid');
    }

    /**
     * Fetch the current weather for a given location.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function fetchCurrentWeather($latitude, $longitude)
    {
        if (!$latitude || !$longitude) {
            return null;
        }

        $response = Http::get("{$this->apiMainUrl}weather", [
            'lat' => $latitude,
            'lon' => $longitude,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'de',
        ]);

        if ($response->successful()) {
            return [
                'daily_temperature' => round(floatval($response->json('main.temp', 0))),
                'night_temperature' => round(floatval($response->json('main.temp_min', 0))),
                'humidity' => $response->json('main.humidity', 0),
                'sunshine_per_day' => rand(5, 10), // Beispielwerte
                'rainy_days' => rand(0, 1), // Beispielwerte
                'weather' => $response->json('weather.0.description', ''), // Wetterbeschreibung
            ];
        }

        return null;
    }

    /**
     * Backwards-compatible method to get current weather by time zone.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getCurrentWeatherByTimeZone($latitude, $longitude)
    {
        $weatherData = $this->fetchCurrentWeather($latitude, $longitude);

        return [
            'current_tmp' => $weatherData['daily_temperature'] ?? null,
            'weather' => $weatherData['weather'] ?? null,
        ];
    }
}
