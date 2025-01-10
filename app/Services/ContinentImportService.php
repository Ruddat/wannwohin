<?php

namespace App\Services;

use App\Models\WwdeContinent;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ContinentImportService
{
    public function import(string $filePath): bool
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue; // Skip header row
            }

            // Map data to your model
            WwdeContinent::updateOrCreate(
                ['alias' => $row[2]], // Match by alias for idempotent updates
                [
                    'title' => $row[1],
                    'area_km' => $row[3],
                    'population' => $row[4],
                    'no_countries' => $row[5],
                    'no_climate_tables' => $row[6],
                    'continent_text' => $row[7],
                    // Leave image and custom_images fields as null
                ]
            );
        }

        return true; // Import successful
    }
}
