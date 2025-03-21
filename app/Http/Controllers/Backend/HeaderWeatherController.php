<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Services\GeocodeService;
use App\Http\Controllers\Controller;
use App\Library\WeatherDataManagerLibrary;
use Illuminate\Support\Facades\Log;

class HeaderWeatherController extends Controller
{
    protected $weatherManager;
    protected $geocodeService;

    public function __construct(WeatherDataManagerLibrary $weatherManager, GeocodeService $geocodeService)
    {
        $this->weatherManager = $weatherManager;
        $this->geocodeService = $geocodeService;
    }

    public function update(Request $request)
    {
        try {
            if ($request->has('lat') && $request->has('lon')) {
                $lat = $request->input('lat');
                $lon = $request->input('lon');
            } elseif ($request->has('location')) {
                $coords = $this->geocodeService->searchByNominatimOnly($request->input('location'));
                Log::info('Geocode Koordinaten für ' . $request->input('location'), $coords);
                $lat = $coords['lat'] ?? 52.52;
                $lon = $coords['lon'] ?? 13.405;
            } else {
                $lat = 52.52;
                $lon = 13.405;
            }

            // Wetterdaten immer frisch holen
            $weatherData = $this->weatherManager->fetchEightDayForecast($lat, $lon, 1);
            Log::info('Wetterdaten von API:', $weatherData ?? ['error' => 'Keine Daten']);

            if ($weatherData && is_array($weatherData) && isset($weatherData['current'], $weatherData['forecast'])) {
                $request->session()->put('weather_data', $weatherData);
                $request->session()->put('weather_lat', $lat);
                $request->session()->put('weather_lon', $lon);
                $request->session()->put('weather_updated_at', now());

                $response = [
                    'current' => [
                        'temperature' => $weatherData['current']['temperature'] ?? 'N/A',
                        'icon' => $this->mapWeatherCodeToIcon($weatherData['current']['weathercode'] ?? 0)
                    ],
                    'forecast' => array_map(function ($day, $index) {
                        return [
                            'weekday' => $day['weekday'] ?? 'N/A',
                            'temp_max' => $day['temp_max'] ?? 'N/A',
                            'icon' => $this->mapWeatherCodeToIcon($day['weathercode'] ?? 0),
                            'precipitation' => $day['precipitation'] ?? 0,
                            'index' => $index
                        ];
                    }, $weatherData['forecast'], array_keys($weatherData['forecast']))
                ];

                return response()->json($response);
            }

            Log::warning('Ungültige Wetterdaten oder keine Daten zurückgegeben');
            return response()->json([
                'current' => ['temperature' => 'N/A', 'icon' => 'ph-cloud'],
                'forecast' => []
            ], 500);
        } catch (\Exception $e) {
            Log::error('Fehler in HeaderWeatherController::update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Serverfehler: ' . $e->getMessage()
            ], 500);
        }
    }

    private function mapWeatherCodeToIcon($weatherCode)
    {
        $iconMap = [
            // Klar und sonnig
            0 => 'ph-sun',               // Klarer Himmel
            1 => 'ph-cloud-sun',         // Meist sonnig

            // Bewölkt
            2 => 'ph-cloud',             // Teilweise bewölkt
            3 => 'ph-cloud',             // Bewölkt

            // Nebel
            45 => 'ph-cloud-fog',        // Nebel
            48 => 'ph-cloud-fog',        // Gefrierender Nebel

            // Nieselregen
            51 => 'ph-cloud-rain',       // Leichter Nieselregen
            53 => 'ph-cloud-rain',       // Mäßiger Nieselregen
            55 => 'ph-cloud-rain',       // Starker Nieselregen
            56 => 'ph-cloud-snow',       // Leichter gefrierender Nieselregen
            57 => 'ph-cloud-snow',       // Starker gefrierender Nieselregen

            // Regen
            61 => 'ph-cloud-rain',       // Leichter Regen
            63 => 'ph-cloud-rain',       // Mäßiger Regen
            65 => 'ph-cloud-rain',       // Starker Regen
            66 => 'ph-cloud-snow',       // Gefrierender Regen (leicht)
            67 => 'ph-cloud-snow',       // Gefrierender Regen (stark)

            // Schnee
            71 => 'ph-snowflake',        // Leichter Schneefall
            73 => 'ph-snowflake',        // Mäßiger Schneefall
            75 => 'ph-snowflake',        // Starker Schneefall
            77 => 'ph-snowflake',        // Schneekörner

            // Regenschauer
            80 => 'ph-cloud-rain',       // Leichter Regenschauer
            81 => 'ph-cloud-rain',       // Mäßiger Regenschauer
            82 => 'ph-cloud-rain',       // Starker Regenschauer

            // Schneeschauer
            85 => 'ph-snowflake',        // Leichter Schneeschauer
            86 => 'ph-snowflake',        // Starker Schneeschauer

            // Gewitter
            95 => 'ph-cloud-lightning',  // Gewitter
            96 => 'ph-cloud-lightning',  // Gewitter mit leichtem Hagel
            99 => 'ph-cloud-lightning',  // Gewitter mit starkem Hagel
        ];

        return $iconMap[$weatherCode] ?? 'ph-cloud'; // Fallback für unbekannte Codes
    }
}
