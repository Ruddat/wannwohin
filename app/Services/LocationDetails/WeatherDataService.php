<?php

namespace App\Services\LocationDetails;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class WeatherDataService
{
    public function get($location): array
    {
        $lat = $location->lat;
        $lon = $location->lon;

        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}"
             . "&current=temperature_2m,relative_humidity_2m,apparent_temperature,pressure_msl,"
             . "wind_speed_10m,wind_direction_10m,is_day"
             . "&daily=weathercode,temperature_2m_max,temperature_2m_min,sunrise,sunset"
             . "&timezone=auto";

        $response = Http::get($url);

        if (!$response->successful()) {
            return [
                'current'  => [],
                'forecast' => []
            ];
        }

        $data = $response->json();

        // °°°°°°°°°°°°°°°°°°°°°
        //   Aktuelle Werte
        // °°°°°°°°°°°°°°°°°°°°°
        $current = $data['current'] ?? [];

        $isDaytime = ($current['is_day'] ?? 1) == 1;
        $temperature = $current['temperature_2m'] ?? null;

        // Wettercode für aktuelle Stunde (aus daily[0])
        $currentWeatherCode = $data['daily']['weathercode'][0] ?? null;

        $currentIcon = $this->mapWeatherIcon(
            $currentWeatherCode,
            $isDaytime,
            $temperature
        );

        // °°°°°°°°°°°°°°°°°°°°°
        //   Forecast (7 Tage)
        // °°°°°°°°°°°°°°°°°°°°°
        $forecast = [];
        if (isset($data['daily'])) {
            foreach ($data['daily']['time'] as $i => $date) {
                $weathercode = $data['daily']['weathercode'][$i];

                $forecast[] = [
                    'date'      => $date,
                    'weekday'   => Carbon::parse($date)->translatedFormat('l'),
                    'temp_max'  => $data['daily']['temperature_2m_max'][$i],
                    'temp_min'  => $data['daily']['temperature_2m_min'][$i],
                    'sunrise'   => Carbon::parse($data['daily']['sunrise'][$i])->format('H:i'),
                    'sunset'    => Carbon::parse($data['daily']['sunset'][$i])->format('H:i'),
                    'icon'      => $this->mapWeatherIcon($weathercode, true),
                    'weather'   => $this->mapWeatherCode($weathercode),
                ];
            }
        }

        return [
            'current' => [
                'date'          => now()->format('d.m.Y'),
                'weekday'       => now()->translatedFormat('l'),
                'time'          => now()->format('H:i'),
                'temperature'   => $temperature,
                'real_feel'     => $current['apparent_temperature'] ?? null,
                'humidity'      => $current['relative_humidity_2m'] ?? null,
                'pressure'      => $current['pressure_msl'] ?? null,
                'wind_speed'    => $current['wind_speed_10m'] ?? null,
                'wind_direction'=> $current['wind_direction_10m'] ?? null,
                'icon'          => $currentIcon,
            ],

            'forecast' => $forecast
        ];
    }

    private function mapWeatherIcon(?int $code, bool $isDaytime = true, float $temperature = null): string
    {
        // Extrem heiß
        if ($temperature && $temperature > 35 && $isDaytime) {
            return 'sunny-hot';
        }

        $map = [
            0 => $isDaytime ? 'sunny' : 'clear-night',
            1 => $isDaytime ? 'partly-cloudy' : 'partly-cloudy-night',
            2 => $isDaytime ? 'partly-cloudy2' : 'partly-cloudy-night',
            3 => 'cloudy',
            45 => 'foggy',
            48 => 'foggy',
            51 => 'rainy',
            53 => 'rainy',
            55 => 'rainy2',
            56 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            57 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            61 => 'rainy',
            63 => 'rainy',
            65 => 'rainy2',
            66 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            67 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            71 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            73 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            75 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            77 => 'snowy-cloudy',
            80 => 'rainy',
            81 => 'rainy',
            82 => 'rainy2',
            85 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            86 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy',
            95 => 'thunderstorm',
            96 => 'thunderstorm',
            99 => 'thunderstorm2',
        ];

        return $map[$code] ?? 'cloudy';
    }

    private function mapWeatherCode(?int $code): string
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
