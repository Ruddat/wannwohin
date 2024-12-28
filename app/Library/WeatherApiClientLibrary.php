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

//
//dd($response->json());

        if ($response->successful()) {
            $airTemp = round(floatval($response->json('main.temp', 0)));

            return [
                'daily_temperature' => $airTemp,
                'night_temperature' => round(floatval($response->json('main.temp_min', 0))),
                'humidity' => $response->json('main.humidity', 0),
                'sunshine_per_day' => rand(5, 10), // Beispielwerte
                'rainy_days' => rand(0, 1), // Beispielwerte
                'weather' => $response->json('weather.0.description', ''), // Wetterbeschreibung
                'icon' => $response['weather'][0]['icon'] ?? null,
                'water_temperature' => $this->calculateWaterTemperature($airTemp), // Wassertemperatur schätzen
            ];
        }

        return null;
    }

    /**
     * Schätzt die Wassertemperatur basierend auf der Lufttemperatur.
     *
     * @param float $airTemp
     * @return float
     */
    private function calculateWaterTemperature($airTemp)
    {
        // Schätzung: Wassertemperatur ist im Schnitt 5-8°C kühler als die Luft
        $baseTemp = max($airTemp - rand(5, 8), 10); // Minimum 10°C
        return round($baseTemp, 1);
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
            'icon' => $weatherData['icon'] ?? null,
            'water_temperature' => $weatherData['water_temperature'] ?? null,
        ];
    }
}
