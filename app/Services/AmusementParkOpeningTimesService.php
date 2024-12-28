<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AmusementParkOpeningTimesService
{
    protected $apiUrl = 'https://api.wartezeiten.app/v1/openingtimes';

    /**
     * Fetch the opening times for a specific park by its external ID.
     *
     * @param string $externalId
     * @return array|null
     */
    public function getOpeningTimes(string $externalId): ?array
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'park' => $externalId, // Header `park` muss gesetzt werden
        ])->get($this->apiUrl);

        if ($response->successful()) {
            return $response->json()[0] ?? null; // Erstes Element aus der Antwort
        }

        \Log::error("Failed to fetch opening times for park ID {$externalId}: " . $response->body());
        return null;
    }

    /**
     * Update the database with the opening times of all parks.
     */
    public function updateOpeningTimesForAllParks(): void
    {
        // Alle Freizeitparks aus der Datenbank abrufen
        $parks = DB::table('amusement_parks')->pluck('external_id');

        foreach ($parks as $externalId) {
            // Öffnungszeiten abrufen und aktualisieren
            $openingTimes = $this->getOpeningTimes($externalId);

            if ($openingTimes) {
                DB::table('amusement_parks')
                    ->where('external_id', $externalId)
                    ->update([
                        'open_today' => $openingTimes['opened_today'],
                        'open_from' => $openingTimes['open_from'],
                        'closed_from' => $openingTimes['closed_from'],
                        'updated_at' => now(),
                    ]);
            }

            // Pause zwischen den Anfragen (2 Sekunden)
            sleep(2);
        }
    }
}
