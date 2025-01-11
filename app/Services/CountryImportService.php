<?php

namespace App\Services;

use App\Models\WwdeCountry;
use App\Models\WwdeContinent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

                    // Bilder abrufen
                    $image1path = $this->getCityImage($countryName, 1, $status);
                    $image2path = $this->getCityImage($countryName, 2, $status);
                    $image3path = $this->getCityImage($countryName, 3, $status);
//dd($image1path, $image2path, $image3path);


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
            'image1_path' => $image1path,
            'image2_path' => $image2path,
            'image3_path' => $image3path,
        ]
    );
}


            return true; // Import erfolgreich

        } catch (\Exception $e) {
            Log::error("Fehler beim Import: {$e->getMessage()}");
            return false;
        }
    }




    private function getCityImage($city, $index, &$status)
    {
        // Cache-Schlüssel für das Bild
        $cacheKey = "country_image_{$city}_{$index}";

        // Überprüfen, ob das Bild bereits im Cache ist
        if (cache()->has($cacheKey)) {
            Log::info('Using cached image for city: ' . $city . ', index: ' . $index);
            return cache($cacheKey);
        }

        // Pixabay API-Schlüssel aus der Konfiguration holen
        $apiKey = config('services.pixabay.api_key', env('PIXABAY_API_KEY'));

        if (empty($apiKey)) {
            Log::error('Pixabay API key is missing.');
            $status = 'inactive'; // Kein API-Key, also Status auf inactive
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }

        // Pixabay API-URL
        $url = 'https://pixabay.com/api/';

        try {
            // API-Anfrage senden (nur 1 Bild pro Anfrage)
            $response = Http::get($url, [
                'key' => $apiKey,
                'q' => $city,
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'safesearch' => 'true',
                'per_page' => 5, // Maximal 5 Bilder abrufen
                //'per_page' => $index, // Nur so viele Bilder abrufen, wie der Index angibt
            ]);

            if ($response->successful()) {
                $images = $response->json()['hits'] ?? [];

                if (empty($images) || !isset($images[$index - 1])) {
                    Log::warning("No images found for city: {$city}, index: {$index}");
                    $status = 'pending'; // Keine Bilder gefunden
                    return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
                }

                // Bild-URL extrahieren
                $imageUrl = $images[$index - 1]['webformatURL'];

                // Speicherpfad erstellen (sanitize city name)
                $safeCityName = str_replace([' ', '/'], ['_', '-'], iconv('UTF-8', 'ASCII//TRANSLIT', $city));
                $directory = "uploads/images/locations/{$safeCityName}/";
                $fileName = "country_image_{$index}.jpg";

                // Verzeichnis erstellen, falls nicht vorhanden
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                // Bild speichern
                $imageContents = Http::get($imageUrl)->body();
                Storage::put($directory . $fileName, $imageContents);

                // Öffentliche URL generieren
                $storedImageUrl = Storage::url($directory . $fileName);

                // Bild-URL im Cache speichern (für 24 Stunden)
                cache([$cacheKey => $storedImageUrl], now()->addDay());

                Log::info("Image found, saved, and cached for city: {$city}, index: {$index}");
                return $storedImageUrl;
            } else {
                Log::error("Failed to fetch images from Pixabay API for city: {$city}, index: {$index}");
                $status = 'inactive'; // API-Fehler
                return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
            }
        } catch (\Exception $e) {
            Log::error("Error fetching image from Pixabay API for city: {$city}, index: {$index}. Error: {$e->getMessage()}");
            $status = 'inactive'; // Ausnahme, Status auf inactive
            return "https://via.placeholder.com/600x400?text=No+Image+for+{$city}";
        }
    }





}
