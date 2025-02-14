<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateFlightData extends Command
{
    protected $signature = 'flights:update-data';
    protected $description = 'Holt korrekte IATA-Codes und Flughafendaten von Skyscanner und aktualisiert die Datenbank';

    public function handle()
    {
        $client = new Client([
            'timeout' => 10,  // Maximale Wartezeit für API-Requests
            'connect_timeout' => 5 // Maximale Zeit zum Verbindungsaufbau
        ]);

        // OpenFlights-Datei herunterladen, falls nicht vorhanden
        $this->downloadOpenFlightsData();

        // Alle Orte aus der Datenbank abrufen
        $locations = WwdeLocation::get();
        $this->info("Starte Update für " . count($locations) . " Orte...");

        foreach ($locations as $location) {
            $iataCode = strtoupper($location->iata_code);

            // 1️⃣ IATA-Code korrigieren, falls leer oder falsch
            if (empty($iataCode) || strlen($iataCode) > 3) {
                $iataCode = $this->fetchIataCode($client, $location);
                if ($iataCode) {
                    $location->update(['iata_code' => $iataCode]);
                    $this->info("🔄 Neuer IATA-Code für {$location->title}: {$iataCode}");
                } else {
                    Log::warning("⚠ Kein IATA-Code für {$location->title} gefunden");
                    continue; // Springt zur nächsten Stadt
                }
            }

            // 2️⃣ Skyscanner API nutzen für Entfernungen & Flugzeiten
            $apiUrl = "https://www.skyscanner.net/g/autosuggest-flights/UK/en-GB/{$iataCode}";

            try {
                $this->info("🔍 Abrufen von Daten für: {$location->title} ({$iataCode})...");

                $response = $client->request('GET', $apiUrl);
                $data = json_decode($response->getBody()->getContents(), true);

                if (!$data || !is_array($data)) {
                    Log::error("⚠ API-Antwort ungültig oder leer für {$location->title} ({$iataCode})");
                    continue;
                }

                // Verarbeite die API-Antwort
                $this->processApiResponse($data, $location);

                sleep(rand(2, 5));

            } catch (\GuzzleHttp\Exception\RequestException $e) {
                Log::error("❌ API Fehler für {$location->title} ({$iataCode}): " . $e->getMessage());
            } catch (\Exception $e) {
                Log::error("❌ Unerwarteter Fehler für {$location->title} ({$iataCode}): " . $e->getMessage());
            }
        }

        $this->info('🚀 Flughafendaten-Update abgeschlossen!');
    }

    /**
     * Verarbeitet die API-Antwort und aktualisiert die Datenbank.
     */
    private function processApiResponse($data, $location)
    {
        foreach ($data as $place) {
            // Überprüfe, ob der Eintrag einen gültigen IATA-Code hat
            if (!empty($place['IataCode'])) {
                $iataCode = $place['IataCode'];

                // Überprüfe, ob der IATA-Code mit dem gesuchten übereinstimmt
                if ($iataCode === $location->iata_code) {
                    // Extrahiere die Koordinaten
                    if (!empty($place['Location'])) {
                        list($lat, $lon) = explode(',', $place['Location']);

                        // Flugstunden & Entfernung berechnen
                        $dist = $this->calculateDistance($place['Location']);
                        $flightTime = $this->estimateFlightTime($place['Location']);

                        if ($dist === null || $flightTime === null) {
                            Log::warning("⚠ Berechnung fehlgeschlagen für {$location->title}");
                            continue;
                        }

                        // Datenbank-Update
                        $location->update([
                            'iata_code' => $iataCode,
                            'lat' => $lat,
                            'lon' => $lon,
                            'dist_from_FRA' => $dist,
                            'flight_hours' => $flightTime,
                            'stop_over' => 0,
                            'updated_at' => now()
                        ]);

                        $this->info("✅ Daten für {$location->title} aktualisiert: IATA-Code {$iataCode}, Koordinaten {$lat}, {$lon}, Entfernung {$dist} km, Flugzeit {$flightTime} h");
                        return true; // Erfolg
                    }
                }
            }
        }

        Log::warning("❌ Kein gültiger Eintrag für {$location->title} ({$location->iata_code}) in der API");
        return false; // Keine gültigen Daten gefunden
    }

    /**
     * Berechnet die Entfernung zwischen zwei geografischen Koordinaten in Kilometern.
     */
    private function calculateDistance($location)
    {
        // Koordinaten von Frankfurt am Main (FRA)
        $fraLat = 50.033333;
        $fraLon = 8.570556;

        // Zielkoordinaten extrahieren
        $coords = explode(',', $location);
        if (count($coords) != 2) {
            Log::warning("❌ Ungültige Koordinaten für Location: {$location}");
            return null;
        }

        $destLat = (float)trim($coords[0]);
        $destLon = (float)trim($coords[1]);

        // Haversine-Formel zur Berechnung der Entfernung
        $earthRadius = 6371; // Radius der Erde in Kilometern

        $latFrom = deg2rad($fraLat);
        $lonFrom = deg2rad($fraLon);
        $latTo = deg2rad($destLat);
        $lonTo = deg2rad($destLon);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        $distance = $angle * $earthRadius;

        return round($distance, 2); // Auf zwei Dezimalstellen runden
    }

    /**
     * Schätzt die Flugzeit basierend auf der Entfernung.
     */
    private function estimateFlightTime($location)
    {
        $distance = $this->calculateDistance($location);

        if ($distance === null) {
            return null;
        }

        // Durchschnittliche Fluggeschwindigkeit in km/h (z.B. 800 km/h)
        $averageSpeed = 800;

        // Flugzeit in Stunden berechnen
        $flightTime = $distance / $averageSpeed;

        return round($flightTime, 1); // Auf eine Dezimalstelle runden
    }

    /**
     * Holt den korrekten IATA-Code über Skyscanner oder OpenFlights.
     */
    private function fetchIataCode($client, $location)
    {
        $query = !empty($location->title) ? $location->title : "{$location->lat},{$location->lon}";
        $apiUrl = "https://www.skyscanner.net/g/autosuggest-flights/UK/en-GB/" . urlencode($query);

        try {
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data || !is_array($data)) {
                return $this->getIataFromOpenFlights($location);
            }

            foreach ($data as $place) {
                if (!empty($place['IataCode'])) {
                    return $place['IataCode'];
                }
            }

            return $this->getIataFromOpenFlights($location);
        } catch (\Exception $e) {
            Log::error("❌ Fehler bei IATA-Code Suche für {$location->title}: " . $e->getMessage());
            return $this->getIataFromOpenFlights($location);
        }
    }

    /**
     * OpenFlights-Datenbank automatisch herunterladen.
     */
    private function downloadOpenFlightsData()
    {
        $filePath = storage_path('openflights.csv');

        if (!file_exists($filePath)) {
            $this->info("📥 Lade OpenFlights-Datenbank herunter...");
            $url = "https://raw.githubusercontent.com/jpatokal/openflights/master/data/airports.dat";

            try {
                file_put_contents($filePath, file_get_contents($url));
                $this->info("✅ OpenFlights-Datenbank gespeichert.");
            } catch (\Exception $e) {
                Log::error("❌ Fehler beim Herunterladen der OpenFlights-Datenbank: " . $e->getMessage());
            }
        }
    }

    /**
     * Holt den IATA-Code aus der OpenFlights CSV-Datei.
     */
    private function getIataFromOpenFlights($location)
    {
        $filePath = storage_path('openflights.csv');

        if (!file_exists($filePath)) {
            Log::error("⚠ OpenFlights-Datenbank nicht gefunden.");
            return null;
        }

        $handle = fopen($filePath, "r");
        if ($handle) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $cityName = strtolower(trim($data[2])); // Spalte mit Stadtname
                $iataCode = strtoupper(trim($data[4])); // Spalte mit IATA-Code

                if ($cityName === strtolower($location->title) && strlen($iataCode) === 3) {
                    fclose($handle);
                    Log::info("✅ OpenFlights IATA-Code gefunden für {$location->title}: {$iataCode}");
                    return $iataCode;
                }
            }
            fclose($handle);
        }

        Log::warning("❌ Kein IATA-Code für {$location->title} in OpenFlights-Daten gefunden.");
        return null;
    }
}
