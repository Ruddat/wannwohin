<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WwdeLocation;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UpdateLocationDetails extends Command
{
    protected $signature = 'update:location-details
                            {--limit=100 : Maximum number of locations to process}
                            {--field= : Specific field to update (iso2, iso3, time_zone)}
                            {--reset : Reset the last processed ID and start from the beginning}
                            {--force-timezone : Force update of timezone even if already set}';
    protected $description = 'Update ISO codes and timezone for active locations based on coordinates, continuing from last processed location.';

    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        parent::__construct();
        $this->geocodeService = $geocodeService;
    }

    public function handle(): void
    {
        $limit = (int) $this->option('limit');
        $field = $this->option('field');
        $reset = $this->option('reset');
        $forceTimezone = $this->option('force-timezone');

        // Letzten bearbeiteten ID aus Cache holen oder zurücksetzen
        $lastProcessedId = $reset ? 0 : Cache::get('update_location_details_last_id', 0);
        if ($reset) {
            Cache::forget('update_location_details_last_id');
            $this->info('Reset last processed ID to start from beginning.');
        }

        // Abfrage dynamisch anpassen
        $query = WwdeLocation::where('status', 'active')
                             ->where('id', '>', $lastProcessedId)
                             ->orderBy('id');
        if ($field) {
            if ($field !== 'time_zone' || !$forceTimezone) {
                $query->whereNull($field);
            }
        } else {
            $query->where(function ($q) use ($forceTimezone) {
                $q->whereNull('iso2')
                  ->orWhereNull('iso3')
                  ->orWhereNull('time_zone');
                if ($forceTimezone) {
                    $q->orWhereNotNull('time_zone'); // Auch Locations mit Zeitzone einschließen
                }
            });
        }

        $locations = $query->limit($limit)->get();

        if ($locations->isEmpty()) {
            $this->info('No more active locations need updating.');
            Cache::forget('update_location_details_last_id');
            return;
        }

        $this->output->progressStart($locations->count());
        $processedCount = 0;
        $maxId = $lastProcessedId;

        foreach ($locations as $location) {
            $this->updateLocation($location, $field, $forceTimezone);
            $processedCount++;
            $maxId = max($maxId, $location->id);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Letzten bearbeiteten ID speichern
        Cache::put('update_location_details_last_id', $maxId, now()->addDays(7));
        $this->info("Processed {$processedCount} active locations. Last processed ID: {$maxId}");
    }

    /**
     * Aktualisiert eine einzelne Location.
     */
    private function updateLocation(WwdeLocation $location, ?string $field, bool $forceTimezone): void
    {
        try {
            // Prüfe Koordinaten
            if (!$this->areCoordinatesValid($location->lat, $location->lon)) {
                $this->warn("Invalid coordinates for {$location->title} (lat: {$location->lat}, lon: {$location->lon})");
                return;
            }

            $updates = [];

            // ISO-Codes aktualisieren (wenn nötig)
            if (!$field || in_array($field, ['iso2', 'iso3'])) {
                if (is_null($location->iso2) || is_null($location->iso3)) {
                    $geocodeData = $this->geocodeService->searchByCoordinates($location->lat, $location->lon);
                    $countryName = $geocodeData['address']['country'] ?? null;

                    if ($countryName) {
                        $isoCodes = $this->getIsoCodes($countryName);
                        $updates['iso2'] = $isoCodes['iso2'] ?? $location->iso2;
                        $updates['iso3'] = $isoCodes['iso3'] ?? $location->iso3;
                    } else {
                        $this->warn("No country found for {$location->title}");
                    }
                }
            }

            // Zeitzone aktualisieren (wenn nötig oder erzwungen)
            if (!$field || $field === 'time_zone') {
                $newTimeZone = $this->getTimeZone($location->lat, $location->lon);
                if ($newTimeZone && ($forceTimezone || is_null($location->time_zone) || $location->time_zone !== $newTimeZone)) {
                    $updates['time_zone'] = $newTimeZone;
                    if ($location->time_zone && $location->time_zone !== $newTimeZone) {
                        $this->info("Corrected timezone for {$location->title} from {$location->time_zone} to {$newTimeZone}");
                    }
                }
            }

            if (!empty($updates)) {
                $location->update($updates);
                $this->info("Updated {$location->title}: " . json_encode($updates));
            } else {
                $this->warn("No updates applied for {$location->title}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to update {$location->title}", [
                'error' => $e->getMessage(),
                'location_id' => $location->id,
                'lat' => $location->lat,
                'lon' => $location->lon,
            ]);
            $this->error("Error updating {$location->title}: {$e->getMessage()}");
        }
    }

    /**
     * Validiert Koordinaten.
     */
    private function areCoordinatesValid(?float $lat, ?float $lon): bool
    {
        return is_numeric($lat) && is_numeric($lon) &&
               $lat >= -90 && $lat <= 90 &&
               $lon >= -180 && $lon <= 180;
    }

    /**
     * Hole ISO-Codes für ein Land mit Cache.
     */
    private function getIsoCodes(string $countryName): array
    {
        $cacheKey = "iso_codes_" . md5($countryName);
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($countryName) {
            try {
                $response = Http::timeout(10)->get('https://restcountries.com/v3.1/name/' . urlencode($countryName));
                if ($response->successful()) {
                    $data = $response->json()[0] ?? null;
                    if ($data) {
                        return [
                            'iso2' => $data['cca2'] ?? null,
                            'iso3' => $data['cca3'] ?? null,
                        ];
                    }
                    Log::warning("No valid data returned for country: {$countryName}", ['response' => $response->body()]);
                } else {
                    Log::warning("Failed to fetch ISO codes for country: {$countryName}", ['status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error("Error fetching ISO codes for {$countryName}", ['error' => $e->getMessage()]);
            }
            return ['iso2' => null, 'iso3' => null];
        });
    }

    /**
     * Hole Zeitzone basierend auf Koordinaten mit Cache.
     */
    private function getTimeZone(float $lat, float $lon): ?string
    {
        $cacheKey = "timezone_{$lat}_{$lon}";
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lon) {
            try {
                $response = Http::timeout(10)->get("https://timeapi.io/api/time/current/coordinate", [
                    'latitude' => $lat,
                    'longitude' => $lon,
                ]);

                if ($response->successful() && $tz = $response->json('timeZone')) {
                    return $tz;
                }
                Log::warning("No timezone found for coordinates ({$lat}, {$lon})", ['response' => $response->body()]);
            } catch (\Exception $e) {
                Log::error("Error fetching timezone for ({$lat}, {$lon})", ['error' => $e->getMessage()]);
            }
            return null;
        });
    }
}
