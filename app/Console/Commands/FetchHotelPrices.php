<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchHotelPrices extends Command
{
    protected $signature = 'hotels:fetch-prices';
    protected $description = 'Holt Hotelpreise von Bing und speichert sie in der Datenbank mit Preisspanne';

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
            // Bing-Such-URL für günstige Hotels generieren
            $searchQuery = "Günstige Hotels {$location->title} ab 30€";
            $searchUrl = "https://www.bing.com/search?q=" . urlencode($searchQuery);

            try {
                // Bing-Suche abrufen
                $response = $client->request('GET', $searchUrl);
                $html = $response->getBody()->getContents();

                // Debugging: HTML speichern, um später zu analysieren
                // file_put_contents(storage_path("debug_hotel_{$location->iata_code}.html"), $html);

                // HTML mit DOMDocument parsen
                libxml_use_internal_errors(true);
                $doc = new \DOMDocument();
                $doc->loadHTML($html);
                libxml_clear_errors();

                $xpath = new \DOMXPath($doc);

                // Hotelpreise mit XPath suchen
                $prices = $xpath->query("//span[contains(@class, 'ppa_slide_price')]");

                $priceList = [];
                foreach ($prices as $price) {
                    $cleanPrice = preg_replace('/[^\d]/', '', $price->nodeValue); // Nur Zahlen behalten
                    if ($cleanPrice >= 30 && $cleanPrice <= 350) { // Nur realistische Preise
                        $priceList[] = $cleanPrice;
                    }
                }

                // Günstigsten und teuersten Preis speichern
                $minPrice = !empty($priceList) ? min($priceList) : null;
                $maxPrice = !empty($priceList) ? max($priceList) : null;

                if ($minPrice && $maxPrice) {
                    // Hotelpreise in der Datenbank speichern
                    $location->update([
                        'price_hotel' => $minPrice,
                        'range_hotel' => $maxPrice,
                        'updated_at' => now(),
                    ]);

                    $this->info("Hotelpreis für {$location->title} aktualisiert: {$minPrice} - {$maxPrice} EUR");
                } else {
                    $this->warn("Kein gültiger Hotelpreis gefunden für {$location->title}");
                }

                // Wartezeit, um Scraping-Blockaden zu vermeiden
                sleep(rand(3, 7));

            } catch (\Exception $e) {
                Log::error("Fehler beim Abrufen der Hotelpreise für {$location->title}: " . $e->getMessage());
                $this->error("Fehler für {$location->title}");
            }
        }

        $this->info('Hotelpreise erfolgreich aktualisiert.');
    }
}
