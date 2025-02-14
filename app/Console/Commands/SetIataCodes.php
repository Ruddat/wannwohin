<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetIataCodes extends Command
{
    protected $signature = 'iata-codes:set';
    protected $description = 'Setzt IATA-Codes für alle Orte in der Datenbank';

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
        $this->info("Starte IATA-Code-Update für " . count($locations) . " Orte...");

        foreach ($locations as $location) {
            $iataCode = strtoupper($location->iata_code);

            // Überspringe Orte, die bereits einen gültigen IATA-Code haben
            if (!empty($iataCode) && strlen($iataCode) === 3) {
                $this->info("✅ {$location->title} hat bereits einen gültigen IATA-Code: {$iataCode}");
                continue;
            }

            // Versuche, den IATA-Code über Skyscanner zu ermitteln
            $iataCode = $this->fetchIataCodeFromSkyscanner($client, $location);

            // Fallback: Verwende die OpenFlights-Datenbank
            if (empty($iataCode)) {
                $iataCode = $this->getIataFromOpenFlights($location);
            }

            // Wenn ein IATA-Code gefunden wurde, aktualisiere die Datenbank
            if ($iataCode) {
                $location->update(['iata_code' => $iataCode]);
                $this->info("🔄 IATA-Code für {$location->title} aktualisiert: {$iataCode}");
            } else {
                Log::warning("⚠ Kein IATA-Code für {$location->title} gefunden");
            }

            // Kurze Pause, um die API nicht zu überlasten
            sleep(rand(2, 5));
        }

        $this->info('🚀 IATA-Code-Update abgeschlossen!');
    }

    /**
     * Holt den korrekten IATA-Code über Skyscanner.
     */
    private function fetchIataCodeFromSkyscanner($client, $location)
    {
        $query = !empty($location->title) ? $location->title : "{$location->lat},{$location->lon}";
        $apiUrl = "https://www.skyscanner.net/g/autosuggest-flights/UK/en-GB/" . urlencode($query);

        try {
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data || !is_array($data)) {
                return null;
            }

            foreach ($data as $place) {
                if (!empty($place['IataCode'])) {
                    return $place['IataCode'];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error("❌ Fehler bei IATA-Code Suche für {$location->title}: " . $e->getMessage());
            return null;
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
