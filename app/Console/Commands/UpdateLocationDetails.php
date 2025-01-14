<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WwdeLocation;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateLocationDetails extends Command
{
    protected $signature = 'update:location-details';
    protected $description = 'Update ISO codes and timezone for locations based on title, latitude, and longitude.';
    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        parent::__construct();
        $this->geocodeService = $geocodeService;
    }

    public function handle(): void
    {
        $locations = WwdeLocation::whereNull('iso2')
            ->orWhereNull('iso3')
            ->orWhereNull('time_zone')
            ->get();

        foreach ($locations as $location) {
            try {
                // Hole Geocode-Daten
                $geocodeData = $this->geocodeService->searchByCoordinates($location->lat, $location->lon);

                if (!empty($geocodeData['address'])) {
                    $countryName = $geocodeData['address']['country'] ?? null;

                    // Hole ISO-Codes
                    $isoCodes = $this->getIsoCodes($countryName);

                    // Hole Zeitzone
                    $timeZone = $this->getTimeZone($location->lat, $location->lon);
//dd($timeZone);

                    // Aktualisiere die Location
                    $location->update([
                        'iso2' => $isoCodes['iso2'] ?? null,
                        'iso3' => $isoCodes['iso3'] ?? null,
                        'time_zone' => $timeZone ?? null,
                    ]);

                    $this->info("Updated location: {$location->title}");
                } else {
                    $this->warn("No geocode data found for location: {$location->title}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to update location: {$location->title}. Error: {$e->getMessage()}");
            }
        }

        $this->info('All locations have been updated.');
    }

    /**
     * Hole ISO-Codes für ein Land.
     */
    private function getIsoCodes(string $countryName): array
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

    /**
     * Hole Zeitzone basierend auf Koordinaten.
     */
    private function getTimeZone(float $lat, float $lon): ?string
    {
        try {
            // API-Request an timeapi.io
            $response = Http::get("https://timeapi.io/api/time/current/coordinate", [
                'latitude' => $lat,
                'longitude' => $lon,
            ]);

            // Überprüfe, ob die Antwort erfolgreich ist und die Zeitzone enthält
            if ($response->successful() && !empty($response->json('timeZone'))) {
                return $response->json('timeZone');
            }

            // Loggen, falls keine Zeitzone gefunden wurde
            Log::error("Failed to fetch timezone for coordinates: ({$lat}, {$lon}). Response: " . $response->body());
        } catch (\Exception $e) {
            // Fehlerbehandlung
            Log::error("Error fetching timezone: " . $e->getMessage());
        }

        return null; // Rückgabe von null, falls kein Ergebnis
    }
}
