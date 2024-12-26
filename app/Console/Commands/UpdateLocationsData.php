<?php

namespace App\Console\Commands;

use App\Services\GeocodeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UpdateLocationsData extends Command
{
    protected $signature = 'locations:update-data';
    protected $description = 'Fetch and update location data from multiple APIs';

    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        parent::__construct();
        $this->geocodeService = $geocodeService;
    }

    public function handle()
    {
        $this->info('Starting data update...');

        $this->updateCountries();
        $this->updateCities();
        $this->updateDistances();

        $this->info('Data update completed successfully.');
    }

    private function updateCountries()
    {
        $this->info('Fetching country data...');

        $response = Http::timeout(120)
            ->retry(5, 100)
            ->get('https://restcountries.com/v3.1/all?fields=name,currencies,cca2,region,population,capital,area,languages');

        if ($response->failed()) {
            $this->error('Failed to fetch country data.');
            return;
        }

        $countries = $response->json();

        foreach ($countries as $country) {
            $countryCode = $country['cca2'] ?? null;

            DB::table('wwde_countries')->updateOrInsert(
                ['country_code' => $countryCode],
                [
                    'continent_id' => $this->getContinentId($country['region'] ?? null),
                    'title' => $country['name']['common'] ?? null,
                    'alias' => isset($country['name']['common']) ? Str::slug($country['name']['common']) : null,
                    'currency_code' => $this->getCurrencyCode($country),
                    'currency_name' => $this->getCurrencyName($country),
                    'country_text' => $country['name']['official'] ?? null,
                    'population' => $country['population'] ?? null,
                    'capital' => $this->getFirstElement($country['capital'] ?? []),
                    'area' => $country['area'] ?? null,
                    'official_language' => $this->getOfficialLanguage($country),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Country data updated.');
    }

    private function updateCities()
    {
        $this->info('Fetching city data...');

        // Abrufen aller Länder mit Hauptstädten aus der `wwde_countries`-Tabelle
        $countries = DB::table('wwde_countries')
            ->whereNotNull('capital')
            ->whereNotNull('country_code')
            ->get(['id', 'country_code', 'capital', 'continent_id']); // continent_id hinzugefügt

        foreach ($countries as $country) {
            $this->info("Processing capital city for country: {$country->capital} ({$country->country_code})");

            // Prüfen, ob die Hauptstadt existiert
            $existingCity = DB::table('wwde_locations')->where('title', $country->capital)->first();

            if (!$existingCity) {
                // API-Aufruf, um Koordinaten für die Hauptstadt zu erhalten
                $geocodeService = app(\App\Services\GeocodeService::class);
                $geocodeData = $geocodeService->searchByAddress($country->capital);

                $lat = $geocodeData['lat'] ?? null;
                $lon = $geocodeData['lon'] ?? null;

                // Einfügen der neuen Location
                DB::table('wwde_locations')->updateOrInsert(
                    ['title' => $country->capital],
                    [
                        'alias' => Str::slug($country->capital),
                        'country_id' => $country->id,
                        'continent_id' => $country->continent_id,
                        'lat' => $lat,
                        'lon' => $lon,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $this->info("Added new capital city: {$country->capital} ({$country->country_code})");
            } else {
                // Aktualisieren der vorhandenen Daten
                DB::table('wwde_locations')->where('id', $existingCity->id)->update([
                    'continent_id' => $country->continent_id,
                    'country_id' => $country->id,
                    'updated_at' => now(),
                ]);

                $this->info("Updated existing capital city: {$country->capital}");
            }
        }

        $this->info('City data updated.');
    }


    private function updateDistances()
    {
        $this->info('Calculating distances between cities...');

        $locations = DB::table('wwde_locations')->get(['id', 'lat', 'lon']);

        foreach ($locations as $fromLocation) {
            foreach ($locations as $toLocation) {
                if ($fromLocation->id === $toLocation->id) {
                    continue;
                }

                if (is_null($fromLocation->lat) || is_null($fromLocation->lon) ||
                    is_null($toLocation->lat) || is_null($toLocation->lon)) {
                    $this->error("Missing coordinates for {$fromLocation->id} or {$toLocation->id}");
                    continue;
                }

                $distance = $this->haversineGreatCircleDistance(
                    $fromLocation->lat,
                    $fromLocation->lon,
                    $toLocation->lat,
                    $toLocation->lon
                );

                DB::table('wwde_distances')->updateOrInsert(
                    [
                        'from_location_id' => $fromLocation->id,
                        'to_location_id' => $toLocation->id,
                    ],
                    [
                        'distance' => $distance,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $this->info("Distance between {$fromLocation->id} and {$toLocation->id}: {$distance} km");
            }
        }

        $this->info('Distances updated successfully.');
    }

    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
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
