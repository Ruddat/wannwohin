<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Models\ModTravelWarning;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeTravelWarnings extends Command
{
    protected $signature = 'scrape:travel-warnings';
    protected $description = 'Scrape travel warnings from travel.state.gov';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = 'https://travel.state.gov/content/travel/en/traveladvisories/traveladvisories.html';
        $client = new Client();

        try {
            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();

            // Debugging: HTML speichern
            file_put_contents(storage_path('app/scraped_travel_warnings.html'), $html);

            $crawler = new Crawler($html);

            $crawler->filter('div.table-data table tbody tr')->each(function (Crawler $row) {
                // Skip header row
                if ($row->filter('th')->count() > 0) {
                    return;
                }

                try {
                    // Daten extrahieren
                    $advisory = $row->filter('td')->eq(0)->text();

                    // Bereinigen des Ländernamens
                    $cleanedCountryName = trim(str_replace('Travel Advisory', '', $advisory));

                    // ISO-Codes abrufen
                    $isoCodes = $this->getIsoCodes($cleanedCountryName);

                    $level = $row->filter('td')->eq(1)->text();
                    $updatedDate = $row->filter('td')->eq(2)->text();

                    // Debugging: Prüfen der extrahierten Daten
                    $this->info("Advisory: $advisory");
                    $this->info("Country: $cleanedCountryName");
                    $this->info("Level: $level");
                    $this->info("Updated Date: $updatedDate");

                    // Link extrahieren
                    $advisoryLink = $row->filter('td a')->attr('href');
                    $fullLink = 'https://travel.state.gov' . $advisoryLink;

                    // Datum formatieren
                    try {
                        $parsedDate = Carbon::parse($updatedDate);
                    } catch (\Exception $e) {
                        $this->warn("Invalid date format for $advisory: $updatedDate");
                        $parsedDate = null;
                    }

                    // Daten in die Datenbank speichern
                    ModTravelWarning::updateOrCreate(
                        ['country' => $cleanedCountryName],
                        [
                            'iso2' => $isoCodes['iso2'],
                            'iso3' => $isoCodes['iso3'],
                            'severity' => $level,
                            'issued_at' => $parsedDate,
                            'url' => $fullLink,
                        ]
                    );

                    $this->info("Added/Updated: $advisory");
                } catch (\Exception $e) {
                    $this->warn("Error processing row: " . $e->getMessage());
                }
            });

            $this->info('Travel warnings successfully scraped and saved.');
        } catch (\Exception $e) {
            $this->error('Error scraping travel warnings: ' . $e->getMessage());
        }
    }

    /**
     * Ruft die ISO-Codes für ein Land ab.
     */
    function getIsoCodes(string $countryName)
    {
        $response = Http::get('https://restcountries.com/v3.1/name/' . urlencode($countryName));
        if ($response->successful() && $response->json()) {
            $data = $response->json()[0];
            return [
                'iso2' => $data['cca2'] ?? null,
                'iso3' => $data['cca3'] ?? null,
            ];
        }
        return ['iso2' => null, 'iso3' => null];
    }
}
