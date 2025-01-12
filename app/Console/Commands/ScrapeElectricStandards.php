<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\WwdeCountry;
use Illuminate\Console\Command;
use App\Models\ModElectricStandards;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeElectricStandards extends Command
{
    protected $signature = 'scrape:electric-standards';
    protected $description = 'Scrape electric standards and plug types from a website';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Guzzle Client erstellen
        $client = new Client();

        // Ziel-URL
        $url = 'https://www.iec.ch/world-plugs'; // Beispiel-URL

        try {
            // HTML von der URL abrufen
            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();

            // Mit DomCrawler parsen
            $crawler = new Crawler($html);

            // Länder-Items filtern und iterieren
            $crawler->filter('.country-item')->each(function (Crawler $node) {
                // Country Code aus dem Attribut "data-action-id" extrahieren
                $countryCode = $node->attr('data-action-id') ?? null;

                // Länderdaten extrahieren
                $countryName = $node->filter('.country-name')->text(); // Ländername
                $powerInfo = $node->filter('.country-content div')->eq(1)->text(); // Spannung und Frequenz

                // Steckertypen extrahieren
                $plugTypes = $node->filter('.country-content div span')->each(function (Crawler $plugNode) {
                    return trim($plugNode->text()); // Steckertypen (z. B. D, G)
                });

                // Debugging
                dump($countryCode, $countryName, $powerInfo, $plugTypes);

                $countryId = $this->getCountryIdByCode($countryCode);

                // Daten speichern
                ModElectricStandards::updateOrCreate(
                    ['country_code' => $countryCode], // Identifikation über country_code
                    [
                        'country_id' => $countryId,
                        'country_name' => $countryName,
                        'power' => trim($powerInfo),
                        'info' => implode(', ', $plugTypes),
                        'typ_a' => in_array('A', $plugTypes) ? 1 : 0,
                        'typ_b' => in_array('B', $plugTypes) ? 1 : 0,
                        'typ_c' => in_array('C', $plugTypes) ? 1 : 0,
                        'typ_d' => in_array('D', $plugTypes) ? 1 : 0,
                        'typ_g' => in_array('G', $plugTypes) ? 1 : 0,
                        'typ_h' => in_array('H', $plugTypes) ? 1 : 0,
                        'typ_i' => in_array('I', $plugTypes) ? 1 : 0,
                        'typ_j' => in_array('J', $plugTypes) ? 1 : 0,
                        'typ_k' => in_array('K', $plugTypes) ? 1 : 0,
                        'typ_l' => in_array('L', $plugTypes) ? 1 : 0,
                        'typ_m' => in_array('M', $plugTypes) ? 1 : 0,
                        'typ_n' => in_array('N', $plugTypes) ? 1 : 0,
                    ]
                );

                $this->info("Daten für $countryName ($countryCode) erfolgreich gespeichert.");
            });
        } catch (\Exception $e) {
            $this->error("Fehler beim Abrufen der Seite: " . $e->getMessage());
        }

        $this->info('Web-Scraping abgeschlossen.');
    }

    private function getCountryIdByName($countryName)
    {
        // Beispiel: Eine Mapping-Funktion, um Länder-IDs aus einem anderen Modell zu holen
        return WwdeCountry::whereRaw('LOWER(TRIM(title)) = ?', [strtolower(trim($countryName))])->value('id') ?? null;
    }

    private function getCountryIdByCode($countryCode)
    {
        return WwdeCountry::where('country_code', $countryCode)->value('id') ?? null;
    }
}
