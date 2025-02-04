<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ModDailyClimateAverage;
use App\Models\ModHistoricalClimateData;
use App\Library\WeatherDataManagerLibrary;

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
            ->where('updated_at', '>=', now()->startOfDay())
            ->where('updated_at', '<=', now()->endOfDay())
            ->latest('updated_at')
            ->get();


            $this->info("Number of climate records for location {$location->id} on {$today}: " . $climatesToday->count());

            if ($climatesToday->isEmpty()) {
                $this->warn("No climate data for location: {$location->id} today. Skipping.");
                continue;
            }

            // Durchschnittswerte berechnen
            $averages = [
                'avg_daily_temperature' => $climatesToday->avg('daily_temperature'),
                'avg_night_temperature' => $climatesToday->avg('night_temperature'),
                'avg_sunshine_per_day' => $climatesToday->avg('sunshine_per_day'),
                'avg_humidity' => $climatesToday->avg('humidity'),
                'total_rainy_days' => $climatesToday->sum('rainy_days'),
                'avg_water_temperature' => $climatesToday->avg('water_temperature'),
            ];

            // Debugging: Zeige die berechneten Durchschnittswerte an
            $this->info("Calculated averages for location {$location->id}: " . json_encode($averages));

            // Durchschnittswerte speichern
           ModDailyClimateAverage::updateOrCreate(
                ['location_id' => $location->id, 'date' => now()->toDateString()],
                $averages
            );


            $this->info("Daily averages stored for location: {$location->title}");
        }
    }

    protected function storeHistoricalData()
    {
        $this->info('Storing historical data...');

        $locations = WwdeLocation::with('climates')->get();
        $year = now()->year;
        $month = now()->format('F');

        foreach ($locations as $location) {
            $climatesThisMonth = $location->climates()
                ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->selectRaw('
                    AVG(daily_temperature) as avg_daily_temperature,
                    AVG(night_temperature) as avg_night_temperature,
                    AVG(sunshine_per_day) as avg_sunshine_per_day,
                    AVG(humidity) as avg_humidity,
                    SUM(rainy_days) as total_rainy_days,
                    AVG(water_temperature) as avg_water_temperature
                ')
                ->first();

            if (!$climatesThisMonth) {
                $this->warn("No climate data for location: {$location->id} in {$month} {$year}. Skipping.");
                continue;
            }

            // Debugging: Werte überprüfen
            $this->info("Averages for location {$location->id} in {$month} {$year}: " . json_encode($climatesThisMonth));

            ModHistoricalClimateData::updateOrCreate(
                ['location_id' => $location->id, 'year' => $year, 'month' => $month],
                [
                    'avg_daily_temperature' => $climatesThisMonth->avg_daily_temperature ?? 0,
                    'avg_night_temperature' => $climatesThisMonth->avg_night_temperature ?? 0,
                    'avg_sunshine_per_day' => $climatesThisMonth->avg_sunshine_per_day ?? 0,
                    'avg_humidity' => $climatesThisMonth->avg_humidity ?? 0,
                    'total_rainy_days' => $climatesThisMonth->total_rainy_days ?? 0,
                    'avg_water_temperature' => $climatesThisMonth->avg_water_temperature ?? 0,
                ]
            );

            $this->info("Historical data stored for location: {$location->title}");
        }
    }

}
