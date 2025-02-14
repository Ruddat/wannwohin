<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchFlightPrices extends Command
{
    protected $signature = 'flights:fetch-prices';
    protected $description = 'Holt aktuelle Flugpreise von Bing und speichert sie in der Datenbank';

    public function handle()
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ]
        ]);

        // Alle Locations mit IATA-Code aus der Datenbank abrufen
        $locations = WwdeLocation::whereNotNull('iata_code')->get();

        foreach ($locations as $location) {
            // Bing-Such-URL generieren
            $origin = 'FRA'; // Abflughafen Frankfurt
            $destination = $location->iata_code;
            $searchUrl = "https://www.bing.com/search?q=Flug+{$origin}+nach+{$destination}";

            try {
                // Bing-Suche abrufen
                $response = $client->request('GET', $searchUrl);
                $html = $response->getBody()->getContents();

                // Debugging: HTML speichern, um später Probleme zu analysieren
                //file_put_contents(storage_path("debug_flight_{$destination}.html"), $html);

                // Preis mit Regex aus HTML extrahieren
                preg_match('/(\d{2,4})\s*€/', $html, $matches);
                $price = $matches[1] ?? null;

                if ($price) {
                    // Flugpreis in der Datenbank speichern
                    $location->update([
                        'price_flight' => $price,
                        'updated_at' => now(),
                    ]);

                    $this->info("Flugpreis für {$location->title} ({$destination}) aktualisiert: {$price} EUR");
                } else {
                    $this->warn("Kein Preis gefunden für {$location->title} ({$destination})");
                }

                // Wartezeit, um Scraping-Blockaden zu vermeiden
                sleep(rand(3, 7)); // 3-7 Sekunden Pause zwischen Requests

            } catch (\Exception $e) {
                Log::error("Fehler beim Abrufen der Daten für {$destination}: " . $e->getMessage());
                $this->error("Fehler beim Abrufen der Daten für {$destination}");
            }
        }

        $this->info('Flugpreise erfolgreich aktualisiert.');
    }
}
