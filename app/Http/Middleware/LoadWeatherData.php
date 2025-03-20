<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Library\WeatherDataManagerLibrary;
use App\Services\GeocodeService;

class LoadWeatherData
{
    protected $weatherManager;
    protected $geocodeService;

    public function __construct(WeatherDataManagerLibrary $weatherManager, GeocodeService $geocodeService)
    {
        $this->weatherManager = $weatherManager;
        $this->geocodeService = $geocodeService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('weather_data') || now()->diffInHours($request->session()->get('weather_updated_at')) >= 6) {
            $lat = $request->session()->get('weather_lat', 52.52);
            $lon = $request->session()->get('weather_lon', 13.405);
            $locationId = 1;

            $weatherData = $this->weatherManager->fetchEightDayForecast($lat, $lon, $locationId);
            \Log::info('Wetterdaten geladen:', [$weatherData]);

            if ($weatherData) {
                $request->session()->put('weather_data', $weatherData);
                $request->session()->put('weather_updated_at', now());
            } else {
                // Vollständiger Fallback
                $request->session()->put('weather_data', [
                    'current' => [
                        'temperature' => 'N/A',
                        'weathercode' => 0 // Sicherstellen, dass weathercode immer da ist
                    ],
                    'forecast' => []
                ]);
                $request->session()->put('weather_updated_at', now());
            }
        }
        return $next($request);
    }
}
