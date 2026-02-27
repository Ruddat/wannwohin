<?php

namespace App\Services\Search;

use App\Models\WwdeLocation;

class SearchScoreV2
{
    public function score(WwdeLocation $location, SearchFilters $filters): int
    {
        $weights = [
            'continent' => 8,
            'country' => 10,
            'month_besttime' => 18,
            'price' => 8,
            'sunshine' => 14,
            'water_temp' => 14,
            'motiv' => 28,          // 🔥 stark erhöht
            'activities' => 10,
            'flight_duration' => 4,
            'distance' => 4,
        ];

        $total = 0;
        $hit = 0;

        $consider = function(string $key) use ($filters, $weights, &$total) {
            $enabled = match($key) {
                'continent' => (bool) $filters->continent,
                'country' => (bool) $filters->country,
                'month_besttime' => (bool) $filters->month,
                'price' => (bool) $filters->priceRange,
                'sunshine' => (bool) $filters->sunshineMin && (bool) $filters->month,
                'water_temp' => (bool) $filters->waterTempMin && (bool) $filters->month,
                'motiv' => !empty($filters->activities),
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

        // ----------------------------
        // Continent
        // ----------------------------
        if ($consider('continent')) {
            if ((int)$location->continent_id === (int)$filters->continent) {
                $hit += $weights['continent'];
            }
        }

        // ----------------------------
        // Country
        // ----------------------------
        if ($consider('country')) {
            if ((int)$location->country_id === (int)$filters->country) {
                $hit += $weights['country'];
            }
        }

        // ----------------------------
        // Best travel time
        // ----------------------------
        if ($consider('month_besttime')) {
            $best = json_decode($location->best_traveltime_json ?? '[]', true) ?: [];
            if (in_array((int)$filters->month, array_map('intval', $best), true)) {
                $hit += $weights['month_besttime'];
            }
        }

        // ----------------------------
        // Price (leicht reduziert)
        // ----------------------------
        if ($consider('price')) {
            $hit += (int) round($weights['price'] * 0.7);
        }

        // ----------------------------
        // Climate
        // ----------------------------
        $climate = null;
        if ($filters->month) {
            $climate = $location->climates
                ? $location->climates->firstWhere('month_id', (int)$filters->month)
                : null;
        }

        if ($consider('sunshine')) {
            $val = (int) ($climate->sunshine_per_day ?? 0);
            $min = (int) $filters->sunshineMin;
            $ratio = $min > 0 ? ($val / $min) : 0;

            $scorePart = $ratio >= 1
                ? 0.7 + min(0.3, ($val - $min) / max(1, $min) * 0.3)
                : max(0.0, $ratio * 0.7);

            $hit += (int) round($weights['sunshine'] * $scorePart);
        }

        if ($consider('water_temp')) {
            $val = (int) ($climate->water_temperature ?? 0);
            $min = (int) $filters->waterTempMin;
            $ratio = $min > 0 ? ($val / $min) : 0;

            $scorePart = $ratio >= 1
                ? 0.7 + min(0.3, ($val - $min) / max(1, $min) * 0.3)
                : max(0.0, $ratio * 0.7);

            $hit += (int) round($weights['water_temp'] * $scorePart);
        }

        // ----------------------------
        // 🔥 MOTIV-BOOST (Hauptintention)
        // ----------------------------
        $motivFlags = [
            'list_beach',
            'list_citytravel',
            'list_sports',
            'list_island',
            'list_culture',
            'list_nature',
            'list_watersport',
            'list_wintersport',
            'list_mountainsport',
            'list_biking',
            'list_fishing',
            'list_amusement_park',
            'list_water_park',
            'list_animal_park',
        ];

        if ($consider('motiv')) {

            $req = array_values(array_filter($filters->activities));
            $ok = 0;
            $motivSelected = 0;

            foreach ($req as $flag) {

                if (in_array($flag, $motivFlags, true)) {
                    $motivSelected++;

                    if ((int)($location->{$flag} ?? 0) === 1) {
                        $ok++;
                    }
                }
            }

            if ($motivSelected > 0) {
                $ratio = $ok / $motivSelected;

                // Deckelung schützt vor Score-Explosion
                $motivScore = min(
                    $weights['motiv'],
                    (int) round($weights['motiv'] * $ratio)
                );

                $hit += $motivScore;
            }
        }

        // ----------------------------
        // Flight
        // ----------------------------
        if ($consider('flight_duration')) {
            $hit += ((float)$location->flight_hours <= (float)$filters->flightDuration)
                ? $weights['flight_duration']
                : 0;
        }

        // ----------------------------
        // Distance
        // ----------------------------
        if ($consider('distance')) {
            $hit += ((int)$location->dist_from_FRA <= (int)$filters->distance)
                ? $weights['distance']
                : 0;
        }

        if ($total <= 0) {
            return 85;
        }

        return max(0, min(100, (int) round(($hit / $total) * 100)));
    }
}
