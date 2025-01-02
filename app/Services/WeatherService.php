<?php

namespace App\Services;

use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Library\WeatherApiClientLibrary;
use App\Library\WeatherDataManagerLibrary;

class WeatherService
{
    protected $weatherApiClient;
    protected $geocodeService;
    protected $WeatherDataManager;
    protected $geocodedLocations = []; // Caching für Geokodierte Locations

    public function __construct(
        WeatherApiClientLibrary $weatherApiClient,
        GeocodeService $geocodeService,
        WeatherDataManagerLibrary $WeatherDataManager
    ) {
        $this->weatherApiClient = $weatherApiClient;
        $this->geocodeService = $geocodeService;
        $this->WeatherDataManager = $WeatherDataManager;
    }

    public function addWeatherToLocations($locations)
    {
        if (is_null($locations) || count($locations) === 0) {
            Log::warning('No locations passed to WeatherService');
            return $locations;
        }

        foreach ($locations as $location) {
            // Validiere Koordinaten
            if (!$this->isValidCoordinates($location->lat_new, $location->lon_new)) {
                Log::warning("Invalid coordinates for location ID: {$location->id}, Name: {$location->title}");

                // Prüfen, ob Geokodierung für diese Location bereits durchgeführt wurde
                if (isset($this->geocodedLocations[$location->id])) {
                    $coordinates = $this->geocodedLocations[$location->id];
                    Log::info("Using cached coordinates for {$location->title}: " . json_encode($coordinates));
                } else {
                    // Versuche, die Koordinaten mit Geokodierung zu korrigieren
                    $coordinates = $this->geocodeService->getCoordinatesByLocationName($location->title);
                    if ($coordinates) {
                        $this->geocodedLocations[$location->id] = $coordinates; // Koordinaten zwischenspeichern
                        Log::info("Updated coordinates for {$location->title}: " . json_encode($coordinates));
                    } else {
                        Log::error("Failed to correct coordinates for location ID: {$location->id}, Name: {$location->title}");
                        $location->current_temp_from_api = 'N/A';
                        $location->current_weather_from_api = 'Keine Daten verfügbar';
                        $location->weather_icon = 'https://openweathermap.org/img/wn/01d@2x.png';
                        continue;
                    }
                }

                // Aktualisiere die Location mit neuen Koordinaten
                $location->lat_new = $coordinates['lat'];
                $location->lon_new = $coordinates['lon'];
            }

            // Abrufen der Wetterdaten
            $weatherData = $this->weatherApiClient->getCurrentWeatherByTimeZone($location->lat_new, $location->lon_new);

            if ($weatherData) {
                $location->current_temp_from_api = $weatherData['current_tmp'] ?? null;
                $location->current_weather_from_api = $weatherData['weather'] ?? null;
                $location->weather_icon = isset($weatherData['icon'])
                    ? "https://openweathermap.org/img/wn/{$weatherData['icon']}@2x.png"
                    : 'https://openweathermap.org/img/wn/01d@2x.png';
            } else {
                Log::error("Weather API returned no data for location ID: {$location->id}, Name: {$location->title}");
                $location->current_temp_from_api = 'N/A';
                $location->current_weather_from_api = 'Keine Daten verfügbar';
                $location->weather_icon = 'https://openweathermap.org/img/wn/01d@2x.png';
            }

            // Füge Country-Flag hinzu
            if ($location->country) {
                $location->country_flag = "https://flagcdn.com/w40/" . strtolower($location->country->country_code) . ".png";
            }
        }

        return $locations;
    }

    /**
     * Holt Wetterdaten für eine Location und aktualisiert die Datenbank bei Bedarf.
     */
    public function getWeatherDataForLocation(WwdeLocation $location)
    {
        // Prüfe, ob die Daten in der Datenbank aktuell sind
        if ($location->weather_updated_at && $location->weather_updated_at->diffInHours(now()) < 1) {
            return [
                'temperature' => $location->current_temp_from_api,
                'description' => $location->current_weather_from_api,
                'icon' => $location->weather_icon,
                'humidity' => $location->humidity,
                'cloudiness' => $location->cloudiness,
                'wind_speed' => $location->wind_speed,
                'wind_direction' => $location->wind_direction,
            ];
        }

        // API-Aufruf für Wetterdaten
        $weatherData = $this->WeatherDataManager->getCurrentWeatherByTimeZone($location->lat, $location->lon, $location->id);
//dd($weatherData);

        if ($weatherData) {
            Log::info('Wetterdaten vor Update', $weatherData);

            // Speichere die Wetterdaten in der wwde_climates Tabelle
            $climateData = [
                'location_id' => $location->id,
                'month_id' => now()->month,
                'month' => now()->format('F'),
                'daily_temperature' => $weatherData['main']['temp'] ?? null,
                'night_temperature' => $weatherData['main']['temp_min'] ?? null,
                'sunshine_per_day' => $weatherData['sunshine_per_day'] ?? null,
            //    'humidity' => $weatherData['humidity'] ?? null,
                'humidity' => $weatherData['main']['humidity'] ?? null,
                'rainy_days' => $weatherData['rainy_days'] ?? null,
                'water_temperature' => $weatherData['water_temperature'] ?? null,
                'icon' => isset($weatherData['icon'])
                    ? "https://openweathermap.org/img/wn/{$weatherData['icon']}@2x.png"
                    : 'https://openweathermap.org/img/wn/01d@2x.png',
            ];

            WwdeClimate::updateOrCreate(
                ['location_id' => $location->id, 'month_id' => now()->month],
                $climateData
            );

            // Gib die aktualisierten Wetterdaten zurück
            return [
                'temperature' => $weatherData['main']['temp'] ?? null,
                'night_temperature' => $weatherData['main']['temp_min'] ?? null,
                'description' => $weatherData['weather'][0]['description'] ?? null,
                'icon' => $climateData['icon'],
                'rainy_days' => $weatherData['rainy_days'] ?? null,
                'wind_speed' => $weatherData['wind']['speed'] ?? null,
                'wind_direction' => $weatherData['wind']['deg'] ?? null,
                'humidity' => $weatherData['main']['humidity'] ?? null,
                'cloudiness' => $weatherData['clouds']['all'] ?? null,
            ];
        }

        Log::error('Fehler beim Abrufen der Wetterdaten');
        return null;
    }

    /**
     * Validiert die Koordinaten.
     */
    private function isValidCoordinates($lat, $lon)
    {
        return !is_null($lat) && !is_null($lon) &&
            $lat >= -90 && $lat <= 90 &&
            $lon >= -180 && $lon <= 180;
    }
}
