<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\WwdeCountry;
use Illuminate\Console\Command;
use App\Models\ModElectricStandards;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;

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
            $crawler->filter('.country-item')->each(function (Crawler $node) use ($client) {
                // Country Code aus dem Attribut "data-action-id" extrahieren
                $countryCode = $node->attr('data-action-id') ?? null;

                // Länderdaten extrahieren
                $countryName = $node->filter('.country-name')->text(); // Ländername
                $powerInfo = $node->filter('.country-content div')->eq(1)->text(); // Spannung und Frequenz

                // Steckertypen extrahieren
                $plugTypes = $node->filter('.country-content div span')->each(function (Crawler $plugNode) {
                    return trim($plugNode->text()); // Steckertypen (z. B. "A", "B")
                });

                $baseImageUrl = 'https://www.iec.ch/themes/custom/iec/images/world-plugs/types/';

                // URLs für Bilder basierend auf Typen erstellen
                $plugImages = array_map(function ($plugType) use ($baseImageUrl, $client) {
                    $images = [
                        'plug' => $this->downloadImage($client, "{$baseImageUrl}{$plugType}/{$plugType}_3d_plug_l.png"),
                        'socket' => $this->downloadImage($client, "{$baseImageUrl}{$plugType}/{$plugType}_3d_sock_l.png"),
                        'plug_dia' => $this->downloadImage($client, "{$baseImageUrl}{$plugType}/{$plugType}_dia_plug_l.png"),
                        'socket_dia' => $this->downloadImage($client, "{$baseImageUrl}{$plugType}/{$plugType}_dia_sock_l.png"),
                    ];

                    return array_filter($images); // Nur vorhandene Bilder zurückgeben
                }, $plugTypes);

                // Bilder flach in eine Liste umwandeln (lokale Pfade speichern)
                $flatPlugImages = [];
                foreach ($plugImages as $images) {
                    $flatPlugImages = array_merge($flatPlugImages, $images);
                }

                dump($flatPlugImages); // Debug: Prüfen der gespeicherten lokalen Pfade

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
                        'plug_images' => implode(',', $flatPlugImages),
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

    private function getFullUrl($url)
    {
        $baseUrl = 'https://www.iec.ch';
        return parse_url($url, PHP_URL_SCHEME) ? $url : $baseUrl . '/' . ltrim($url, '/');
    }

    private function downloadImage(Client $client, $url)
    {
        try {
            $response = $client->get($url);
            $imageName = basename($url);
            $path = 'plugs/' . $imageName;

            // Bild speichern
            if (!Storage::disk('public')->exists('plugs')) {
                Storage::disk('public')->makeDirectory('plugs');
            }

            $localPath = Storage::disk('public')->put($path, $response->getBody());
            $this->info("Bild $imageName erfolgreich gespeichert.");

            return Storage::url($path); // Lokaler Pfad zurückgeben
        } catch (\Exception $e) {
            $this->warn("Bild konnte nicht heruntergeladen werden: $url");
            return null;
        }
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
