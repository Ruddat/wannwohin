<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportMonthlyClimateData extends Command
{
    protected $signature = 'climate:import-monthly {year : The year for which climate data should be fetched}';
    protected $description = 'Import monthly climate data from Meteostat API using RapidAPI for each location';

    public function handle()
    {
        $year = $this->argument('year');

        if (!is_numeric($year) || strlen($year) != 4) {
            $this->error("Invalid year format. Please provide a valid year (e.g., 2023).");
            return;
        }

        $apiKey = 'bb55879ddamsh510b49bc9b982cdp1687fcjsn4032200cbab7'; // Replace with your RapidAPI key
        $startDate = "$year-01-01";
        $endDate = "$year-12-31";

        $locations = DB::table('wwde_locations')->get();

        if ($locations->isEmpty()) {
            $this->error("No locations found in the database.");
            return;
        }

        foreach ($locations as $location) {
            $this->info("Processing location: {$location->title} (ID: {$location->id})");

            try {
                // Fetch monthly climate data from Meteostat API via RapidAPI
                $response = Http::withHeaders([
                    'x-rapidapi-host' => 'meteostat.p.rapidapi.com',
                    'x-rapidapi-key' => $apiKey,
                ])->get("https://meteostat.p.rapidapi.com/point/monthly", [
                    'lat' => $location->lat,
                    'lon' => $location->lon,
                    'alt' => $location->alt ?? 0, // Altitude, if available
                    'start' => $startDate,
                    'end' => $endDate,
                ]);

                if ($response->failed()) {
                    $this->error("Failed to fetch data for location: {$location->title}");
                    Log::error("RapidAPI request failed for climate data", [
                        'location' => $location->title,
                        'response' => $response->body(),
                    ]);
                    continue;
                }

                $data = $response->json();

                if (!isset($data['data']) || empty($data['data'])) {
                    $this->warn("No climate data available for location: {$location->title}");
                    continue;
                }

                foreach ($data['data'] as $monthIndex => $entry) {
                    // Convert month index (0-based) to month number and name
                    $monthNumber = $monthIndex + 1;
                    $monthName = \Carbon\Carbon::create()->month($monthNumber)->locale('de')->monthName;

                    DB::table('climate_monthly_data')->insert([
                        'location_id' => $location->id,
                        'month' => $monthNumber,
                        'month_name' => $monthName, // Add month name
                        'year' => $year, // Store the year explicitly
                        'temperature_avg' => $entry['tavg'] ?? null,
                        'temperature_max' => $entry['tmax'] ?? null,
                        'temperature_min' => $entry['tmin'] ?? null,
                        'precipitation' => $entry['prcp'] ?? null,
                        'snowfall' => $entry['snow'] ?? null,
                        'sunshine_hours' => $entry['tsun'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $this->info("Climate data imported for location: {$location->title}");
            } catch (\Exception $e) {
                $this->error("An error occurred while processing location: {$location->title}. Error: {$e->getMessage()}");
                Log::error("Exception during processing", [
                    'location' => $location->title,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Climate data import completed for all locations.");
    }
}
