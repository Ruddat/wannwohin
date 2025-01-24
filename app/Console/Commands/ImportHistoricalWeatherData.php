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

                    // Map the data to your database structure
                    DB::table('climate_monthly_data')->updateOrInsert(
                        [
                            'location_id' => $station->id,
                            'year' => $rowData[0] ?? null,
                            'month' => $rowData[1] ?? null,
                        ],
                        [
                            'month_name' => \Carbon\Carbon::create()->month($rowData[1])->locale('de')->monthName ?? null,
                            'temperature_avg' => $rowData[2] ?? null,
                            'temperature_max' => $rowData[3] ?? null,
                            'temperature_min' => $rowData[4] ?? null,
                            'precipitation' => $rowData[5] ?? null,
                            'snowfall' => $rowData[6] ?? null,
                            'sunshine_hours' => isset($rowData[11]) ? $rowData[11] / 60 : null, // Convert minutes to hours
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
