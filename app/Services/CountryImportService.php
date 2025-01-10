<?php

namespace App\Services;

use App\Models\WwdeCountry;
use App\Models\WwdeContinent;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CountryImportService
{
    public function import(string $filePath): bool
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Alle Kontinente aus der Datenbank holen und als Map speichern (nach `iso2`, `iso3` und `title`)
// Alle Kontinente aus der Datenbank holen und als Map speichern (nach `iso2`, `iso3` und `title`)
$continents = WwdeContinent::all()->keyBy(function ($item) {
    //return strtolower(trim($item->iso2)) . '|' . strtolower(trim($item->iso3));
    return strtolower(trim($item->iso2));
});

//dd($continents);

foreach ($rows as $index => $row) {
    if ($index === 0) {
        continue; // Überspringe die Header-Zeile
    }

    // `continent_iso_2`, `continent_iso_3` und `title` aus der Excel-Zeile in Kleinbuchstaben konvertieren
    $continentIso2 = strtolower(trim($row[20] ?? ''));
    $continentIso3 = strtolower(trim($row[21] ?? ''));
    $continentName = strtolower(trim($row[1] ?? ''));
//dd($continentName, $continentIso2, $continentIso3);
    // Generiere den Schlüssel zur Suche
    //$continentKey = $continentIso2 . '|' . $continentIso3;
    $continentKey = $continentIso2;
    // Kontinent-ID bestimmen (oder null, falls nicht gefunden)
    $continentId = $continents[$continentKey]->id ?? null;

//dd($continentId);

    if (!$continentId) {
        Log::warning("Kontinent nicht gefunden für '{$continentKey}' in Zeile {$index}.");
        continue; // Überspringe Länder ohne gültigen Kontinent
    }

    // Länder-Daten validieren und speichern
    $countryName = trim($row[2] ?? '');
    if (empty($countryName)) {
        Log::warning("Fehlender Landesname in Zeile {$index}.");
        continue;
    }

    WwdeCountry::updateOrCreate(
        ['alias' => $row[3] ?? strtolower(str_replace(' ', '-', $countryName))],
        [
            'continent_id' => $continentId,
            'title' => $countryName,
            'currency_code' => $row[4] ?? null,
            'currency_name' => $row[5] ?? null,
            'country_code' => $row[6] ?? null,
            'country_text' => $row[7] ?? null,
            'currency_conversion' => $row[8] ?? null,
            'population' => $row[9] ?? null,
            'capital' => $row[10] ?? null,
            'population_capital' => $row[11] ?? null,
            'area' => $row[12] ?? null,
            'official_language' => $row[13] ?? null,
            'language_ezmz' => $row[14] ?? null,
            'bsp_in_USD' => $row[15] ?? null,
            'life_expectancy_m' => $row[16] ?? null,
            'life_expectancy_w' => $row[17] ?? null,
            'population_density' => $row[18] ?? null,
            'country_iso_3' => $row[19] ?? null,
            'continent_iso_2' => $continentIso2,
            'continent_iso_3' => $continentIso3,
            'country_visum_needed' => isset($row[22]) ? (bool) $row[22] : null,
            'country_visum_max_time' => $row[23] ?? null,
            'count_climatezones' => $row[24] ?? null,
            'climatezones_ids' => $row[25] ?? null,
            'climatezones_lnam' => $row[26] ?? null,
            'climatezones_details_lnam' => $row[27] ?? null,
            'artikel' => $row[28] ?? null,
            'travelwarning_id' => $row[29] ?? null,
            'price_tendency' => $row[30] ?? null,
        ]
    );
}


            return true; // Import erfolgreich

        } catch (\Exception $e) {
            Log::error("Fehler beim Import: {$e->getMessage()}");
            return false;
        }
    }
}
