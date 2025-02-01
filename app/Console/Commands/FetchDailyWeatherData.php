<?php

namespace App\Console\Commands;

use App\Library\WeatherDataManagerLibrary;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            ->chunkById(50, function ($locations) {
                foreach ($locations as $location) {
                    DB::transaction(function () use ($location) {
                        try {
                            $this->info("Processing location: {$location->title} (ID: {$location->id})");

                            // Prüfen, ob der letzte Eintrag älter als 2 Stunden ist oder kein Eintrag vorhanden ist
                            $lastUpdate = $location->climates()
                                ->latest('updated_at')
                                ->first();

                            // Wenn ein Eintrag vorhanden ist und dieser weniger als 2 Stunden alt ist, überspringen
                            if ($lastUpdate && $lastUpdate->updated_at->gt(now()->subHours(2))) {
                                $this->info("Skipping location: {$location->title}, last updated less than 2 hours ago.");
                                return;
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
                    });
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

        $locations = WwdeLocation::with('climates')->get();
        $today = Carbon::today()->toDateString();

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

                \App\Models\ModDailyClimateAverage::firstOrCreate(
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

            \App\Models\ModDailyClimateAverage::firstOrCreate(
                ['location_id' => $location->id, 'date' => $today],
                $averages
            );

            $this->info("Daily averages stored for location: {$location->title}");
        }
    }

    protected function storeHistoricalData()
    {
        $this->info('Storing historical data...');

        $locations = WwdeLocation::with('climates')->get();
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('F');

        foreach ($locations as $location) {
            $averages = $location->climates()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->selectRaw('
                    AVG(daily_temperature) as avg_daily_temperature,
                    AVG(night_temperature) as avg_night_temperature,
                    AVG(sunshine_per_day) as avg_sunshine_per_day,
                    AVG(humidity) as avg_humidity,
                    SUM(rainy_days) as total_rainy_days,
                    AVG(water_temperature) as avg_water_temperature
                ')
                ->first();

            \App\Models\ModHistoricalClimateData::firstOrCreate(
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
