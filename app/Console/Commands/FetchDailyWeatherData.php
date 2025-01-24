<?php

namespace App\Console\Commands;

use App\Library\WeatherDataManagerLibrary;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchDailyWeatherData extends Command
{
    protected $signature = 'climate:fetch-daily';
    protected $description = 'Fetch daily weather data for locations and store it in the database';

    public function __construct(protected WeatherDataManagerLibrary $weatherManager)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting daily weather data fetching...');

        WwdeLocation::whereNotNull('lat')
            ->whereNotNull('lon')
            ->chunk(50, function ($locations) {
                foreach ($locations as $location) {
                    try {
                        $this->info("Processing location: {$location->title} (ID: {$location->id})");

                        // Prüfen, ob der letzte Eintrag älter als 2 Stunden ist
                        $lastUpdate = $location->climates()
                            ->where('updated_at', '<', now()->subHours(2))
                            ->latest('updated_at')
                            ->first();

                        if (!$lastUpdate) {
                            $this->info("Skipping location: {$location->title}, last updated less than 2 hours ago.");
                            continue;
                        }

                        // Wetterdaten abrufen und speichern
                        $weatherData = $this->weatherManager->fetchAndStoreWeatherData(
                            $location->lat,
                            $location->lon,
                            $location->id
                        );

                        if ($weatherData) {
                            $this->info("Weather data successfully updated for location: {$location->title}");
                        } else {
                            $this->warn("No weather data available for location: {$location->title}");
                        }
                    } catch (\Exception $e) {
                        $this->error("Failed to process location: {$location->title}. Error: {$e->getMessage()}");
                        Log::error("Weather data fetching error for location ID {$location->id}: {$e->getMessage()}");
                    }

                    // Delay zwischen den Anfragen
                    usleep(1000000); // 1 Sekunde
                }
            });

        $this->info('Daily weather data fetching completed.');

        // Historische Daten speichern
        $this->storeHistoricalData();

        // Durchschnittswerte speichern
        $this->storeDailyAverages();

        $this->info('Daily averages and historical data stored successfully.');
    }

    protected function storeDailyAverages()
    {
        $this->info('Calculating daily averages...');

        $locations = WwdeLocation::all();
        $today = now()->toDateString();

        foreach ($locations as $location) {
            // Heutige Klimadaten abrufen
            $climatesToday = $location->climates()
                ->whereDate('created_at', $today)
                ->get();

            if ($climatesToday->isEmpty()) {
                $this->warn("No climate data for location: {$location->id} today. Skipping.");
                continue;
            }

            // Prüfen, ob nur ein Eintrag vorhanden ist
            if ($climatesToday->count() === 1) {
                $firstEntry = $climatesToday->first();

                \App\Models\ModDailyClimateAverage::updateOrCreate(
                    ['location_id' => $location->id, 'date' => $today],
                    [
                        'avg_daily_temperature' => $firstEntry->daily_temperature,
                        'avg_night_temperature' => $firstEntry->night_temperature,
                        'avg_sunshine_per_day' => $firstEntry->sunshine_per_day,
                        'avg_humidity' => $firstEntry->humidity,
                        'total_rainy_days' => $firstEntry->rainy_days,
                        'avg_water_temperature' => $firstEntry->water_temperature,
                    ]
                );

                $this->info("First climate data copied for location: {$location->title}");
                continue;
            }

            // Durchschnitt berechnen
            $averages = $climatesToday
                ->map(function ($climate) {
                    return [
                        'daily_temperature' => $climate->daily_temperature,
                        'night_temperature' => $climate->night_temperature,
                        'sunshine_per_day' => $climate->sunshine_per_day,
                        'humidity' => $climate->humidity,
                        'rainy_days' => $climate->rainy_days,
                        'water_temperature' => $climate->water_temperature,
                    ];
                })
                ->reduce(function ($carry, $item) {
                    foreach ($item as $key => $value) {
                        $carry[$key] = ($carry[$key] ?? 0) + ($value ?? 0);
                    }
                    return $carry;
                }, []);

            $averages = array_map(fn($value) => round($value / $climatesToday->count(), 2), $averages);

            \App\Models\ModDailyClimateAverage::updateOrCreate(
                ['location_id' => $location->id, 'date' => $today],
                $averages
            );

            $this->info("Daily averages stored for location: {$location->title}");
        }
    }

    protected function storeHistoricalData()
    {
        $this->info('Storing historical data...');

        $locations = WwdeLocation::all();
        $year = now()->year;
        $month = now()->format('F');

        foreach ($locations as $location) {
            $averages = $location->climates()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', now()->month)
                ->selectRaw('
                    AVG(daily_temperature) as avg_daily_temperature,
                    AVG(night_temperature) as avg_night_temperature,
                    AVG(sunshine_per_day) as avg_sunshine_per_day,
                    AVG(humidity) as avg_humidity,
                    SUM(rainy_days) as total_rainy_days,
                    AVG(water_temperature) as avg_water_temperature
                ')
                ->first();

            \App\Models\ModHistoricalClimateData::updateOrCreate(
                ['location_id' => $location->id, 'year' => $year, 'month' => $month],
                [
                    'avg_daily_temperature' => $averages->avg_daily_temperature ?? 0,
                    'avg_night_temperature' => $averages->avg_night_temperature ?? 0,
                    'avg_sunshine_per_day' => $averages->avg_sunshine_per_day ?? 0,
                    'avg_humidity' => $averages->avg_humidity ?? 0,
                    'total_rainy_days' => $averages->total_rainy_days ?? 0,
                    'avg_water_temperature' => $averages->avg_water_temperature ?? 0,
                ]
            );

            $this->info("Historical data stored for location: {$location->title}");
        }
    }
}
