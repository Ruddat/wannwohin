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

    public function handle()
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ],
            'timeout' => 10,
        ]);

        $url = 'https://www.iec.ch/world-plugs';

        try {
            $response = $client->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $crawler->filter('.country-item')->each(function (Crawler $node) use ($client) {
                try {
                    $countryCode = $node->attr('data-action-id') ?? null;
                    $countryName = $node->filter('.country-name')->count() > 0
                        ? $node->filter('.country-name')->text()
                        : null;
                    $powerInfo = $node->filter('.country-content div')->eq(1)->count() > 0
                        ? $node->filter('.country-content div')->eq(1)->text()
                        : null;

                    $plugTypes = $node->filter('.country-content div span')->each(function (Crawler $plugNode) {
                        return trim($plugNode->text());
                    });

                    $plugTypes = array_unique($plugTypes); // Duplikate entfernen

                    $baseImageUrl = 'https://www.iec.ch/themes/custom/iec/images/world-plugs/types/';
                    $plugImages = array_map(function ($plugType) use ($baseImageUrl, $client) {
                        return [
                            'plug' => $this->downloadImage($client, "{$baseImageUrl}{$plugType}/{$plugType}_3d_plug_l.png"),
                            'socket' => $this->downloadImage($client, "{$baseImageUrl}{$plugType}/{$plugType}_3d_sock_l.png"),
                        ];
                    }, $plugTypes);

                    $flatPlugImages = [];
                    foreach ($plugImages as $images) {
                        $flatPlugImages = array_merge($flatPlugImages, array_filter($images));
                    }

                    $countryId = $this->getCountryIdByCode($countryCode);

                    ModElectricStandards::updateOrCreate(
                        ['country_code' => $countryCode],
                        [
                            'country_id' => $countryId,
                            'country_name' => $countryName,
                            'power' => $powerInfo ? trim($powerInfo) : null,
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

                    $this->info("Data for {$countryName} ({$countryCode}) saved successfully.");
                } catch (\Exception $e) {
                    $this->warn("Failed to process a country: " . $e->getMessage());
                }
            });
        } catch (\Exception $e) {
            $this->error("Failed to fetch the page: " . $e->getMessage());
        }

        $this->info('Scraping completed.');
    }

    private function downloadImage(Client $client, $url)
    {
        try {
            $response = $client->get($url);
            $imageName = basename($url);
            $path = 'plugs/' . $imageName;

            if (!Storage::disk('public')->exists('plugs')) {
                Storage::disk('public')->makeDirectory('plugs');
            }

            Storage::disk('public')->put($path, $response->getBody());
            $this->info("Image $imageName downloaded successfully.");

            return Storage::url($path);
        } catch (\Exception $e) {
            $this->warn("Failed to download image: $url");
            return null;
        }
    }

    private function getCountryIdByCode($countryCode)
    {
        return WwdeCountry::where('country_code', $countryCode)->value('id') ?? null;
    }
}
