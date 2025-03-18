<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AmusementParkService
{
    protected $queueTimesApiUrl = 'https://queue-times.com/parks.json'; // Echte API für Parkliste
    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        $this->geocodeService = $geocodeService;
    }

    // Parks von Queue-Times abrufen
    public function getQueueTimesParks()
    {
        $response = Http::get($this->queueTimesApiUrl);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to fetch parks from Queue-Times: " . $response->body());
    }

    // Freizeitparks in die Datenbank importieren
    public function importParksToDatabase()
    {
        $queueTimesParks = $this->getQueueTimesParks();

        foreach ($queueTimesParks as $group) {
            foreach ($group['parks'] as $park) {
                // Prüfe, ob Koordinaten vorhanden sind, sonst Fallback auf Geocode
                $latitude = $park['latitude'] ?? $this->getCoordinates($park['name'])['lat'];
                $longitude = $park['longitude'] ?? $this->getCoordinates($park['name'])['lon'];

                // Eindeutige external_id generieren, falls nicht vorhanden (z. B. Name als Slug)
                $externalId = strtolower(str_replace(' ', '-', $park['name']));

                DB::table('amusement_parks')->updateOrInsert(
                    ['external_id' => $externalId],
                    [
                        'queue_times_id' => $park['id'], // ID von queue-times.com
                        'group_id' => $group['id'],
                        'name' => $park['name'],
                        'group_name' => $group['name'],
                        'country' => $park['country'],
                        'continent' => $park['continent'],
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'timezone' => $park['timezone'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    // Koordinaten abrufen (Fallback)
    protected function getCoordinates($parkName)
    {
        try {
            $response = $this->geocodeService->searchByParkName($parkName);
            return [
                'lat' => $response[0]['lat'] ?? null,
                'lon' => $response[0]['lon'] ?? null,
            ];
        } catch (\Exception $e) {
            \Log::error("Failed to fetch coordinates for {$parkName}: " . $e->getMessage());
            return ['lat' => null, 'lon' => null];
        }
    }
}
