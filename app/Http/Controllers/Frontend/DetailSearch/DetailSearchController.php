<?php

namespace App\Http\Controllers\Frontend\DetailSearch;

use App\Models\WwdeRange;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Models\HeaderContent;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class DetailSearchController extends Controller
{
    private function resolveImagePath($path): ?string
    {
        return $path && (Storage::exists($path)
            ? Storage::url($path)
            : (file_exists(public_path($path)) ? asset($path) : null));
    }

    public function index()
    {
        $headerContent = Cache::remember('header_content_random', 5 * 60, fn() => HeaderContent::inRandomOrder()->first());
        $bgImgPath = $this->resolveImagePath($headerContent->bg_img);
        $mainImgPath = $this->resolveImagePath($headerContent->main_img);

        $countries = Cache::remember('countries_all', 15 * 60, fn() => WwdeCountry::all());
        $currencies = Cache::remember('unique_currencies', 15 * 60, fn() => $countries->unique('currency_code')->sortBy('currency_name'));
        $ranges = Cache::remember('flight_ranges', 15 * 60, fn() => WwdeRange::where('Type', 'Flight')->orderBy('Sort')->get());
        $continents = Cache::remember('continents_all', 15 * 60, fn() => \App\Models\WwdeContinent::all());
        $totalLocations = Cache::remember('total_locations', 15 * 60, fn() => WwdeLocation::count());

        $languages = Cache::remember('unique_languages', 15 * 60, fn() => $countries->pluck('official_language')
            ->flatMap(fn($lang) => array_map('trim', explode(',', $lang ?? '')))
            ->unique()
            ->map(fn($code) => ['code' => $code, 'name' => $this->getLanguageName($code)])
            ->sortBy('name')
            ->values());

        $priceTendencies = Cache::remember('price_tendencies', 15 * 60, fn() => $countries->pluck('price_tendency')->unique());
        $climateZones = Cache::remember('climate_zones', 15 * 60, fn() => $countries->whereNotNull('climatezones_ids')
            ->pluck('climatezones_lnam', 'climatezones_ids')
            ->all());

        $flightDuration = [];
        for ($i = 1; $i <= 10; $i++) {
            $flightDuration[$i] = ['title' => $i, 'unit' => 'h'];
        }
        $flightDuration[11] = ['title' => '> 10', 'unit' => 'h'];

        $destinations = [];
        $steps = [500, 1000, 1500, 2000, 3000, 5000, 7500, 10000];
        foreach ($steps as $index => $step) {
            $destinations[$index + 1] = ['title' => $step, 'unit' => 'km'];
        }
        $destinations[9] = ['title' => '> 10000', 'unit' => 'km'];

        $activities = [
            'list_beach' => ['title' => 'Strand', 'icon' => 'fa-umbrella-beach'],
            'list_citytravel' => ['title' => 'Städtetrip', 'icon' => 'fa-city'],
            'list_sports' => ['title' => 'Sport', 'icon' => 'fa-basketball-ball'],
            'list_island' => ['title' => 'Insel', 'icon' => ''],
            'list_culture' => ['title' => 'Kultur', 'icon' => 'fa-landmark'],
            'list_nature' => ['title' => 'Natur', 'icon' => 'fa-leaf'],
            'list_watersport' => ['title' => 'Wassersport', 'icon' => 'fa-water'],
            'list_wintersport' => ['title' => 'Wintersport', 'icon' => 'fa-snowflake'],
            'list_mountainsport' => ['title' => 'Bergsport', 'icon' => 'fa-mountain'],
            'list_biking' => ['title' => 'Radfahren', 'icon' => 'fa-bicycle'],
            'list_fishing' => ['title' => 'Angeln', 'icon' => 'fa-fish'],
            'list_amusement_park' => ['title' => 'Freizeitpark', 'icon' => 'fa-ticket-alt'],
            'list_water_park' => ['title' => 'Wasserpark', 'icon' => 'fa-swimmer'],
            'list_animal_park' => ['title' => 'Tierpark', 'icon' => 'fa-paw'],
        ];

        return view('frondend.detailSearch._index', [
            'countries' => $countries,
            'ranges' => $ranges,
            'climate_lnam' => $climateZones,
            'continents' => $continents,
            'total_locations' => $totalLocations,
            'languages' => $languages,
            'currencies' => $currencies,
            'price_tendencies' => $priceTendencies,
            'flightDuration' => $flightDuration,
            'Destinations' => $destinations,
            'activities' => $activities,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
            'panorama_location_text' => $headerContent->main_text ?? null,
        ]);
    }

    public function search(Request $request)
    {
        $query = WwdeLocation::query()
            ->select('wwde_locations.id')
            ->leftJoin('wwde_climates', 'wwde_locations.id', '=', 'wwde_climates.location_id');
        $months = config('custom.months') ?? [];

        $this->applyFilters($query, $request, $months);

        $locationIds = $query->pluck('id')->toArray();
        $count = count($locationIds);

        if ($request->ajax()) {
            return response()->json(['count' => $count]);
        }

        session(['search_location_ids' => $locationIds, 'items_per_page' => $request->get('items_per_page', 10)]);
        return redirect()->route('ergebnisse.anzeigen');
    }

    private function applyFilters($query, Request $request, array $months): void
    {
        if ($request->filled('month') && isset($months[$request->month])) {
            $query->whereJsonContains('wwde_locations.best_traveltime_json', $months[$request->month]);
        }

        if ($request->filled('range_flight')) {
            $range = WwdeRange::find($request->range_flight);
            if ($range) {
                $maxPrice = (int)filter_var($range->Range_to_show, FILTER_SANITIZE_NUMBER_INT);
                $query->where('wwde_locations.price_flight', strpos($range->Range_to_show, '>') !== false ? '>' : '<=', $maxPrice);
            }
        }

        if ($request->filled('country')) {
            $query->where('wwde_locations.country_id', $request->country);
        }

        if ($request->filled('continents')) {
            $query->whereIn('continent_id', array_keys($request->continents));
        }

        foreach (['currency', 'language', 'visum', 'price_tendency'] as $filter) {
            if ($request->filled($filter)) {
                $this->filterByCountryAttribute($query, $filter, $request->$filter);
            }
        }

        if ($request->filled('climate_zone')) {
            $climateZones = explode(',', str_replace(' ', '', $request->climate_zone));
            $countryIds = WwdeCountry::where(fn($q) => array_map(fn($zone) => $q->orWhereRaw("FIND_IN_SET(?, climatezones_ids)", [$zone]), $climateZones))
                ->pluck('id');
            $query->whereIn('wwde_locations.country_id', $countryIds);
        }

        if ($request->filled('activities')) {
            $query->where(fn($q) => array_map(fn($activity) => $q->orWhere($activity, 1), (array)$request->activities));
        }

        $this->applyRangeFilter($query, $request, 'flight_duration', 'wwde_locations.flight_hours', [
            '1' => '<= 1', '2' => '<= 2', '3' => '<= 3', '10' => '> 10',
        ]);

        $this->applyRangeFilter($query, $request, 'distance_to_destination', 'wwde_locations.dist_from_FRA', [
            '1' => '<= 500', '2' => 'between 501 and 1000', '9' => '> 10000',
        ]);

        if ($request->filled('stop_over')) {
            $query->where('wwde_locations.stop_over', $request->stop_over === 'yes' ? '>' : '=', 0);
        }

        $climateFields = [
            'daily_temp' => 'daily_temperature',
            'night_temp' => 'night_temperature',
            'water_temp' => 'water_temperature',
            'sunshine' => 'sunshine_per_day',
            'rainy_days' => 'rainy_days',
            'humidity' => 'humidity',
        ];

        foreach ($climateFields as $key => $field) {
            if ($request->filled("{$key}_min") && $request->filled("{$key}_max")) {
                $query->whereBetween("wwde_climates.{$field}", [$request->{"{$key}_min"}, $request->{"{$key}_max"}]);
            }
        }
    }

    private function filterByCountryAttribute($query, string $attribute, $value): void
    {
        $column = $attribute === 'visum' ? 'country_visum_needed' : ($attribute === 'language' ? 'official_language' : $attribute);
        $value = $attribute === 'visum' ? ($value === 'yes' ? 1 : 0) : $value;
        $countryIds = WwdeCountry::where($column, $value)->pluck('id');
        $query->whereIn('wwde_locations.country_id', $countryIds);
    }

    private function applyRangeFilter($query, Request $request, string $key, string $column, array $conditions): void
    {
        if ($request->filled($key) && isset($conditions[$request->$key])) {
            [$operator, $limit] = explode(' ', $conditions[$request->$key], 2);
            if ($operator === 'between') {
                [$min, $max] = explode(' and ', $limit);
                $query->whereBetween($column, [(int)$min, (int)$max]);
            } else {
                $query->where($column, $operator, (int)$limit);
            }
        }
    }

    public function showSearchResults(Request $request)
    {
        $locationIds = session('search_location_ids', []);
        $itemsPerPage = session('items_per_page', 10);

        if (empty($locationIds)) {
            return view('pages.main.search-results', [
                'locations' => new LengthAwarePaginator([], 0, $itemsPerPage),
                'items_per_page' => $itemsPerPage,
            ]);
        }

        $currentDate = now()->toDateString();
        $locationsQuery = WwdeLocation::with([
            'country.continent',
            'dailyClimates' => fn($q) => $q->whereDate('date', $currentDate),
        ])->whereIn('id', $locationIds);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $locations = $locationsQuery->forPage($currentPage, $itemsPerPage)->get()->map(function ($location) {
            $climate = $location->dailyClimates->first();
            $location->climate_data = [
                'main' => ['temp' => $climate->avg_daily_temperature ?? 0],
                'water_temperature' => $climate->avg_water_temperature ?? 0,
                'rain' => ['1h' => $climate->total_rainy_days ?? 0],
                'sunshine_per_day' => $climate->avg_sunshine_per_day ?? 0,
            ];
            return $location;
        });

        $pagedLocations = new LengthAwarePaginator(
            $locations,
            count($locationIds),
            $itemsPerPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.main.search-results', [
            'locations' => $pagedLocations,
            'items_per_page' => $itemsPerPage,
        ]);
    }

    public function predictFutureClimate($locationId)
    {
        $existingData = \App\Models\WwdeClimate::where('location_id', $locationId)->orderBy('month_id')->get();
        if ($existingData->isEmpty()) {
            return response()->json(['message' => 'Keine Klimadaten verfügbar.'], 404);
        }

        $futureMonths = \App\Models\WwdeClimate::predictFutureMonths($locationId); // Annahme: Methode existiert
        return view('pages.detailSearch.climate_forecast', compact('futureMonths'));
    }

    protected function getLanguageName($code): string
    {
        return [
            'en' => 'Englisch', 'de' => 'Deutsch', 'fr' => 'Französisch', 'es' => 'Spanisch',
            'it' => 'Italienisch', 'zh' => 'Chinesisch', 'ja' => 'Japanisch', 'ru' => 'Russisch',
        ][$code] ?? $code;
    }
}
