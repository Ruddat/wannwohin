<?php

namespace App\Services;

use App\Services\GeocodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AmusementParkService
{
    protected $apiUrl = 'https://api.wartezeiten.app/v1/parks';
    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        $this->geocodeService = $geocodeService;
    }

    // Freizeitparks abrufen
    public function getParks()
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'language' => 'de',
        ])->get($this->apiUrl);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to fetch parks: " . $response->body());
    }

    // Freizeitparks in die Datenbank importieren
    public function importParksToDatabase()
    {
        $parks = $this->getParks();

        foreach ($parks as $park) {
            // Nur nach Parknamen suchen
            $coordinates = $this->getCoordinates($park['name']);

            DB::table('amusement_parks')->updateOrInsert(
                ['external_id' => $park['id']],
                [
                    'name' => $park['name'],
                    'country' => $park['land'],
                    'latitude' => $coordinates['lat'] ?? null,
                    'longitude' => $coordinates['lon'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    // Koordinaten abrufen (nur nach Parknamen)
    protected function getCoordinates($parkName)
    {
        try {
            $response = $this->geocodeService->searchByParkName($parkName);
            return [
                'lat' => $response[0]['lat'] ?? null,
                'lon' => $response[0]['lon'] ?? null,
            ];
        } catch (\Exception $e) {
            // Fehler bei der Geokodierung protokollieren
            \Log::error("Failed to fetch coordinates for {$parkName}: " . $e->getMessage());
            return ['lat' => null, 'lon' => null];
        }
    }
}
