<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UpdateMissingCountries extends Command
{
    protected $signature = 'countries:update-missing';
    protected $description = 'Update missing countries in the database';

    public function handle()
    {
        $tempFilePath = storage_path('app/temp_restcountries.json');

        // Save API response in chunks to avoid truncation
        $file = fopen($tempFilePath, 'w');
        $response = Http::sink($file)->timeout(120)->get('https://restcountries.com/v3.1/all');
        fclose($file);

        if ($response->failed()) {
            $this->error('Failed to fetch data from API.');
            return Command::FAILURE;
        }

        // Process the JSON file
        $rawData = file_get_contents($tempFilePath);
        $countries = json_decode($rawData, true);

        if (!$countries || json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        foreach ($countries as $country) {
            DB::table('wwde_countries')->insertOrIgnore([
                'continent_id' => $this->getContinentId($country['region'] ?? null),
                'title' => $country['name']['common'] ?? null,
                'alias' => Str::slug($country['name']['common'] ?? ''),
                'currency_code' => $country['currencies'][array_key_first($country['currencies'] ?? [])]['code'] ?? null,
                'currency_name' => $country['currencies'][array_key_first($country['currencies'] ?? [])]['name'] ?? null,
                'country_code' => $country['cca2'] ?? null,
                'country_text' => $country['name']['official'] ?? null,
                'population' => $country['population'] ?? null,
                'capital' => $country['capital'][0] ?? null,
                'area' => $country['area'] ?? null,
                'official_language' => array_values($country['languages'] ?? [])[0] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('Missing countries updated successfully.');

        unlink($tempFilePath);

        return Command::SUCCESS;
    }

    private function getContinentId($region)
    {
        $continents = [
            'Africa' => 1,
            'Antarctica' => 2,
            'Asia' => 3,
            'Europe' => 4,
            'North America' => 5,
            'Oceania' => 6,
            'South America' => 7,
        ];

        return $continents[$region] ?? null;
    }
}
