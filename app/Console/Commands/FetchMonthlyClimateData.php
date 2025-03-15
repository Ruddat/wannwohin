<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FetchMonthlyClimateData extends Command
{
    protected $signature = 'climate:fetch-monthly';
    protected $description = 'Fetch and store monthly climate data for all locations for the previous year, updating only if older than 3 hours';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting monthly climate data fetching...');
        $previousYear = now()->year - 1;
        $lastProcessedId = Cache::get('climate_fetch_last_id', 0); // Letzte verarbeitete ID

        $locations = WwdeLocation::whereNotNull('lat')
            ->whereNotNull('lon')
            ->where('id', '>', $lastProcessedId) // Fortfahren ab letzter ID
            ->orderBy('id')
            ->chunkById(50, function ($locations) use ($previousYear) {
                foreach ($locations as $location) {
                    try {
                        // Prüfe, ob Daten älter als 3 Stunden sind
                        $latestClimate = WwdeClimate::where('location_id', $location->id)
                            ->where('year', $previousYear)
                            ->orderBy('updated_at', 'desc')
                            ->first();

                        if ($latestClimate && $latestClimate->updated_at->gt(now()->subHours(3))) {
                            $this->info("Skipping location: {$location->title} (ID: {$location->id}), data fresh (updated {$latestClimate->updated_at})");
                            Cache::put('climate_fetch_last_id', $location->id, 24 * 60 * 60);
                            continue;
                        }

                        $this->info("Processing location: {$location->title} (ID: {$location->id})");
                        $result = $this->fetchAndStoreClimateData($location, $previousYear);

                        if ($result === 'limit_reached') {
                            $this->warn("API limit reached at location {$location->id}. Pausing run.");
                            Cache::put('climate_fetch_last_id', $location->id - 1, 24 * 60 * 60); // Vorherige ID speichern
                            return false; // Beendet den Chunk
                        }

                        Cache::put('climate_fetch_last_id', $location->id, 24 * 60 * 60);
                        sleep(1); // 1 Sekunde Pause zwischen Anfragen
                    } catch (\Exception $e) {
                        $this->error("Failed to process location: {$location->title}. Error: {$e->getMessage()}");
                        Log::error("Climate data fetching error for location ID {$location->id}: {$e->getMessage()}");
                        Cache::put('climate_fetch_last_id', $location->id, 24 * 60 * 60);
                    }
                }
            });

        if ($locations !== false) {
            Cache::forget('climate_fetch_last_id'); // Reset bei vollständigem Durchlauf
            $this->info('Monthly climate data fetching completed.');
        } else {
            $this->info('Paused due to API limit. Will resume next run from ID ' . Cache::get('climate_fetch_last_id'));
        }
    }

    private function fetchAndStoreClimateData(WwdeLocation $location, int $previousYear)
    {
        $cacheKey = "climate_data_{$location->id}_{$previousYear}";

        // Hole vorhandene Monate
        $existingMonths = WwdeClimate::where('location_id', $location->id)
            ->where('year', $previousYear)
            ->pluck('month_id')
            ->toArray();

        $missingMonths = array_diff(range('01', '12'), $existingMonths);

        // Wenn keine Monate fehlen, überspringen
        if (empty($missingMonths)) {
            $this->info("All months already present for location {$location->id}, year {$previousYear}");
            return true;
        }

        if (!$location->lat || !$location->lon) {
            Log::error("Invalid coordinates for location {$location->id}: lat={$location->lat}, lon={$location->lon}");
            return true;
        }

        $climateData = Cache::get($cacheKey);
        if ($climateData === null) {
            $response = Http::get('https://archive-api.open-meteo.com/v1/archive', [
                'latitude' => $location->lat,
                'longitude' => $location->lon,
                'start_date' => "$previousYear-01-01",
                'end_date' => "$previousYear-12-31",
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,sunshine_duration,relative_humidity_2m_mean,pressure_msl_mean,wind_speed_10m_max,wind_direction_10m_dominant,precipitation_sum,precipitation_hours,cloud_cover_mean',
                'timezone' => 'auto',
            ]);

            if ($response->status() === 429) {
                Log::warning("API limit reached for location {$location->id}. Skipping this run.");
                return 'limit_reached';
            }

            if (!$response->successful() || empty($response->json()['daily']['time'])) {
                Log::warning("No climate data from Open-Meteo for location {$location->id}, year {$previousYear}. Status: {$response->status()}");
                return true;
            }

            $data = $response->json()['daily'];
            $monthlyData = collect($data['time'])->reduce(function ($carry, $date, $key) use ($data) {
                $month = Carbon::parse($date)->month;
                $carry[$month] = $carry[$month] ?? [
                    'temps_max' => [], 'temps_min' => [], 'sunshine_durations' => [], 'humidities' => [],
                    'pressures' => [], 'wind_speeds' => [], 'wind_directions' => [], 'precipitation_hours' => [],
                    'cloud_covers' => [], 'weather_codes' => [],
                ];

                $carry[$month]['temps_max'][] = $data['temperature_2m_max'][$key] ?? null;
                $carry[$month]['temps_min'][] = $data['temperature_2m_min'][$key] ?? null;
                $carry[$month]['sunshine_durations'][] = $data['sunshine_duration'][$key] ?? null;
                $carry[$month]['humidities'][] = $data['relative_humidity_2m_mean'][$key] ?? null;
                $carry[$month]['pressures'][] = $data['pressure_msl_mean'][$key] ?? null;
                $carry[$month]['wind_speeds'][] = $data['wind_speed_10m_max'][$key] ?? null;
                $carry[$month]['wind_directions'][] = $data['wind_direction_10m_dominant'][$key] ?? null;
                $carry[$month]['precipitation_hours'][] = $data['precipitation_hours'][$key] ?? null;
                $carry[$month]['cloud_covers'][] = $data['cloud_cover_mean'][$key] ?? null;
                $carry[$month]['weather_codes'][] = $data['weather_code'][$key] ?? null;

                return $carry;
            }, []);

            $climates = [];
            foreach ($monthlyData as $month => $values) {
                $weatherId = !empty($values['weather_codes']) ? collect($values['weather_codes'])->mode()[0] ?? null : null;
                $climates[$month] = [
                    'year' => $previousYear,
                    'location_id' => $location->id,
                    'month_id' => sprintf('%02d', $month),
                    'month' => Carbon::create()->month($month)->format('F'),
                    'daily_temperature' => $this->safeAvg($values['temps_max']),
                    'night_temperature' => $this->safeAvg($values['temps_min']),
                    'water_temperature' => null,
                    'humidity' => $this->safeAvg($values['humidities']),
                    'sunshine_per_day' => $this->safeAvg($values['sunshine_durations']) / 3600,
                    'rainy_days' => $this->safeSum($values['precipitation_hours']) / 24,
                    'cloudiness' => $this->safeAvg($values['cloud_covers']),
                    'pressure' => $this->safeAvg($values['pressures']),
                    'wind_speed' => $this->safeAvg($values['wind_speeds']),
                    'wind_deg' => $this->safeAvg($values['wind_directions']),
                    'weather_id' => $weatherId,
                    'icon' => $this->mapWeatherIcon($weatherId),
                    'weather_main' => $this->mapWeatherCode($weatherId),
                    'weather_description' => $this->mapWeatherCode($weatherId, true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Cache::put($cacheKey, $climates, 24 * 60 * 60);
            $climateData = $climates;
        }

        if (!empty($climateData) && is_array($climateData)) {
            $insertData = array_filter($climateData, fn($monthData) => in_array($monthData['month_id'], $missingMonths));
            if (!empty($insertData)) {
                WwdeClimate::upsert(
                    array_values($insertData),
                    ['location_id', 'year', 'month_id'],
                    array_keys(reset($insertData))
                );
                $this->info("Climate data stored for location {$location->id}, year {$previousYear}");
            } else {
                $this->info("No new climate data to store for location {$location->id}, year {$previousYear}");
            }
        } else {
            $this->warn("No climate data available for location {$location->id}, year {$previousYear}");
        }

        return true;
    }

    private function safeAvg(array $values): ?float
    {
        $numericValues = array_filter($values, fn($value) => is_numeric($value) && $value !== null);
        return empty($numericValues) ? null : array_sum($numericValues) / count($numericValues);
    }

    private function safeSum(array $values): ?float
    {
        $numericValues = array_filter($values, fn($value) => is_numeric($value) && $value !== null);
        return empty($numericValues) ? null : array_sum($numericValues);
    }

    private function mapWeatherCode(?int $code): string
    {
        $map = [
            0 => 'Klarer Himmel', 1 => 'Leicht bewölkt', 2 => 'Mäßig bewölkt', 3 => 'Bedeckt',
            45 => 'Nebel', 48 => 'Nebel mit Reif', 51 => 'Leichter Nieselregen', 53 => 'Mäßiger Nieselregen',
            55 => 'Starker Nieselregen', 56 => 'Leichter gefrierender Nieselregen', 57 => 'Starker gefrierender Nieselregen',
            61 => 'Leichter Regen', 63 => 'Mäßiger Regen', 65 => 'Starker Regen', 66 => 'Leichter gefrierender Regen',
            67 => 'Starker gefrierender Regen', 71 => 'Leichter Schneefall', 73 => 'Mäßiger Schneefall',
            75 => 'Starker Schneefall', 77 => 'Schneegriesel', 80 => 'Leichte Regenschauer', 81 => 'Mäßige Regenschauer',
            82 => 'Starke Regenschauer', 85 => 'Leichte Schneeschauer', 86 => 'Starke Schneeschauer',
            95 => 'Gewitter', 96 => 'Gewitter mit leichtem Hagel', 99 => 'Gewitter mit starkem Hagel'
        ];
        return $map[$code] ?? 'Unbekannt';
    }

    private function mapWeatherIcon(?int $code): string
    {
        $map = [
            0 => 'sunny', 1 => 'partly-cloudy', 2 => 'partly-cloudy2', 3 => 'cloudy', 45 => 'foggy', 48 => 'foggy',
            51 => 'rainy', 53 => 'rainy', 55 => 'rainy2', 61 => 'rainy', 63 => 'rainy', 65 => 'rainy2',
            80 => 'rainy', 81 => 'rainy', 82 => 'rainy2', 95 => 'thunderstorm', 96 => 'thunderstorm', 99 => 'thunderstorm2',
            56 => 'snowy-sunny', 57 => 'snowy-sunny', 66 => 'snowy-sunny', 67 => 'snowy-sunny', 71 => 'snowy-sunny',
            73 => 'snowy-sunny', 75 => 'snowy-sunny', 77 => 'snowy-cloudy', 85 => 'snowy-sunny', 86 => 'snowy-sunny'
        ];
        return $map[$code] ?? 'cloudy';
    }
}
