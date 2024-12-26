<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Library\WeatherApiClientLibrary;

class UpdateMissingCountriesByRegion extends Command
{
    protected $signature = 'countries:update-missing-by-region';
    protected $description = 'Update missing countries in the database by region';
    protected $weatherApi;

    public function __construct()
    {
        parent::__construct();
        $this->weatherApi = new WeatherApiClientLibrary();
    }

    public function handle()
    {
        $regions = [
            'Africa',
            'Americas',
            'Asia',
            'Europe',
            'Oceania',
            'Antarctic',
        ];

        foreach ($regions as $region) {
            $this->info("Fetching countries in the region: {$region}");

            $response = Http::timeout(120)
                ->withOptions([
                    'curl' => [
                        CURLOPT_BUFFERSIZE => 8192,
                    ],
                ])
                ->retry(5, 100)
                ->get("https://restcountries.com/v3.1/region/{$region}?fields=name,currencies,cca2,region,population,capital,area,languages,latlng");

            if ($response->failed()) {
                $this->error("API request failed for region: {$region} - " . $response->status());
                continue;
            }

            $countries = $response->json();

            foreach ($countries as $country) {
                $countryCode = $country['cca2'] ?? null;

                // Visumspflicht-Logik
                $visaFreeCountries = ['DE', 'FR', 'IT', 'ES', 'US', 'CA'];
                $countryVisumNeeded = !in_array($countryCode, $visaFreeCountries);
                $countryVisumMaxTime = $countryVisumNeeded ? null : '90 days';

                // Wetter- und Klimadaten
                $latitude = $country['latlng'][0] ?? null;
                $longitude = $country['latlng'][1] ?? null;
                $climateData = $this->getClimateData($latitude, $longitude);

                // Placeholder für Reisewarnungen
                $travelWarningId = $this->getTravelWarningId($countryCode);

                // Preisstufen-Logik
                $priceTendency = $this->getPriceTendency($country);

                DB::table('wwde_countries')->updateOrInsert(
                    ['country_code' => $countryCode],
                    array_merge([
                        'continent_id' => $this->getContinentId($region),
                        'title' => $country['name']['common'] ?? null,
                        'alias' => isset($country['name']['common']) ? Str::slug($country['name']['common']) : null,
                        'currency_code' => $this->getCurrencyCode($country),
                        'currency_name' => $this->getCurrencyName($country),
                        'country_text' => $country['name']['official'] ?? null,
                        'population' => $country['population'] ?? null,
                        'capital' => $this->getFirstElement($country['capital']),
                        'area' => $country['area'] ?? null,
                        'official_language' => $this->getOfficialLanguage($country),
                        'country_visum_needed' => $countryVisumNeeded,
                        'country_visum_max_time' => $countryVisumMaxTime,
                        'travelwarning_id' => $travelWarningId,
                        'price_tendency' => $priceTendency,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $climateData)
                );
            }

            $this->info("Successfully updated missing countries for region: {$region}");
        }

        $this->info('All missing countries have been updated successfully.');
    }

    private function getClimateData($latitude, $longitude)
    {
        if (!$latitude || !$longitude) {
            return [
                'count_climatezones' => 1,
                'climatezones_ids' => '0',
                'climatezones_lnam' => 'Unknown',
                'climatezones_details_lnam' => 'No data available.',
            ];
        }

        $weather = $this->weatherApi->fetchCurrentWeather($latitude, $longitude);

        if (!$weather) {
            return [
                'count_climatezones' => 1,
                'climatezones_ids' => '0',
                'climatezones_lnam' => 'Unknown',
                'climatezones_details_lnam' => 'No data available.',
            ];
        }

        $temperature = $weather['daily_temperature'];
        $rainyDays = $weather['rainy_days'];

        if ($temperature > 25 && $rainyDays < 3) {
            $climateZone = 'Tropical';
        } elseif ($temperature < 10) {
            $climateZone = 'Polar';
        } else {
            $climateZone = 'Temperate';
        }

        return [
            'count_climatezones' => 1,
            'climatezones_ids' => '1',
            'climatezones_lnam' => $climateZone,
            'climatezones_details_lnam' => "The country has a {$climateZone} climate with average daily temperatures of {$temperature}°C.",
        ];
    }

    private function getTravelWarningId($countryCode)
    {
        // Beispiel: Reisewarnungen verknüpfen
        $travelWarnings = [
            'AF' => 1, // Afghanistan
            'SY' => 2, // Syria
        ];

        return $travelWarnings[$countryCode] ?? null;
    }

    private function getPriceTendency($country)
    {
        $population = $country['population'] ?? 0;
        $area = $country['area'] ?? 1; // Um Division durch 0 zu vermeiden
        $populationDensity = $population / $area;

        if ($populationDensity > 500) {
            return 'Expensive';
        } elseif ($populationDensity > 100) {
            return 'Moderate';
        }

        return 'Affordable';
    }

    private function getContinentId($region)
    {
        $continents = [
            'Africa' => 1,
            'Americas' => 5,
            'Asia' => 3,
            'Europe' => 4,
            'Oceania' => 6,
            'Antarctic' => 2,
        ];

        return $continents[$region] ?? null;
    }

    private function getCurrencyCode($country)
    {
        if (isset($country['currencies']) && is_array($country['currencies'])) {
            return array_key_first($country['currencies']);
        }

        return null;
    }

    private function getCurrencyName($country)
    {
        if (isset($country['currencies']) && is_array($country['currencies']) && !empty($country['currencies'])) {
            $currency = array_values($country['currencies'])[0];
            return $currency['name'] ?? null;
        }

        return null;
    }

    private function getOfficialLanguage($country)
    {
        if (isset($country['languages']) && is_array($country['languages'])) {
            return array_values($country['languages'])[0] ?? null;
        }

        return null;
    }

    private function getFirstElement($array)
    {
        return is_array($array) && !empty($array) ? $array[0] : null;
    }
}
