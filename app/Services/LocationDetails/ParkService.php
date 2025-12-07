<?php

namespace App\Services\LocationDetails;

use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http; // Ganz oben einfügen




class ParkService
{
    public function get(WwdeLocation $location)
    {
        $key = "parks_{$location->id}_" . date('Y-m-d');

        return Cache::remember($key, 60 * 60 * 24, function () use ($location) {

            // 1) Parks mit Distanz laden
            $parks = DB::table('amusement_parks')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("
                    amusement_parks.*,
                    ({$this->distanceSql($location)}) AS distance
                ")
                ->having("distance", "<=", 150)
                ->orderBy("distance")
                ->get();

            // 2) Struktur wie früher generieren
            return $parks->map(function ($park) {

                // Öffnungszeiten decodieren
                $openingTimes = is_string($park->opening_hours) && json_decode($park->opening_hours, true)
                    ? json_decode($park->opening_hours, true)
                    : $park->opening_hours;

                // Warteschlangen (Queue Times)
                $waitingTimes = $park->queue_times_id
                    ? $this->fetchQueueTimes($park->queue_times_id)
                    : [];

                // Last updated Zeit formatieren
                $lastUpdatedFormatted = $waitingTimes && isset($waitingTimes[0]['last_updated'])
                    ? Carbon::parse($waitingTimes[0]['last_updated'])->locale('de')->isoFormat('D. MMMM YYYY, HH:mm')
                    : null;

                // Coolness Score berechnen
                $coolnessVotes = DB::table('park_coolness_votes')
                    ->where('park_id', $park->id)
                    ->pluck('value')
                    ->toArray();

                $coolnessScore = !empty($coolnessVotes)
                    ? round(array_sum($coolnessVotes) / count($coolnessVotes) * 10)
                    : null;

                // Feedback Rating & Kommentarzähler
                $feedbackData = DB::table('park_feedback')
                    ->where('park_id', $park->id)
                    ->selectRaw('AVG(rating) as avg_rating, COUNT(comment) as comment_count')
                    ->first();

                return [
                    'park'           => $park,
                    'opening_times'  => $this->formatOpeningTimes($openingTimes),
                    'waiting_times'  => $waitingTimes,
                    'coolness_score' => $coolnessScore,
                    'avg_rating'     => $feedbackData->avg_rating ?? null,
                    'comment_count'  => $feedbackData->comment_count ?? 0,
                    'last_updated'   => $lastUpdatedFormatted,
                ];
            });
        });
    }

    private function distanceSql($location)
    {
        return "6371 * acos(
            cos(radians({$location->lat})) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians({$location->lon})) +
            sin(radians({$location->lat})) *
            sin(radians(latitude))
        )";
    }

private function fetchQueueTimes(int $parkId): array
{
    $cacheKey = "queue_times_park_{$parkId}_" . date('H');

    return Cache::remember($cacheKey, 3600, function () use ($parkId) {

        $response = Http::get("https://queue-times.com/parks/{$parkId}/queue_times.json");

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        $rides = [];

        if (isset($data['lands'])) {
            foreach ($data['lands'] as $land) {
                foreach ($land['rides'] as $ride) {
                    $rides[] = [
                        'name'         => $ride['name'],
                        'waitingtime'  => $ride['wait_time'],
                        'status'       => $ride['is_open'] ? 'opened' : 'closed',
                        'last_updated' => $ride['last_updated'],
                    ];
                }
            }
        }

        if (isset($data['rides'])) {
            foreach ($data['rides'] as $ride) {
                $rides[] = [
                    'name'         => $ride['name'],
                    'waitingtime'  => $ride['wait_time'],
                    'status'       => $ride['is_open'] ? 'opened' : 'closed',
                    'last_updated' => $ride['last_updated'],
                ];
            }
        }

        return $rides;
    });
}

    private function formatOpeningTimes($openingTimes): ?array
    {
        if (is_null($openingTimes)) {
            return null;
        }

        // Array (JSON)
        if (is_array($openingTimes)) {
            $today = strtolower(now()->locale('de')->isoFormat('dddd'));

            if (!isset($openingTimes[$today])) {
                return null;
            }

            return [
                'opened_today' => !empty($openingTimes[$today]['open']),
                'open_from'    => $openingTimes[$today]['open'] ?? null,
                'closed_from'  => $openingTimes[$today]['close'] ?? null,
            ];
        }

        // String "09:00-17:00"
        if (is_string($openingTimes) && preg_match('/^(\d{2}:\d{2})-(\d{2}:\d{2})$/', $openingTimes, $m)) {
            return [
                'opened_today' => true,
                'open_from'    => $m[1],
                'closed_from'  => $m[2],
            ];
        }

        return null;
    }
}
