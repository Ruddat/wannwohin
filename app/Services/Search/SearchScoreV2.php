<?php

namespace App\Services\Search;

use App\Models\WwdeLocation;

class SearchScoreV2
{
    public function score(WwdeLocation $location, SearchFilters $filters): int
    {
        // Gewichte (kannst du später feinjustieren)
        $weights = [
            'continent' => 8,
            'country' => 10,
            'month_besttime' => 18,
            'price' => 10,
            'sunshine' => 16,
            'water_temp' => 16,
            'activities' => 12,
            'flight_duration' => 5,
            'distance' => 5,
        ];

        $total = 0;
        $hit = 0;

        // helper: nur werten, wenn Filter gesetzt ist
        $consider = function(string $key) use ($filters, $weights, &$total) {
            $enabled = match($key) {
                'continent' => (bool) $filters->continent,
                'country' => (bool) $filters->country,
                'month_besttime' => (bool) $filters->month,
                'price' => (bool) $filters->priceRange,
                'sunshine' => (bool) $filters->sunshineMin && (bool) $filters->month,
                'water_temp' => (bool) $filters->waterTempMin && (bool) $filters->month,
                'activities' => !empty($filters->activities),
                'flight_duration' => (bool) $filters->flightDuration,
                'distance' => (bool) $filters->distance,
                default => false,
            };

            if ($enabled) {
                $total += $weights[$key];
            }

            return $enabled;
        };

        // 1) Continent
        if ($consider('continent')) {
            if ((int)$location->continent_id === (int)$filters->continent) {
                $hit += $weights['continent'];
            }
        }

        // 2) Country
        if ($consider('country')) {
            if ((int)$location->country_id === (int)$filters->country) {
                $hit += $weights['country'];
            }
        }

        // 3) Best travel time includes month (wenn month gesetzt)
        if ($consider('month_besttime')) {
            $best = json_decode($location->best_traveltime_json ?? '[]', true) ?: [];
            if (in_array((int)$filters->month, array_map('intval', $best), true)) {
                $hit += $weights['month_besttime'];
            }
        }

        // 4) Price (soft): näher am Budget besser
        if ($consider('price')) {
            // Wenn du aus Range_to_show min/max willst, kannst du die Range im Filter DTO bereits auflösen.
            // V1: nur "vorhanden" werten (oder: budgetDistance)
            $hit += (int) round($weights['price'] * 0.7); // placeholder: später durch echte Budget-Nähe ersetzen
        }

        // 5) Climate month data
        $climate = null;
        if ($filters->month) {
            $climate = $location->climates
                ? $location->climates->firstWhere('month_id', (int)$filters->month)
                : null;
        }

        // Sunshine (soft)
        if ($consider('sunshine')) {
            $val = (int) ($climate->sunshine_per_day ?? 0);
            $min = (int) $filters->sunshineMin;

            // soft scoring: unter min -> 0..70%, über min -> 70..100%
            $ratio = $min > 0 ? ($val / $min) : 0;
            $scorePart = $ratio >= 1
                ? 0.7 + min(0.3, ($val - $min) / max(1, $min) * 0.3)
                : max(0.0, $ratio * 0.7);

            $hit += (int) round($weights['sunshine'] * $scorePart);
        }

        // Water temp (soft)
        if ($consider('water_temp')) {
            $val = (int) ($climate->water_temperature ?? 0);
            $min = (int) $filters->waterTempMin;

            $ratio = $min > 0 ? ($val / $min) : 0;
            $scorePart = $ratio >= 1
                ? 0.7 + min(0.3, ($val - $min) / max(1, $min) * 0.3)
                : max(0.0, $ratio * 0.7);

            $hit += (int) round($weights['water_temp'] * $scorePart);
        }

        // Activities: Anteil erfüllter Aktivitäten
        if ($consider('activities')) {
            $req = array_values(array_filter($filters->activities));
            $ok = 0;
            foreach ($req as $flag) {
                if ((int) ($location->{$flag} ?? 0) === 1) {
                    $ok++;
                }
            }
            $ratio = count($req) ? ($ok / count($req)) : 0;
            $hit += (int) round($weights['activities'] * $ratio);
        }

        // Flight duration (soft): <= ist ok; sonst 0
        if ($consider('flight_duration')) {
            $hit += ((float)$location->flight_hours <= (float)$filters->flightDuration)
                ? $weights['flight_duration']
                : 0;
        }

        // Distance (soft): <= ist ok; sonst 0
        if ($consider('distance')) {
            $hit += ((int)$location->dist_from_FRA <= (int)$filters->distance)
                ? $weights['distance']
                : 0;
        }

        if ($total <= 0) {
            return 85; // Default, wenn kaum Filter: nicht 0 wirken lassen
        }

        return max(0, min(100, (int) round(($hit / $total) * 100)));
    }
}
