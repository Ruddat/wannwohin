<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AmusementParkOpeningTimesService
{
    protected $queueTimesApiUrl = 'https://queue-times.com/parks'; // Basis-URL für Warteschlangen

    /**
     * Fetch the queue times (and derived opening times) for a specific park by its queue_times_id.
     *
     * @param int $queueTimesId
     * @return array|null
     */
    public function getOpeningTimes(int $queueTimesId): ?array
    {
        $response = Http::get("{$this->queueTimesApiUrl}/{$queueTimesId}/queue_times.json");

        if ($response->successful()) {
            $data = $response->json();
            $rides = [];

            // Sammle alle Rides aus lands und rides
            foreach ($data['lands'] as $land) {
                foreach ($land['rides'] as $ride) {
                    $rides[] = $ride;
                }
            }
            foreach ($data['rides'] as $ride) {
                $rides[] = $ride;
            }

            if (empty($rides)) {
                return null;
            }

            // Prüfe, ob mindestens eine Attraktion geöffnet ist
            $isOpenToday = collect($rides)->pluck('is_open')->contains(true);
            $lastUpdated = $rides[0]['last_updated'] ?? null;

            // Da queue-times keine expliziten Öffnungszeiten liefert, simulieren wir sie
            $openFrom = $isOpenToday ? '09:00' : null; // Platzhalter, ggf. anpassen
            $closedFrom = $isOpenToday ? '17:00' : null; // Platzhalter, ggf. anpassen

            return [
                'open_from' => $openFrom,
                'closed_from' => $closedFrom,
                'opened_today' => $isOpenToday,
                'last_updated' => $lastUpdated,
            ];
        }

        \Log::error("Failed to fetch queue times for park ID {$queueTimesId}: " . $response->body());
        return null;
    }

    /**
     * Update the database with the opening times of all parks.
     */
    public function updateOpeningTimesForAllParks(): void
    {
        // Alle Freizeitparks mit queue_times_id aus der Datenbank abrufen
        $parks = DB::table('amusement_parks')->whereNotNull('queue_times_id')->pluck('queue_times_id', 'external_id');

        foreach ($parks as $externalId => $queueTimesId) {
            // Öffnungszeiten abrufen
            $openingTimes = $this->getOpeningTimes($queueTimesId);

            if ($openingTimes) {
                // Öffnungszeiten als JSON-Objekt für den aktuellen Tag speichern
                $today = Carbon::now()->locale('de')->isoFormat('dddd');
                $todayKey = strtolower($today);
                $openingHours = [
                    $todayKey => [
                        'open' => $openingTimes['open_from'],
                        'close' => $openingTimes['closed_from'],
                    ],
                ];

                DB::table('amusement_parks')
                    ->where('external_id', $externalId)
                    ->update([
                        'opening_hours' => json_encode($openingHours),
                        'updated_at' => now(),
                    ]);
            }

            // Pause zwischen den Anfragen (2 Sekunden)
            sleep(2);
        }
    }
}
