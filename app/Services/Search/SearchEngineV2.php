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
        // Reisezeit (nur wenn explizit gewünscht)
        // ACHTUNG: wir filtern NICHT automatisch bei month
        // ----------------------------------
        if ($filters->month && request()->boolean('nurInBesterReisezeit')) {
            $query->whereRaw(
                'JSON_CONTAINS(best_traveltime_json, ?)',
                [json_encode((int) $filters->month)]
            );
        }

        // ----------------------------------
        // Sonnenstunden
        // ----------------------------------
        if ($filters->sunshineMin && $filters->month) {
            $query->whereHas('climates', function ($q) use ($filters) {
                $q->where('month_id', $filters->month)
                  ->whereRaw(
                      'COALESCE(sunshine_per_day,0) >= ?',
                      [$filters->sunshineMin]
                  );
            });
        }

        // ----------------------------------
        // Wassertemperatur
        // ----------------------------------
        if ($filters->waterTempMin && $filters->month) {
            $query->whereHas('climates', function ($q) use ($filters) {
                $q->where('month_id', $filters->month)
                  ->whereRaw(
                      'COALESCE(water_temperature,0) >= ?',
                      [$filters->waterTempMin]
                  );
            });
        }

        // ----------------------------------
        // Tages-Temperatur Range
        // ----------------------------------
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
        // Spezielle Wünsche (AND wie alte Logik)
        // ----------------------------------
        if (!empty($filters->activities)) {
            foreach ($filters->activities as $wish) {
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
                explode('-', str_replace(['€',' '], '', $value))
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
