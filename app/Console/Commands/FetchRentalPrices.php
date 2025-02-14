<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchRentalPrices extends Command
{
    protected $signature = 'rentalcars:fetch-prices';
    protected $description = 'Holt Mietwagenpreise von Bing und speichert sie in der Datenbank';

    public function handle()
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ]
        ]);

        // Alle Standorte mit IATA-Code oder Städtenamen abrufen
        $locations = WwdeLocation::whereNotNull('iata_code')->orWhereNotNull('title')->get();

        foreach ($locations as $location) {
            // Bing-Such-URL für Mietwagenpreise generieren
            $searchQuery = "Mietwagen {$location->title} Preise";
            $searchUrl = "https://www.bing.com/search?q=" . urlencode($searchQuery);

            try {
                // Bing-Suche abrufen
                $response = $client->request('GET', $searchUrl);
                $html = $response->getBody()->getContents();

                // Debugging: HTML speichern, um später zu analysieren
                //file_put_contents(storage_path("debug_rental_{$location->iata_code}.html"), $html);

                // Preis mit Regex extrahieren
                preg_match('/(\d{2,4})\s*€/', $html, $matches);
                $price = $matches[1] ?? null;

                if ($price) {
                    // Mietwagenpreis in der Datenbank speichern
                    $location->update([
                        'price_rental' => $price,
                        'updated_at' => now(),
                    ]);

                    $this->info("Mietwagenpreis für {$location->title} aktualisiert: {$price} EUR");
                } else {
                    $this->warn("Kein Mietwagenpreis gefunden für {$location->title}");
                }

                // Wartezeit, um Scraping-Blockaden zu vermeiden
                sleep(rand(3, 7));

            } catch (\Exception $e) {
                Log::error("Fehler beim Abrufen der Mietwagenpreise für {$location->title}: " . $e->getMessage());
                $this->error("Fehler für {$location->title}");
            }
        }

        $this->info('Mietwagenpreise erfolgreich aktualisiert.');
    }
}
