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
        $activity = $request->query('activity') ?? Session::get('filters.activity');
        $time = $request->query('time') ?? Session::get('filters.time');

        $validActivities = ['relax', 'adventure', 'culture', 'amusement'];
        $validTimes = ['now', 'month', 'later'];
        $activity = in_array($activity, $validActivities) ? $activity : 'relax';
        $time = in_array($time, $validTimes) ? $time : 'now';

        if ($request->query('activity') || $request->query('time')) {
            Session::put('filters', ['activity' => $activity, 'time' => $time]);
        }

        $latitude = $request->query('lat') ?? Session::get('user_location.lat');
        $longitude = $request->query('lon') ?? Session::get('user_location.lon');

        if ($request->query('lat') && $request->query('lon')) {
            Session::put('user_location', ['lat' => $latitude, 'lon' => $longitude]);
        }

        $monthId = match ($time) {
            'now' => now()->month,
            'month' => now()->addMonth()->month,
            'later' => null,
            default => now()->month,
        };

        $activityMap = [
            'relax' => ['list_beach' => 1, 'list_nature' => 1],
            'adventure' => ['list_sports' => 1, 'list_mountainsport' => 1],
            'culture' => ['list_culture' => 1],
            'amusement' => 'amusement_parks',
        ];

        $locations = collect();

        if ($activity === 'amusement') {
            $query = DB::table('amusement_parks')
                ->selectRaw("
                    id,
                    name AS title,
                    latitude AS lat,
                    longitude AS lon,
                    country,
                    continent,
                    logo_url AS text_pic1, -- Angenommen, das Bildfeld heißt logo_url
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    ) AS distance
                ", [$latitude, $longitude, $latitude]);

            $locations = $query->having('distance', '<=', 400)
                ->orderBy('distance')
                ->limit(6)
                ->get()
                ->map(function ($park) {
                    $park->type = 'amusement_park';
                    $park->continent_alias = strtolower($park->continent);
                    $park->country_alias = strtolower($park->country);
                    $park->alias = str_replace(' ', '-', strtolower($park->title));
                    $park->text_pic1 = $park->text_pic1 ?? 'https://via.placeholder.com/400x250?text=' . urlencode($park->title);
                    return $park;
                });
        } else {
            $filters = $activityMap[$activity] ?? [];
            $baseQuery = $this->locationRepository->getLocationsByFiltersAndMonth($filters, $monthId)
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
                    'wwde_locations.text_pic1', // Bildfeld hinzufügen
                    DB::raw('COALESCE(stat_location_search_histories.search_count, 0) AS search_count')
                );

            if ($latitude && $longitude) {
                $query = DB::table(DB::raw("({$baseQuery->toSql()}) AS base"))
                    ->mergeBindings($baseQuery->getQuery())
                    ->selectRaw("
                        base.id,
                        base.title,
                        base.lat,
                        base.lon,
                        base.climate_details_lnam,
                        base.continent_alias,
                        base.country_alias,
                        base.text_pic1,
                        base.search_count,
                        6371 * acos(
                            cos(radians(?)) * cos(radians(base.lat)) * cos(radians(base.lon) - radians(?)) +
                            sin(radians(?)) * sin(radians(base.lat))
                        ) AS distance
                    ", [$latitude, $longitude, $latitude]);

                $distanceLimits = [1200, 2500];
                foreach ($distanceLimits as $limit) {
                    $locations = $query->having('distance', '<=', $limit)
                        ->orderBy('distance')
                        ->limit(6)
                        ->get();
                    if (!$locations->isEmpty()) {
                        break;
                    }
                }

                if ($locations->isEmpty()) {
                    $locations = $baseQuery->orderByDesc('search_count')
                        ->limit(6)
                        ->get();
                }

                $locations = $locations->map(function ($location) {
                    $location->type = 'location';
                    $location->alias = str_replace(' ', '-', strtolower($location->title));
                    $location->text_pic1 = $location->text_pic1 ?? 'https://via.placeholder.com/400x250?text=' . urlencode($location->title);
                    return $location;
                });
            } else {
                $locations = $baseQuery->orderByDesc('search_count')
                    ->limit(6)
                    ->get()
                    ->map(function ($location) {
                        $location->type = 'location';
                        $location->alias = str_replace(' ', '-', strtolower($location->title));
                        $location->text_pic1 = $location->text_pic1 ?? 'https://via.placeholder.com/400x250?text=' . urlencode($location->title);
                        return $location;
                    });
            }
        }

        $modelIdMap = [
            'relax-now' => 1, 'relax-month' => 2, 'relax-later' => 3,
            'adventure-now' => 4, 'adventure-month' => 5, 'adventure-later' => 6,
            'culture-now' => 7, 'culture-month' => 8, 'culture-later' => 9,
            'amusement-now' => 10, 'amusement-month' => 11, 'amusement-later' => 12,
        ];
        $modelId = $modelIdMap["{$activity}-{$time}"] ?? 0;

        $seo = $this->seoService->getSeoData([
            'model_type' => 'explore_results',
            'model_id' => $modelId,
            'title' => "Deine Reisevorschläge für {$activity}",
            'description' => "Entdecke die besten Ziele für {$activity} – basierend auf deinen Antworten!",
            'canonical' => url("/explore/results?activity={$activity}&time={$time}"),
            'image' => asset('img/explore-results.jpg'),
        ]);

        $headerSlug = "explore-{$activity}-{$time}";
        $headerData = HeaderHelper::getHeaderContent($headerSlug);
        if (!$headerData['bgImgPath']) {
            $headerData = HeaderHelper::getHeaderContent('explore') ?? [
                'bgImgPath' => asset('img/headers/default-header.jpg'),
                'mainImgPath' => null,
                'title' => "Dein {$activity}-Abenteuer",
                'title_text' => "Entdecke jetzt passende Reiseziele",
                'main_text' => '',
            ];
        }
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
