<?php

namespace App\Console\Commands;

use App\Library\WeatherApiClientLibrary;
use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use App\Models\MonthlyClimateSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FetchDailyWeatherData extends Command
{
    protected $signature = 'climate:fetch-daily';
    protected $description = 'Fetch daily weather data for locations, store it in the climate table, and generate summaries';

    public function handle()
    {
        $weatherClient = new WeatherApiClientLibrary();

        // Chunk the locations to process in batches
        WwdeLocation::whereNotNull('lat')->whereNotNull('lon')->chunk(50, function ($locations) use ($weatherClient) {
            foreach ($locations as $location) {
                $this->info("Fetching daily data for location: {$location->title}");

                $weatherData = $weatherClient->fetchCurrentWeather($location->lat, $location->lon);

                if ($weatherData) {
                    WwdeClimate::updateOrCreate(
                        [
                            'location_id' => $location->id,
                            'month_id' => now()->month,
                            'month' => now()->format('F'),
                        ],
                        [
                            'daily_temperature' => $weatherData['daily_temperature'],
                            'night_temperature' => $weatherData['night_temperature'],
                            'sunshine_per_day' => $weatherData['sunshine_per_day'],
                            'humidity' => $weatherData['humidity'],
                            'rainy_days' => $weatherData['rainy_days'],
                        ]
                    );

                    $this->info("Daily data stored for location: {$location->title}");
                } else {
                    $this->error("Failed to fetch daily data for location: {$location->title}");
                }

                // Delay to respect API rate limits
                usleep(1000000); // 1 second delay between requests (60 calls/minute)
            }
        });

        $this->info('Daily weather data fetching completed.');

        // Generate monthly summary after fetching data
        $this->generateMonthlySummary();

        // Archive yearly data if needed
        $this->archiveYearlyData();

        return 0;
    }

    /**
     * Generate monthly summaries for each location.
     */
    protected function generateMonthlySummary()
    {
        $this->info('Generating monthly climate summaries...');

        $summaries = DB::table('wwde_climates')
            ->select(
                'location_id',
                'month_id',
                DB::raw('AVG(daily_temperature) as avg_daily_temperature'),
                DB::raw('AVG(night_temperature) as avg_night_temperature'),
                DB::raw('AVG(sunshine_per_day) as avg_sunshine_per_day'),
                DB::raw('AVG(humidity) as avg_humidity'),
                DB::raw('SUM(rainy_days) as total_rainy_days')
            )
            ->groupBy('location_id', 'month_id')
            ->get();

        foreach ($summaries as $summary) {
            MonthlyClimateSummary::updateOrCreate(
                [
                    'location_id' => $summary->location_id,
                    'month_id' => $summary->month_id,
                ],
                [
                    'avg_daily_temperature' => $summary->avg_daily_temperature,
                    'avg_night_temperature' => $summary->avg_night_temperature,
                    'avg_sunshine_per_day' => $summary->avg_sunshine_per_day,
                    'avg_humidity' => $summary->avg_humidity,
                    'total_rainy_days' => $summary->total_rainy_days,
                ]
            );

            $this->info("Summary stored for location ID: {$summary->location_id}, Month ID: {$summary->month_id}");
        }

        $this->info('Monthly climate summaries generated successfully.');
    }

    /**
     * Archive yearly data from the climate table.
     */
    protected function archiveYearlyData()
    {
        $this->info('Archiving yearly climate data...');

        $year = now()->subYear()->year;

        $dataToArchive = WwdeClimate::whereYear('created_at', $year)->get();

        if ($dataToArchive->isEmpty()) {
            $this->info("No data found for archiving for the year {$year}.");
            return;
        }

        DB::table('wwde_climate_archives')->insert(
            $dataToArchive->map(function ($item) {
                return $item->toArray();
            })->toArray()
        );

        WwdeClimate::whereYear('created_at', $year)->delete();

        $this->info("Archived data for the year {$year} successfully.");
    }
}
