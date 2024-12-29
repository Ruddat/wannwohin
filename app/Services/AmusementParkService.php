<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AmusementParkService
{
    protected $apiUrl = 'https://api.wartezeiten.app/v1/parks';
    protected $geocodeService;
    protected $openingTimesService;

    public function __construct(GeocodeService $geocodeService, AmusementParkOpeningTimesService $openingTimesService)
    {
        $this->geocodeService = $geocodeService;
        $this->openingTimesService = $openingTimesService;
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
            $coordinates = $this->getCoordinates($park['name']);
            $openingTimes = $this->getParkOpeningTimes($park['id']);

            // Zeitstempel konvertieren
            $openToday = $openingTimes['opened_today'] ?? null;
            $openFrom = isset($openingTimes['open_from'])
                ? Carbon::parse($openingTimes['open_from'])->format('Y-m-d H:i:s')
                : null;
            $closedFrom = isset($openingTimes['closed_from'])
                ? Carbon::parse($openingTimes['closed_from'])->format('Y-m-d H:i:s')
                : null;

            DB::table('amusement_parks')->updateOrInsert(
                ['external_id' => $park['id']],
                [
                    'name' => $park['name'],
                    'land' => $park['land'], // Neues Feld hinzugefügt
                    'country' => $park['land'], // Optional, falls das alte Feld "country" beibehalten wird
                    'latitude' => $coordinates['lat'] ?? null,
                    'longitude' => $coordinates['lon'] ?? null,
                    'open_today' => $openToday,
                    'open_from' => $openFrom,
                    'closed_from' => $closedFrom,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    // Öffnungszeiten für einen Park abrufen
    protected function getParkOpeningTimes(string $externalId)
    {
        try {
            return $this->openingTimesService->getOpeningTimes($externalId);
        } catch (\Exception $e) {
            \Log::error("Failed to fetch opening times for park ID {$externalId}: " . $e->getMessage());
            return [
                'opened_today' => null,
                'open_from' => null,
                'closed_from' => null,
            ];
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
            \Log::error("Failed to fetch coordinates for {$parkName}: " . $e->getMessage());
            return ['lat' => null, 'lon' => null];
        }
    }
}
