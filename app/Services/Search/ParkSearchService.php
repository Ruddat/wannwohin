<?php

namespace App\Services\Search;

use Illuminate\Support\Facades\DB;

class ParkSearchService
{
    public function search(SearchFilters $filters)
    {
        $query = DB::table('amusement_parks as p')
            ->join('wwde_countries as c', 'c.id', '=', 'p.country_id')

            ->leftJoin(
                DB::raw('(SELECT park_id, AVG(value) as avg_coolness
                          FROM park_coolness_votes
                          GROUP BY park_id) as cv'),
                'cv.park_id',
                '=',
                'p.id'
            )
            ->leftJoin(
                DB::raw('(SELECT park_id, AVG(rating) as avg_rating, COUNT(*) as comment_count
                          FROM park_feedback
                          GROUP BY park_id) as pf'),
                'pf.park_id',
                '=',
                'p.id'
            )

            ->select([
                'p.id',
                'p.name',
                'p.slug',
                'p.logo_url',
                'p.latitude',
                'p.longitude',
                'p.description',
                'p.affiliate_enabled',
                'p.country_id',
                'c.title as country_name',
                'c.continent_id',
                'cv.avg_coolness',
                'pf.avg_rating',
                'pf.comment_count',
            ])

            ->whereNotNull('p.country_id');

        // ----------------------------
        // Landfilter
        // ----------------------------
        if ($filters->country) {
            $query->where('p.country_id', $filters->country);
        }

        // ----------------------------
        // Kontinentfilter
        // ----------------------------
        if ($filters->continent) {
            $query->where('c.continent_id', $filters->continent);
        }

        // ----------------------------
        // Park-Tags
        // ----------------------------
        if (!empty($filters->tags['parks'])) {
            $query->whereExists(function ($sub) use ($filters) {
                $sub->select(DB::raw(1))
                    ->from('park_tag as pt')
                    ->join('wwde_tags as t', 't.id', '=', 'pt.tag_id')
                    ->whereColumn('pt.park_id', 'p.id')
                    ->whereIn('t.slug', $filters->tags['parks']);
            });
        }

        // ---------------------------------------
        // Score-Berechnung + Sortierung
        // ---------------------------------------

        return $query
            ->limit(30) // etwas mehr holen, dann sortieren
            ->get()
            ->map(function ($park) use ($filters) {

                $score = 0;

                // ----------------------
                // 1. Tag-Match
                // ----------------------
                if (!empty($filters->tags['parks'])) {
                    $score += 30;
                }

                // ----------------------
                // 2. Land-Match
                // ----------------------
                if ($filters->country && $park->country_id == $filters->country) {
                    $score += 20;
                }

                // ----------------------
                // 3. Kontinent-Match
                // ----------------------
                if ($filters->continent && $park->continent_id == $filters->continent) {
                    $score += 10;
                }

                // ----------------------
                // 4. Rating (0–5 → max 25)
                // ----------------------
                if ($park->avg_rating) {
                    $score += min(25, $park->avg_rating * 5);
                }

                // ----------------------
                // 5. Coolness (max 10)
                // ----------------------
                if ($park->avg_coolness) {
                    $score += min(10, $park->avg_coolness);
                }

                // ----------------------
                // 6. Affiliate Boost
                // ----------------------
                if ($park->affiliate_enabled) {
                    $score += 5;
                }

                $park->search_score = round($score);

                // Optional Anzeige
                $park->coolness_score = $park->avg_coolness
                    ? round($park->avg_coolness * 10)
                    : null;

                return $park;
            })
            ->sortByDesc('search_score')
            ->take(4)   // final nur Top 4 anzeigen
            ->values();
    }
}
