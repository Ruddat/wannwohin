<?php

namespace App\Services;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Log;

class LocationImportService
{
    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        $this->geocodeService = $geocodeService;
    }

    public function import(string $filePath): bool
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
//dd($rows);


            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue; // Überspringe die Header-Zeile
                }

                // Hole den Stadtnamen aus der Datei
                $cityName = trim($row[3] ?? ''); // Spalte mit dem Stadtnamen

//dd($cityName);

                if (empty($cityName)) {
                    Log::warning("Fehlender Stadtname in Zeile {$index}");
                    continue; // Überspringe, wenn kein Stadtname vorhanden ist
                }

                try {
                    // Verwende den GeocodeService, um das Land basierend auf dem Stadtnamen zu finden
                    $result = $this->geocodeService->searchByAddress($cityName);
//dd($result);
                    if (empty($result)) {
                        Log::warning("Kein Ergebnis für Stadt '{$cityName}' in Zeile {$index}");
                        continue;
                    }

                    // Hole Land und Länder-Code aus dem Ergebnis
                    $countryName = $result[0]['address']['country'] ?? null;
                    $countryCode = $result[0]['address']['country_code'] ?? null;
//dd($countryName, $countryCode);
                    if (!$countryName) {
                        Log::warning("Kein Land gefunden für Stadt '{$cityName}' in Zeile {$index}");
                        continue;
                    }

                    // Finde das passende Land in der Datenbank
                    $country = WwdeCountry::where('country_code', strtoupper($countryCode))
                        ->orWhere('title', $countryName)
                        ->first();
//dd($country);
                    if (!$country) {
                        Log::warning("Kein Land in der Datenbank gefunden für '{$countryName}' (Code: {$countryCode})");
                        continue;
                    }

                    // Hole die Kontinent-ID aus der Beziehung
                    $continentId = $country->continent_id;
//dd($continentId);
                    // Speichere die Location
                    WwdeLocation::updateOrCreate(
                        ['iata_code' => $row[4] ?? null], // IATA-Code als eindeutiger Wert
                        [
                            'title' => $cityName,
                            'country_id' => $country->id,
                            'continent_id' => $continentId,
                            'lat' => $result[0]['lat'] ?? null,
                            'lon' => $result[0]['lon'] ?? null,
                            // Weitere Felder ...
                            'alias' => $row[4],
                            'bundesstaat_long' => $row[12],
                            'bundesstaat_short' => $row[13],
                            'no_city_but' => $row[14],
                            'population' => 0,

                            'list_beach' => $row[15],
                            'list_citytravel' => $row[16],
                            'list_sports' => $row[17],
                            'list_island' => $row[18],
                            'list_culture' => $row[19],
                            'list_nature' => $row[20],
                            'list_watersport' => $row[21],
                            'list_wintersport' => $row[22],
                            'list_mountainsport' => $row[23],
                            'list_biking' => $row[24],
                            'list_fishing' => $row[25],
                            'list_amusement_park' => $row[26],
                            'list_water_park' => $row[27],
                         //   'list_animal_park' => $listAnimalPark, // Verwende den Standardwert
                            'best_traveltime' => $row[29],

                            'text_pic1' => $row[30],
                            'text_pic2' => $row[31],
                            'text_pic3' => $row[32],

                            'text_headline' => isset($row[33]) ? substr($row[33], 0, 255) : null,
                            'text_short' => $row[34],
                            'text_location_climate' => $row[35],
                            'text_what_to_do' => $row[36],
                            'text_best_traveltime' => $row[37],
                            'text_sports' => $row[38],
                            'text_amusement_parks' => $row[39],
                           // 'climate_details_id' => $climateDetailsId, // Konvertierter Wert
                            'climate_lnam' => $row[41],
                            'climate_details_lnam' => $row[42],
                            'price_flight' => $row[43],
                            'range_flight' => $row[44],
                            'price_hotel' => $row[45],
                            'range_hotel' => $row[46],
                            'price_rental' => $row[47] ?? null,
                            'range_rental' => $row[48] ?? null,
                            'price_travel' => $row[49],
                            'range_travel' => $row[50],
                            'finished' => 1, // Immer auf 1 setzen
                          //  'best_traveltime_json' => $bestTravelTimeJson, // Verarbeitetes JSON-Array mit Monatsnamen
                            'panorama_text_and_style' => $row[53],
                            'time_zone' => $row[54],
                            'lat_new' => $result[0]['lat'] ?? null,
                            'lon_new' => $result[0]['lon'] ?? null,
                            'status' => 'active', // Standardstatus
                        ]
                    );

                    Log::info("Location erfolgreich importiert: {$cityName} (Land: {$countryName})");

                } catch (\Exception $e) {
                    Log::error("Fehler bei Geocoding für Stadt '{$cityName}' in Zeile {$index}: " . $e->getMessage());
                }

                // Pause von 1 Sekunde, um API-Limits einzuhalten
               // sleep(1);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Fehler beim Import: " . $e->getMessage());
            return false;
        }
    }
}
