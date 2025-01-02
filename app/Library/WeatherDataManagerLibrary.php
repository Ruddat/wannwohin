<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;
use App\Models\WwdeClimate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class WeatherDataManagerLibrary
{
    protected $apiMainUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiMainUrl = config('custom.weather.apiurl');
        $this->apiKey = config('custom.weather.appid');
    }

    /**
     * Holt die aktuellen Wetterdaten von der API und speichert oder aktualisiert sie in der Datenbank.
     * Nur wenn die vorhandenen Daten älter als 1 Stunde sind oder kein Eintrag vorhanden ist.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $location_id
     * @return array|null
     */
    public function fetchAndStoreWeatherData($latitude, $longitude, $location_id)
    {
        if (!$latitude || !$longitude || !$location_id) {
            return null;
        }

        // Cache-Schlüssel für die Location
        $cacheKey = "weather_data_{$location_id}";

        // Prüfen, ob die Daten im Cache vorhanden sind
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Prüfen, ob ein Eintrag für diese Location vorhanden und jünger als 1 Stunde ist
        $existingEntry = WwdeClimate::where('location_id', $location_id)
            ->where('updated_at', '>=', Carbon::now()->subHour())
            ->first();

        // Wenn ein aktueller Eintrag vorhanden ist, gib die vorhandenen Daten zurück
        if ($existingEntry) {
            $weatherData = [
                'main' => [
                    'temp' => $existingEntry->daily_temperature,
                    'temp_min' => $existingEntry->night_temperature,
                    'humidity' => $existingEntry->humidity,
                    'feels_like' => $existingEntry->feels_like,
                    'temp_max' => $existingEntry->temp_max,
                    'pressure' => $existingEntry->pressure,
                    'sea_level' => $existingEntry->sea_level,
                    'grnd_level' => $existingEntry->grnd_level,
                ],
                'weather' => [
                    [
                        'id' => $existingEntry->weather_id,
                        'main' => $existingEntry->weather_main,
                        'description' => $existingEntry->weather_description,
                        'icon' => $existingEntry->icon,
                    ],
                ],
                'visibility' => $existingEntry->visibility,
                'wind' => [
                    'speed' => $existingEntry->wind_speed,
                    'deg' => $existingEntry->wind_deg,
                ],
                'clouds' => [
                    'all' => $existingEntry->clouds_all,
                ],
                'dt' => $existingEntry->dt,
                'timezone' => $existingEntry->timezone,
                'sys' => [
                    'country' => $existingEntry->country,
                    'sunrise' => $existingEntry->sunrise,
                    'sunset' => $existingEntry->sunset,
                ],
                'water_temperature' => $existingEntry->water_temperature, // Wassertemperatur hinzufügen
                'sunshine_per_day' => $existingEntry->sunshine_per_day,   // Sonnenstunden hinzufügen
            ];

            // Daten im Cache speichern
            Cache::put($cacheKey, $weatherData, 3600); // 1 Stunde Cache

            return $weatherData;
        }

        // Wetterdaten von der API holen (nur wenn notwendig)
        $response = Http::get("{$this->apiMainUrl}weather", [
            'lat' => $latitude,
            'lon' => $longitude,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'de',
        ]);

        if ($response->successful()) {
            $weatherData = $response->json();

            // Sonnenstunden berechnen
            $sunshineHours = 'N/A';
            if (isset($weatherData['sys']['sunrise']) && isset($weatherData['sys']['sunset'])) {
                $sunrise = $weatherData['sys']['sunrise'];
                $sunset = $weatherData['sys']['sunset'];
                $daylightDuration = $sunset - $sunrise; // Differenz in Sekunden
                $sunshineHours = round($daylightDuration / 3600, 1); // In Stunden umrechnen
            }

            // Wassertemperatur berechnen
            $waterTemperature = $this->calculateWaterTemperature($weatherData['main']['temp'], $latitude);

            // Daten für die Datenbank vorbereiten
            $data = [
                'location_id' => $location_id,
                'month_id' => Carbon::now()->month, // Aktueller Monat als ID
                'month' => Carbon::now()->format('F'), // Name des aktuellen Monats
                'daily_temperature' => $weatherData['main']['temp'],
                'night_temperature' => $weatherData['main']['temp_min'],
                'humidity' => $weatherData['main']['humidity'],
                'sunshine_per_day' => $sunshineHours, // Berechnete Sonnenstunden
                'rainy_days' => rand(0, 1), // Beispielwert
                'water_temperature' => $waterTemperature, // Berechnete Wassertemperatur
                'icon' => "https://openweathermap.org/img/wn/{$weatherData['weather'][0]['icon']}@2x.png",
                'weather_id' => $weatherData['weather'][0]['id'] ?? null,
                'feels_like' => $weatherData['main']['feels_like'] ?? null,
                'temp_min' => $weatherData['main']['temp_min'] ?? null,
                'temp_max' => $weatherData['main']['temp_max'] ?? null,
                'pressure' => $weatherData['main']['pressure'] ?? null,
                'sea_level' => $weatherData['main']['sea_level'] ?? null,
                'grnd_level' => $weatherData['main']['grnd_level'] ?? null,
                'visibility' => $weatherData['visibility'] ?? null,
                'wind_speed' => $weatherData['wind']['speed'] ?? null,
                'wind_deg' => $weatherData['wind']['deg'] ?? null,
                'clouds_all' => $weatherData['clouds']['all'] ?? null,
                'dt' => $weatherData['dt'] ?? null,
                'timezone' => $weatherData['timezone'] ?? null,
                'country' => $weatherData['sys']['country'] ?? null,
                'sunrise' => $weatherData['sys']['sunrise'] ?? null,
                'sunset' => $weatherData['sys']['sunset'] ?? null,
                'weather_main' => $weatherData['weather'][0]['main'] ?? null,
                'weather_description' => $weatherData['weather'][0]['description'] ?? null,
            ];

            // Eintrag aktualisieren oder neu erstellen
            $existingEntry = WwdeClimate::where('location_id', $location_id)->first();
            if ($existingEntry) {
                $existingEntry->update($data);
            } else {
                WwdeClimate::create($data);
            }

            // Wassertemperatur und Sonnenstunden zur Rückgabe hinzufügen
            $weatherData['water_temperature'] = $waterTemperature; // Wassertemperatur hinzufügen
            $weatherData['sunshine_per_day'] = $sunshineHours;     // Sonnenstunden hinzufügen

            // Daten im Cache speichern
            Cache::put($cacheKey, $weatherData, 3600); // 1 Stunde Cache

            return $weatherData;
        }

        return null;
    }

    /**
     * Schätzt die Wassertemperatur basierend auf der Lufttemperatur.
     *
     * @param float $airTemp
     * @return float
     */
    private function calculateWaterTemperature($airTemp, $latitude)
    {
        // Differenz basierend auf Jahreszeit und Breitengrad
        $month = Carbon::now()->month;
        if ($latitude >= -30 && $latitude <= 30) { // Tropische Regionen
            $difference = rand(1, 3);
        } else { // Gemäßigte Zonen
            if ($month >= 6 && $month <= 8) { // Sommer
                $difference = rand(2, 4);
            } elseif ($month >= 12 || $month <= 2) { // Winter
                $difference = rand(4, 6);
            } else { // Frühling/Herbst
                $difference = rand(3, 5);
            }
        }

        // Wassertemperatur berechnen
        $waterTemp = $airTemp - $difference;

        // Extremwerte begrenzen
        $waterTemp = max($waterTemp, 0);  // Minimum 0°C
        $waterTemp = min($waterTemp, 30); // Maximum 30°C

        return round($waterTemp, 1);
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
        return $this->fetchAndStoreWeatherData($latitude, $longitude);
    }
}
