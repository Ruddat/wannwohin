<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Services\GeocodeService;
use App\Http\Controllers\Controller;
use App\Library\WeatherDataManagerLibrary;

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
        if ($request->has('lat') && $request->has('lon')) {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
        } elseif ($request->has('location')) {
            $coords = $this->geocodeService->getCoordinatesByLocationName($request->input('location'));
            $lat = $coords['lat'] ?? 52.52;
            $lon = $coords['lon'] ?? 13.405;
        } else {
            $lat = 52.52;
            $lon = 13.405;
        }

        $weatherData = $this->weatherManager->fetchEightDayForecast($lat, $lon, 1);

        if ($weatherData) {
            $request->session()->put('weather_data', $weatherData);
            $request->session()->put('weather_lat', $lat);
            $request->session()->put('weather_lon', $lon);
            $request->session()->put('weather_updated_at', now());

            $response = [
                'current' => [
                    'temperature' => $weatherData['current']['temperature'],
                    'icon' => $this->mapWeatherCodeToIcon($weatherData['current']['weathercode'] ?? 0)
                ],
                'forecast' => array_map(function ($day, $index) {
                    return [
                        'weekday' => $day['weekday'],
                        'temp_max' => $day['temp_max'],
                        'icon' => $this->mapWeatherCodeToIcon($day['weathercode'] ?? 0),
                        'precipitation' => $day['precipitation'],
                        'index' => $index
                    ];
                }, $weatherData['forecast'], array_keys($weatherData['forecast']))
            ];

            return response()->json($response);
        }

        return response()->json([
            'current' => ['temperature' => 'N/A', 'icon' => 'ph-cloud'],
            'forecast' => []
        ], 500);
    }

    private function mapWeatherCodeToIcon($weatherCode)
    {
        $iconMap = [
            0 => 'ph-sun',
            1 => 'ph-cloud-sun',
            2 => 'ph-cloud',
            3 => 'ph-cloud',
            61 => 'ph-cloud-rain',
            63 => 'ph-cloud-rain',
            65 => 'ph-cloud-rain',
            95 => 'ph-cloud-lightning',
        ];
        return $iconMap[$weatherCode] ?? 'ph-cloud';
    }
}
