<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportHistoricalWeatherData extends Command
{
    protected $signature = 'weather:import-historical';
    protected $description = 'Download and import historical monthly weather data for all locations with assigned stations';

    public function handle()
    {
        ini_set('memory_limit', '4G'); // Memory-Limit erhöhen

        $stations = DB::table('wwde_locations')->whereNotNull('station_id')->get();

        if ($stations->isEmpty()) {
            $this->error('No locations with assigned weather stations found.');
            return;
        }

        // Ensure the temp directory exists
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        foreach ($stations as $station) {
            $this->info("Processing station: {$station->station_id} (Location: {$station->title})");

            try {
                $url = "https://bulk.meteostat.net/v2/monthly/{$station->station_id}.csv.gz";
                $tempFilePath = "$tempDir/{$station->station_id}.csv.gz";
                $csvFilePath = "$tempDir/{$station->station_id}.csv";

                // Download the compressed CSV file
                $this->info("Downloading data from $url");
                $gzData = file_get_contents($url);

                if ($gzData === false) {
                    $this->warn("Failed to download data for station: {$station->station_id}");
                    continue;
                }

                // Save the GZ file temporarily
                file_put_contents($tempFilePath, $gzData);

                // Extract the GZ file
                $this->info("Extracting data for station: {$station->station_id}");
                $gzFile = gzopen($tempFilePath, 'rb');
                $csvFile = fopen($csvFilePath, 'wb');

                if ($gzFile === false || $csvFile === false) {
                    throw new \Exception("Failed to open GZ or CSV file for station: {$station->station_id}");
                }

                while (!gzeof($gzFile)) {
                    fwrite($csvFile, gzread($gzFile, 4096));
                }

                gzclose($gzFile);
                fclose($csvFile);

                // Use PhpSpreadsheet to read and process the CSV
                $this->info("Parsing data for station: {$station->station_id}");
                $spreadsheet = IOFactory::load($csvFilePath);
                $worksheet = $spreadsheet->getActiveSheet();

                // Get rows from the CSV
// Parse and process each row from the CSV file
foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
    // Skip the header row
    if ($rowIndex === 1) {
        continue;
    }

    $cells = $row->getCellIterator();
    $cells->setIterateOnlyExistingCells(false); // Loop through all cells
    $rowData = [];
    foreach ($cells as $cell) {
        $rowData[] = $cell->getValue();
    }

    // Ensure critical fields are present
    if (!isset($rowData[0], $rowData[1])) {
        $this->warn("Skipping invalid row at index $rowIndex for station: {$station->station_id}");
        continue;
    }

    // Map data and handle missing fields
    $year = $rowData[0];
    $month = $rowData[1];
    $tavg = $rowData[2] ?? null;
    $tmin = $rowData[3] ?? null;
    $tmax = $rowData[4] ?? null;
    $prcp = $rowData[5] ?? null;
    $snow = $rowData[6] ?? null;
    $wdir = $rowData[7] ?? null;
    $wspd = $rowData[8] ?? null;
    $wpgt = $rowData[9] ?? null;
    $pres = $rowData[10] ?? null;
    $tsun = isset($rowData[11]) ? $rowData[11] / 60 : null; // Convert minutes to hours

    // Log missing data for debugging
    if ($tavg === null || $tmin === null || $tmax === null) {
        $this->warn("Temperature data is incomplete for row $rowIndex in station: {$station->station_id}");
    }

    DB::table('climate_monthly_data')->updateOrInsert(
        [
            'location_id' => $station->id,
            'year' => $year,
            'month' => $month,
        ],
        [
            'month_name' => \Carbon\Carbon::create()->month($month)->locale('de')->monthName,
            'temperature_avg' => $tavg,
            'temperature_max' => $tmax,
            'temperature_min' => $tmin,
            'precipitation' => $prcp,
            'snowfall' => $snow,
            'sunshine_hours' => $tsun,
            'wind_direction' => $wdir,
            'wind_speed' => $wspd,
            'peak_wind_gust' => $wpgt,
            'sea_level_pressure' => $pres,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );
}


                $this->info("Data imported successfully for station: {$station->station_id}");

                // Clean up temporary files
                unlink($tempFilePath);
                unlink($csvFilePath);

            } catch (\Exception $e) {
                $this->error("An error occurred for station: {$station->station_id}. Error: {$e->getMessage()}");
                Log::error("Error during historical weather data import", [
                    'station_id' => $station->station_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Historical weather data import completed for all stations.');
    }
}
