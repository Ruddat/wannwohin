<?php

namespace App\Http\Controllers\Frontend\DetailSearch;

use App\Models\WwdeRange;
use App\Models\WwdeClimate;
use App\Models\WwdeCountry;
use App\Models\ModLanguages;
use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\WwdeContinent; // WwdeContinent importieren

class DetailSearchController extends Controller
{
    /**
     * Zeigt die Detail-Suche-Seite an.
     */
    public function index()
    {
        // Länder abrufen
        $countries = WwdeCountry::all();

        // Nur eindeutige Währungen basierend auf `currency_code`
        $currencies = $countries->unique('currency_code');

        // Preisspannen abrufen
        $ranges = WwdeRange::where('Type', 'Flight')->orderBy('Sort')->get();

        // Sprachen
        //$languages = ModLanguages::all();

        $languages = WwdeCountry::select('official_language as code')
        ->distinct()
        ->get()
        ->map(function ($lang) {
            return [
                'code' => $lang->code,
                'name' => $this->getLanguageName($lang->code),
            ];
        });


        $priceTendencies = WwdeCountry::select('price_tendency')
        ->distinct()
        ->pluck('price_tendency');
        //dd($priceTendencies);
        // Klimazonen aus Ländern extrahieren
        $climateZones = WwdeCountry::query()
            ->whereNotNull('climatezones_ids')
            ->distinct()
            ->pluck('climatezones_lnam', 'climatezones_ids')
            ->toArray();

        // Kontinente abrufen
        $continents = WwdeContinent::all();

        // Anzahl der Locations (Placeholder, bis eine Suche ausgeführt wird)
        $totalLocations = WwdeLocation::count();


    // Flugstunden-Optionen (1 bis 10 Stunden, und "> 10 Stunden")
    $flightDuration = [];
    for ($i = 1; $i <= 10; $i++) {
        $flightDuration[$i] = ['title' => $i, 'unit' => 'h'];
    }
    $flightDuration['11'] = ['title' => '> 10', 'unit' => 'h'];

    // Entfernung zum Reiseziel-Optionen
    $Destinations = [];
    $steps = [500, 1000, 1500, 2000, 3000, 5000, 7500, 10000];
    foreach ($steps as $index => $step) {
        $Destinations[$index + 1] = ['title' => $step, 'unit' => 'km'];
    }
    $Destinations['9'] = ['title' => '> 10000', 'unit' => 'km'];


    // Aktivitäten definieren
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
            'Destinations' => $Destinations,
            'activities' => $activities,
        ]);
    }

    /**
     * Verarbeitet die Suchanfrage.
     */
    public function search(Request $request)
    {
        // Monatsnamen aus der Config laden
        $months = config('custom.months');

        // Query mit Join initialisieren
        $query = WwdeLocation::query()
            ->join('wwde_climates', 'wwde_locations.id', '=', 'wwde_climates.location_id');

        // Filter: Reisezeit
        if ($request->filled('month')) {
            $selectedMonth = $months[$request->month] ?? null;
            if ($selectedMonth) {
                $query->whereJsonContains('wwde_locations.best_traveltime_json', $selectedMonth);
            }
        }

        // Filter: Flugpreisbereich
        if ($request->filled('range_flight')) {
            $selectedRange = WwdeRange::find($request->range_flight);
            if ($selectedRange) {
                if (strpos($selectedRange->Range_to_show, '>') !== false) {
                    $query->where('wwde_locations.price_flight', '>', 2000);
                } else {
                    $maxPrice = (int) filter_var($selectedRange->Range_to_show, FILTER_SANITIZE_NUMBER_INT);
                    $query->where('wwde_locations.price_flight', '<=', $maxPrice);
                }
            }
        }

        // Filter: Länder
        if ($request->filled('country')) {
            $query->where('wwde_locations.country_id', $request->country);
        }

        // Filter: Klimazonen
        if ($request->filled('climate_zone')) {
            $climateZones = explode(',', str_replace(' ', '', $request->climate_zone));
            $countryIds = WwdeCountry::query()
                ->where(function ($query) use ($climateZones) {
                    foreach ($climateZones as $zone) {
                        $query->orWhereRaw("FIND_IN_SET(?, climatezones_ids)", [$zone]);
                    }
                })
                ->pluck('id');
            $query->whereIn('wwde_locations.country_id', $countryIds);
        }

        // Filter: Flugstunden
        if ($request->filled('flight_duration')) {
            switch ($request->flight_duration) {
                case '1': $query->where('wwde_locations.flight_hours', '<=', 1); break;
                case '2': $query->where('wwde_locations.flight_hours', '<=', 2); break;
                case '3': $query->where('wwde_locations.flight_hours', '<=', 3); break;
                case '10': $query->where('wwde_locations.flight_hours', '>', 10); break;
            }
        }

        // Filter: Entfernung zum Ziel
        if ($request->filled('distance_to_destination')) {
            switch ($request->distance_to_destination) {
                case '1': $query->where('wwde_locations.dist_from_FRA', '<=', 500); break;
                case '2': $query->whereBetween('wwde_locations.dist_from_FRA', [501, 1000]); break;
                case '9': $query->where('wwde_locations.dist_from_FRA', '>', 10000); break;
            }
        }

        // Filter: Direktflug
        if ($request->filled('stop_over')) {
            if ($request->stop_over === 'yes') {
                $query->where('wwde_locations.stop_over', '>', 0);
            } elseif ($request->stop_over === 'no') {
                $query->where('wwde_locations.stop_over', '=', 0);
            }
        }

        // Filter: Klimadaten
        if ($request->filled('daily_temp_min') && $request->filled('daily_temp_max')) {
            $query->whereBetween('wwde_climates.daily_temperature', [$request->daily_temp_min, $request->daily_temp_max]);
        }

        if ($request->filled('night_temp_min') && $request->filled('night_temp_max')) {
            $query->whereBetween('wwde_climates.night_temperature', [$request->night_temp_min, $request->night_temp_max]);
        }

        if ($request->filled('water_temp_min') && $request->filled('water_temp_max')) {
            $query->whereBetween('wwde_climates.water_temperature', [$request->water_temp_min, $request->water_temp_max]);
        }

        if ($request->filled('sunshine_min') && $request->filled('sunshine_max')) {
            $query->whereBetween('wwde_climates.sunshine_per_day', [$request->sunshine_min, $request->sunshine_max]);
        }

        if ($request->filled('rainy_days_min') && $request->filled('rainy_days_max')) {
            $query->whereBetween('wwde_climates.rainy_days', [$request->rainy_days_min, $request->rainy_days_max]);
        }

        if ($request->filled('humidity_min') && $request->filled('humidity_max')) {
            $query->whereBetween('wwde_climates.humidity', [$request->humidity_min, $request->humidity_max]);
        }

        // (Weitere Filter wie night_temp, water_temp, usw.)

        // Debugging-Log der generierten Query
        Log::info('Generierte Query:', ['query' => $query->toSql()]);

        // Anzahl der Ergebnisse
        $count = $query->count();

        // Rückgabe der JSON-Antwort
        return response()->json(['count' => $count]);
    }

        /**
     * Berechnet zukünftige Monate basierend auf vorhandenen Klimadaten.
     */
    public function predictFutureClimate($locationId)
    {
        // Hole vorhandene Monate für die Location
        $existingData = WwdeClimate::where('location_id', $locationId)
            ->orderBy('month_id')
            ->get();

        if ($existingData->isEmpty()) {
            return response()->json(['message' => 'Keine Klimadaten für diese Location verfügbar.'], 404);
        }

        // Berechnung zukünftiger Monate
        $futureMonths = WwdeClimate::predictFutureMonths($locationId);

        return view('pages.detailSearch.climate_forecast', compact('futureMonths'));
    }

    protected function getLanguageName($code)
{
    $languageNames = [
        'en' => 'Englisch',
        'de' => 'Deutsch',
        'fr' => 'Französisch',
        'es' => 'Spanisch',
        'it' => 'Italienisch',
        'zh' => 'Chinesisch',
        'ja' => 'Japanisch',
        'ru' => 'Russisch',
    ];
    return $languageNames[$code] ?? $code;
}
}
