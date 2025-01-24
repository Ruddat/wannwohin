<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateClimateData extends Command
{
    protected $signature = 'climate:aggregate {location_id}';
    protected $description = 'Aggregate climate data and store it in the climate_aggregated_data table';

    public function handle()
    {
        $locationId = $this->argument('location_id');

        // Überprüfen, ob die Location existiert
        $locationExists = DB::table('wwde_locations')->where('id', $locationId)->exists();
        if (!$locationExists) {
            $this->error("Location with ID $locationId does not exist.");
            return;
        }

        $this->info("Aggregating climate data for location ID: $locationId");

        // Daten aus climate_monthly_data aggregieren
        $aggregatedData = DB::table('climate_monthly_data')
            ->selectRaw('
                MONTH(date) as month,
                AVG(CASE WHEN datatype = "TAVG" THEN value ELSE NULL END) as avg_daily_temp,
                AVG(CASE WHEN datatype = "TMIN" THEN value ELSE NULL END) as min_temp,
                AVG(CASE WHEN datatype = "TMAX" THEN value ELSE NULL END) as max_temp,
                AVG(CASE WHEN datatype = "HUM" THEN value ELSE NULL END) as avg_humidity,
                COUNT(DISTINCT CASE WHEN datatype = "PRCP" AND value > 0 THEN date ELSE NULL END) as total_rainy_days,
                COUNT(DISTINCT date) as days_in_month
            ')
            ->where('location_id', $locationId)
            ->groupByRaw('MONTH(date)')
            ->orderByRaw('MONTH(date)')
            ->get();

        // Daten in climate_aggregated_data speichern
        foreach ($aggregatedData as $data) {
            if ($data->days_in_month > 0) {
                DB::table('climate_aggregated_data')->updateOrInsert(
                    [
                        'location_id' => $locationId,
                        'month' => $data->month,
                    ],
                    [
                        'avg_daily_temp' => round($data->avg_daily_temp, 1),
                        'min_temp' => round($data->min_temp, 1),
                        'max_temp' => round($data->max_temp, 1),
                        'avg_humidity' => round($data->avg_humidity, 1),
                        'total_rainy_days' => $data->total_rainy_days,
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->info("Climate data aggregation completed for location ID: $locationId");
    }
}
