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
     * 1) FILTER + SESSION HANDLING
     * ---------------------------------------------- */
    $validActivities = ['relax', 'adventure', 'culture', 'amusement'];
    $validTimes = ['now', 'month', 'later'];

    $activity = $request->query('activity') ?? Session::get('filters.activity', 'relax');
    $time = $request->query('time') ?? Session::get('filters.time', 'now');

    $activity = in_array($activity, $validActivities) ? $activity : 'relax';
    $time = in_array($time, $validTimes) ? $time : 'now';

    Session::put('filters', compact('activity', 'time'));

    /* ----------------------------------------------
     * 2) GEO KOORDINATEN
     * ---------------------------------------------- */
    $latitude = $request->query('lat') ?? Session::get('user_location.lat');
    $longitude = $request->query('lon') ?? Session::get('user_location.lon');

    if ($request->query('lat') && $request->query('lon')) {
        Session::put('user_location', ['lat' => $latitude, 'lon' => $longitude]);
    }

    /* ----------------------------------------------
     * 3) MONATS-LOGIK
     * ---------------------------------------------- */
    $monthId = match ($time) {
        'now'   => now()->month,
        'month' => now()->addMonth()->month,
        'later' => null,
        default => now()->month,
    };

    /* ----------------------------------------------
     * 4) AKTIVITÄTS-FILTERBUILDING
     * ---------------------------------------------- */
    $activityMap = [
        'relax'     => ['list_beach' => 1, 'list_nature' => 1],
        'adventure' => ['list_sports' => 1, 'list_mountainsport' => 1],
        'culture'   => ['list_culture' => 1],
    ];

    /* --------------------------------------------------------
     * 5) SPEZIALFALL AMUSEMENT – SEPARATE LOGIK
     * -------------------------------------------------------- */
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
            ->map(function ($park) use ($latitude, $longitude) {
                $park->distance = $this->calculateDistance($latitude, $longitude, $park->lat, $park->lon);
                $park->type = 'amusement_park';
                $park->continent_alias = strtolower($park->continent);
                $park->country_alias = strtolower($park->country);
                $park->alias = str_replace(' ', '-', strtolower($park->title));
                return $park;
            });

        $top = $parks->sortBy('distance')->take(20)->shuffle()->take(6)->values();

        return $this->returnExploreView($top, $activity, $time, $latitude, $longitude);
    }

    /* --------------------------------------------------------
     * 6) NORMALE LOCATIONS HOLEN – OHNE LIMITIERUNG
     * -------------------------------------------------------- */
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

    /* --------------------------------------------------------
     * 7) DISTANZ + SCORE BERECHNEN
     * -------------------------------------------------------- */
    $ranked = $results->map(function ($loc) use ($latitude, $longitude, $monthId) {

        $loc->distance = $this->calculateDistance($latitude, $longitude, $loc->lat, $loc->lon);

        // Scores
        $distanceScore = $loc->distance ? max(0, 1 - ($loc->distance / 2500)) : 0.25;
        $climateScore  = $loc->climate_details_lnam ? 1 : 0.4;
        $popularityScore = min(1, $loc->search_count / 500);
        $randomBoost = rand(5, 20) / 100; // 0.05 – 0.20

        $loc->score =
            ($distanceScore * 0.45) +
            ($climateScore * 0.25) +
            ($popularityScore * 0.20) +
            ($randomBoost * 0.10);

        return $loc;
    });

    /* --------------------------------------------------------
     * 8) RANKING: TOP 20 → zufällige 6
     * -------------------------------------------------------- */
    $topPool = $ranked->sortByDesc('score')->take(20);
    $final = $topPool->shuffle()->take(6)->values();

    /* --------------------------------------------------------
     * 9) ZURÜCKGEBEN – EIGENE FUNKTION
     * -------------------------------------------------------- */
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
