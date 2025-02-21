<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class WetterTestController extends Controller
{
    public function showWeather()
    {
        $lat = "52.5200"; // Breitengrad für Berlin
        $lon = "13.4050"; // Längengrad für Berlin

        // Open-Meteo API für Wetterdaten inklusive Sonnenaufgang/-untergang
        $response = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $lat,
            'longitude' => $lon,
            'hourly' => 'temperature_2m,weathercode',
            'daily' => 'sunrise,sunset',
            'timezone' => 'auto', // Automatische Zeitzonenanpassung
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Sonnenaufgang & Sonnenuntergang ermitteln
            $sunrise = Carbon::parse($data['daily']['sunrise'][0])->format('H:i');
            $sunset = Carbon::parse($data['daily']['sunset'][0])->format('H:i');

            // Aktuelles Wetter extrahieren
            $currentHourIndex = Carbon::now()->hour;
            $weather_data = [
                'temperature' => $data['hourly']['temperature_2m'][$currentHourIndex] ?? null,
                'description' => $this->mapWeatherCode($data['hourly']['weathercode'][$currentHourIndex] ?? null),
                'icon' => $this->mapWeatherIcon($data['hourly']['weathercode'][$currentHourIndex] ?? null),
                'sunrise' => $sunrise,
                'sunset' => $sunset
            ];

            // Stündliche Wetterdaten für die nächsten 24 Stunden
            $hourly_weather = [];
            foreach (array_slice($data['hourly']['time'], 0, 24) as $index => $time) {
                $hour = Carbon::parse($time)->format('H');
                $isDaytime = ($hour >= Carbon::parse($sunrise)->format('H') && $hour < Carbon::parse($sunset)->format('H'));

                $hourly_weather[] = [
                    'day' => (Carbon::parse($time)->isToday()) ? 'tod' : 'tom',
                    'hour' => $hour,
                    'weather' => $this->mapWeatherIcon($data['hourly']['weathercode'][$index], $isDaytime),
                    'temp' => round($data['hourly']['temperature_2m'][$index]),
                    'time' => Carbon::parse($time)->format('H:i')
                ];
            }

            return view('weather-test', compact('weather_data', 'hourly_weather'));
        }

        return abort(500, 'Wetterdaten konnten nicht geladen werden.');
    }

    // Wetter-Code von Open-Meteo in eigene Icons umwandeln
    private function mapWeatherIcon($code, $isDaytime = true)
    {
        $map = [
            0 => $isDaytime ? 'sunny' : 'clear-night',
            1 => $isDaytime ? 'partly-cloudy' : 'partly-cloudy-night',
            2 => $isDaytime ? 'partly-cloudy' : 'partly-cloudy-night',
            3 => 'cloudy',
            45 => 'foggy',
            48 => 'foggy',
            51 => 'rainy',
            53 => 'rainy',
            55 => 'rainy',
            56 => 'snowy',
            57 => 'snowy',
            61 => 'rainy',
            63 => 'rainy',
            65 => 'rainy',
            66 => 'snowy',
            67 => 'snowy',
            71 => 'snowy',
            73 => 'snowy',
            75 => 'snowy',
            77 => 'snowy',
            80 => 'rainy',
            81 => 'rainy',
            82 => 'rainy',
            85 => 'snowy',
            86 => 'snowy',
            95 => 'thunderstorm',
            96 => 'thunderstorm',
            99 => 'thunderstorm'
        ];

        return $map[$code] ?? 'unknown';
    }

    private function mapWeatherCode($code)
    {
        $map = [
            0 => 'Klarer Himmel',
            1 => 'Leicht bewölkt',
            2 => 'Mäßig bewölkt',
            3 => 'Bedeckt',
            45 => 'Nebel',
            48 => 'Nebel mit Reif',
            51 => 'Leichter Nieselregen',
            53 => 'Mäßiger Nieselregen',
            55 => 'Starker Nieselregen',
            56 => 'Leichter gefrierender Nieselregen',
            57 => 'Starker gefrierender Nieselregen',
            61 => 'Leichter Regen',
            63 => 'Mäßiger Regen',
            65 => 'Starker Regen',
            66 => 'Leichter gefrierender Regen',
            67 => 'Starker gefrierender Regen',
            71 => 'Leichter Schneefall',
            73 => 'Mäßiger Schneefall',
            75 => 'Starker Schneefall',
            77 => 'Schneegriesel',
            80 => 'Leichte Regenschauer',
            81 => 'Mäßige Regenschauer',
            82 => 'Starke Regenschauer',
            85 => 'Leichte Schneeschauer',
            86 => 'Starke Schneeschauer',
            95 => 'Gewitter',
            96 => 'Gewitter mit leichtem Hagel',
            99 => 'Gewitter mit starkem Hagel'
        ];

        return $map[$code] ?? 'Unbekannt';
    }
}
