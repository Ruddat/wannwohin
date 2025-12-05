<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SeoService;
use App\Repositories\LocationRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Helpers\HeaderHelper;

class ExploreController extends Controller
{
    protected $seoService;
    protected $locationRepository;

    public function __construct(SeoService $seoService, LocationRepository $locationRepository)
    {
        $this->seoService = $seoService;
        $this->locationRepository = $locationRepository;
    }

    public function index(Request $request)
    {
        $latitude = $request->query('lat') ?? Session::get('user_location.lat');
        $longitude = $request->query('lon') ?? Session::get('user_location.lon');

//dd($latitude, $longitude);

        if ($request->query('lat') && $request->query('lon')) {
            Session::put('user_location', ['lat' => $latitude, 'lon' => $longitude]);
        }

        $popularLocations = DB::table('stat_location_search_histories')
            ->join('wwde_locations', 'stat_location_search_histories.location_id', '=', 'wwde_locations.id')
            ->join('wwde_continents', 'wwde_locations.continent_id', '=', 'wwde_continents.id')
            ->join('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id')
            ->select(
                'wwde_locations.*',
                'wwde_continents.alias as continent_alias',
                'wwde_countries.alias as country_alias',
                'stat_location_search_histories.search_count',
                'wwde_locations.text_pic1' // Angenommen, das Bildfeld heißt so
            )
            ->where('month', '2025-03')
            ->orderByDesc('search_count')
            ->limit(12)
            ->get();

        $seo = $this->seoService->getSeoData([
            'model_type' => 'explore_page',
            'model_id' => 1,
            'title' => 'Finde dein spontanes Reiseziel',
            'description' => 'Beantworte ein paar Fragen und entdecke dein nächstes Abenteuer!',
            'canonical' => url('/explore'),
            'image' => asset('img/explore.jpg'),
        ]);

        $headerData = HeaderHelper::getHeaderContent('explore');
        Session::put('headerData', $headerData);

        return view('pages.main.explore', [
            'seo' => $seo,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'popularLocations' => $popularLocations,
            'panorama_location_picture' => $headerData['bgImgPath'] ?? asset('img/headers/default-header.jpg'),
            'panorama_location_text' => $headerData['title_text'] ?? 'Finde dein Abenteuer',
        ]);
    }

public function results(Request $request)
{
    /* ----------------------------------------------
     * DYNAMISCHE 4-GRID EINSTELLUNGEN
     * ---------------------------------------------- */
    $minResults  = 12;   // mindestens 12 anzeigen
    $maxResults  = 40;   // maximaler Pool für Ranking/Shuffle

    /* ----------------------------------------------
     * 1) FILTER + SESSION
     * ---------------------------------------------- */
    $validActivities = ['relax', 'adventure', 'culture', 'amusement'];
    $validTimes = ['now', 'month', 'later'];

    $activity = $request->query('activity') ?? Session::get('filters.activity', 'relax');
    $time = $request->query('time') ?? Session::get('filters.time', 'now');

    $activity = in_array($activity, $validActivities) ? $activity : 'relax';
    $time     = in_array($time, $validTimes) ? $time : 'now';

    Session::put('filters', compact('activity', 'time'));

    /* ----------------------------------------------
     * 2) USER GEO LOCATION
     * ---------------------------------------------- */
    $latitude  = $request->query('lat') ?? Session::get('user_location.lat');
    $longitude = $request->query('lon') ?? Session::get('user_location.lon');

    if ($request->query('lat') && $request->query('lon')) {
        Session::put('user_location', ['lat' => $latitude, 'lon' => $longitude]);
    }

    /* ----------------------------------------------
     * 3) MONTH LOGIC
     * ---------------------------------------------- */
    $monthId = match ($time) {
        'now'   => now()->month,
        'month' => now()->addMonth()->month,
        'later' => null,
        default => now()->month,
    };

    /* ----------------------------------------------
     * 4) ACTIVITY FILTER MAPPING
     * ---------------------------------------------- */
    $activityMap = [
        'relax'     => ['list_beach' => 1, 'list_nature' => 1],
        'adventure' => ['list_sports' => 1, 'list_mountainsport' => 1],
        'culture'   => ['list_culture' => 1],
    ];

    /* ----------------------------------------------
     * 5) SPECIAL CASE: AMUSEMENT PARKS
     * ---------------------------------------------- */
    if ($activity === 'amusement') {

        $parks = DB::table('amusement_parks')
            ->select(
                'id',
                'name AS title',
                'latitude AS lat',
                'longitude AS lon',
                'country',
                'continent',
                'logo_url AS text_pic1'
            )
            ->get()
            ->map(function ($p) use ($latitude, $longitude) {
                $p->distance = $this->calculateDistance($latitude, $longitude, $p->lat, $p->lon);
                $p->type = 'amusement_park';
                $p->continent_alias = \Illuminate\Support\Str::slug($p->continent);
                $p->country_alias   = \Illuminate\Support\Str::slug($p->country);
                $p->alias           = \Illuminate\Support\Str::slug($p->title);
                return $p;
            });

        // GROSSEN POOL → Runde auf perfektes 4-Grid
        $poolCount = min($parks->count(), $maxResults);
        $gridCount = max($minResults, floor($poolCount / 4) * 4);

        $final = $parks
            ->sortBy('distance')
            ->take($maxResults)
            ->shuffle()
            ->take($gridCount)
            ->values();

        return $this->returnExploreView($final, $activity, $time, $latitude, $longitude);
    }

    /* ----------------------------------------------
     * 6) NORMAL LOCATIONS EINLESEN
     * ---------------------------------------------- */
    $filters = $activityMap[$activity] ?? [];

    $results = $this->locationRepository
        ->getLocationsByFiltersAndMonth($filters, $monthId)
        ->join('wwde_continents', 'wwde_locations.continent_id', '=', 'wwde_continents.id')
        ->join('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id')
        ->leftJoin('stat_location_search_histories', 'wwde_locations.id', '=', 'stat_location_search_histories.location_id')
        ->select(
            'wwde_locations.id',
            'wwde_locations.title',
            'wwde_locations.lat',
            'wwde_locations.lon',
            'wwde_locations.climate_details_lnam',
            'wwde_continents.alias AS continent_alias',
            'wwde_countries.alias AS country_alias',
            'wwde_locations.text_pic1',
            DB::raw('COALESCE(stat_location_search_histories.search_count, 0) AS search_count')
        )
        ->get();

    /* ----------------------------------------------
     * 7) SCORING + ALIAS GENERIEREN
     * ---------------------------------------------- */
    $ranked = $results->map(function ($loc) use ($latitude, $longitude) {

        // Aliase für URLs
        $loc->alias           = \Illuminate\Support\Str::slug($loc->title);
        $loc->continent_alias = \Illuminate\Support\Str::slug($loc->continent_alias ?? 'unknown');
        $loc->country_alias   = \Illuminate\Support\Str::slug($loc->country_alias ?? 'unknown');

        if (!$loc->text_pic1) {
            $loc->text_pic1 = 'https://via.placeholder.com/400x250?text=' . urlencode($loc->title);
        }

        // Distanz
        $loc->distance = $this->calculateDistance($latitude, $longitude, $loc->lat, $loc->lon);

        // Scores
        $distanceScore   = $loc->distance ? max(0, 1 - ($loc->distance / 2500)) : 0.25;
        $climateScore    = $loc->climate_details_lnam ? 1 : 0.4;
        $popularityScore = min(1, $loc->search_count / 500);
        $randomBoost     = rand(5, 20) / 100;

        $loc->score =
            ($distanceScore * 0.45) +
            ($climateScore * 0.25) +
            ($popularityScore * 0.20) +
            ($randomBoost * 0.10);

        return $loc;
    });

    /* ----------------------------------------------
     * 8) GRID-PASSENDE ANZAHL BERECHNEN
     * ---------------------------------------------- */
    $pool = $ranked->sortByDesc('score')->take($maxResults)->values();

    $poolCount = $pool->count();
    $gridCount = max($minResults, floor($poolCount / 4) * 4);

    if ($gridCount > $poolCount) {
        $gridCount = $poolCount - ($poolCount % 4);
    }

    if ($gridCount < $minResults) {
        $gridCount = $minResults;
    }

    $final = $pool->shuffle()->take($gridCount)->values();

    /* ----------------------------------------------
     * 9) RETURN VIEW
     * ---------------------------------------------- */
    return $this->returnExploreView($final, $activity, $time, $latitude, $longitude);
}




private function returnExploreView($locations, $activity, $time, $latitude, $longitude)
{
    // SEO-Zuordnung
    $modelIdMap = [
        'relax-now' => 1, 'relax-month' => 2, 'relax-later' => 3,
        'adventure-now' => 4, 'adventure-month' => 5, 'adventure-later' => 6,
        'culture-now' => 7, 'culture-month' => 8, 'culture-later' => 9,
        'amusement-now' => 10, 'amusement-month' => 11, 'amusement-later' => 12,
    ];

    $modelId = $modelIdMap["{$activity}-{$time}"] ?? 0;

    $seo = app(SeoService::class)->getSeoData([
        'model_type' => 'explore_results',
        'model_id' => $modelId,
        'title' => "Deine Reisevorschläge für {$activity}",
        'description' => "Entdecke jetzt die besten Reiseziele!",
        'canonical' => url("/explore/results?activity={$activity}&time={$time}"),
        'image' => asset('img/explore-results.jpg'),
    ]);

    // Header
    $headerSlug = "explore-{$activity}-{$time}";
    $headerData = HeaderHelper::getHeaderContent($headerSlug);
    Session::put('headerData', $headerData);

//dd($locations);

    return view('pages.main.explore-results', [
        'seo' => $seo,
        'locations' => $locations,
        'activity' => $activity,
        'time' => $time,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'panorama_location_picture' => $headerData['bgImgPath'],
        'panorama_location_text' => $headerData['title_text'],
    ]);
}




    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
