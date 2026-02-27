<?php

namespace App\Services\Search;

use App\Models\WwdeLocation;
use App\Models\WwdeRange;
use Illuminate\Database\Eloquent\Builder;

class SearchEngineV2
{
    public function query(SearchFilters $filters): Builder
    {
        $query = WwdeLocation::query()
            ->with(['climates', 'country'])
            ->active()
            ->finished()
            ->whereNotNull('country_id');

        // ----------------------------------
        // Kontinent
        // ----------------------------------
        if ($filters->continent) {
            $query->where('continent_id', $filters->continent);
        }

        // ----------------------------------
        // Land
        // ----------------------------------
        if ($filters->country) {
            $query->where('country_id', $filters->country);
        }

        // ----------------------------------
        // Preis
        // ----------------------------------
        if ($filters->priceRange) {
            $this->applyPriceFilter($query, $filters->priceRange);
        }

        // ----------------------------------
        // Beste Reisezeit
        // ----------------------------------
        if ($filters->month && $filters->bestTimeOnly) {
            $query->whereRaw(
                'JSON_CONTAINS(best_traveltime_json, ?)',
                [json_encode((int) $filters->month)]
            );
        }

        // ----------------------------------
        // Klima
        // ----------------------------------
        if ($filters->sunshineMin && $filters->month) {
            $query->whereHas('climates', function ($q) use ($filters) {
                $q->where('month_id', $filters->month)
                    ->whereRaw('COALESCE(sunshine_per_day,0) >= ?', [$filters->sunshineMin]);
            });
        }

        if ($filters->waterTempMin && $filters->month) {
            $query->whereHas('climates', function ($q) use ($filters) {
                $q->where('month_id', $filters->month)
                    ->whereRaw('COALESCE(water_temperature,0) >= ?', [$filters->waterTempMin]);
            });
        }

        if ($filters->dailyTempMin || $filters->dailyTempMax) {
            $query->whereHas('climates', function ($q) use ($filters) {

                if ($filters->month) {
                    $q->where('month_id', $filters->month);
                }

                if ($filters->dailyTempMin) {
                    $q->where('daily_temperature', '>=', $filters->dailyTempMin);
                }

                if ($filters->dailyTempMax) {
                    $q->where('daily_temperature', '<=', $filters->dailyTempMax);
                }
            });
        }

        // ----------------------------------
        // Activities (Motiv + Legacy)
        // ----------------------------------
        if (!empty($filters->activities)) {

            $validColumns = [
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

            foreach ((array) $filters->activities as $wish) {

                if (!is_string($wish)) continue;
                if (!in_array($wish, $validColumns, true)) continue;

                $query->where($wish, 1);
            }
        }

        // ----------------------------------
        // Flugzeit
        // ----------------------------------
        if ($filters->flightDuration) {
            $query->where('flight_hours', '<=', $filters->flightDuration);
        }

        // ----------------------------------
        // Distanz
        // ----------------------------------
        if ($filters->distance) {
            $query->where('dist_from_FRA', '<=', $filters->distance);
        }

        // ----------------------------------
        // TAG SYSTEM
        // ----------------------------------
        if (!empty($filters->tags)) {

            // AND Logik (Standard)
            if ($filters->tagMode === 'and') {

                foreach ($filters->tags as $group => $slugs) {

                    $slugs = array_values(array_filter((array) $slugs));
                    if (empty($slugs)) continue;

                    $query->whereHas('tags', function ($q) use ($group, $slugs) {
                        $q->where('group', $group)
                            ->whereIn('slug', $slugs);
                    });
                }
            }
            // OR Logik
            else {

                $query->where(function ($outer) use ($filters) {

                    foreach ($filters->tags as $group => $slugs) {

                        $slugs = array_values(array_filter((array) $slugs));
                        if (empty($slugs)) continue;

                        $outer->orWhereHas('tags', function ($q) use ($group, $slugs) {
                            $q->where('group', $group)
                                ->whereIn('slug', $slugs);
                        });
                    }
                });
            }
        }

        return $query;
    }

    private function applyPriceFilter($query, $priceId): void
    {
        $range = WwdeRange::find($priceId);

        if (!$range) {
            return;
        }

        $value = $range->Range_to_show;

        if (str_contains($value, '-')) {
            [$min, $max] = array_map(
                'intval',
                explode('-', str_replace(['€', ' '], '', $value))
            );
            $query->whereBetween('price_flight', [$min, $max]);
        } elseif (str_contains($value, '>')) {
            $min = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            $query->where('price_flight', '>=', $min);
        } else {
            $max = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            $query->where('price_flight', '<=', $max);
        }
    }
}
