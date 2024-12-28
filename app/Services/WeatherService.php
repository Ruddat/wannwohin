<?php

namespace App\Services;

use App\Library\WeatherApiClientLibrary;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $weatherApiClient;
    protected $geocodeService;
    protected $geocodedLocations = []; // Caching für Geokodierte Locations

    public function __construct(WeatherApiClientLibrary $weatherApiClient, GeocodeService $geocodeService)
    {
        $this->weatherApiClient = $weatherApiClient;
        $this->geocodeService = $geocodeService;
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
     * Validiert die Koordinaten.
     *
     * @param float|null $lat
     * @param float|null $lon
     * @return bool
     */
    private function isValidCoordinates($lat, $lon)
    {
        return !is_null($lat) && !is_null($lon) &&
            $lat >= -90 && $lat <= 90 &&
            $lon >= -180 && $lon <= 180;
    }
}
