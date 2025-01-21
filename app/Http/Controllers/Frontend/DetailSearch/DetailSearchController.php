<?php

namespace App\Http\Controllers\Frontend\DetailSearch;

use App\Models\WwdeRange;
use App\Models\WwdeClimate;
use App\Models\WwdeCountry;
use App\Models\ModLanguages;
use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Models\HeaderContent;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\WwdeContinent; // WwdeContinent importieren

class DetailSearchController extends Controller
{
    /**
     * Zeigt die Detail-Suche-Seite an.
     */
    public function index()
    {

    // Header Content und Bilder laden
    $headerContent = Cache::remember('header_content_random', 5 * 60, function () {
        return HeaderContent::inRandomOrder()->first();
    });

    $bgImgPath = $headerContent->bg_img
        ? (Storage::exists($headerContent->bg_img)
            ? Storage::url($headerContent->bg_img)
            : (file_exists(public_path($headerContent->bg_img))
                ? asset($headerContent->bg_img)
                : null))
        : null;

    $mainImgPath = $headerContent->main_img
        ? (Storage::exists($headerContent->main_img)
            ? Storage::url($headerContent->main_img)
            : (file_exists(public_path($headerContent->main_img))
                ? asset($headerContent->main_img)
                : null))
        : null;


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
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
            'panorama_location_text' => $headerContent->main_text ?? null,
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

        if ($request->filled('continents')) {
            $selectedContinents = array_keys($request->input('continents')); // IDs der ausgewählten Kontinente
            $query->whereIn('continent_id', $selectedContinents);
        }



        // Filter Locations basierend auf ausgewählten Währungen
        if ($request->filled('currency')) {
            $countryIds = WwdeCountry::where('currency_code', $request->currency)->pluck('id');
            $query->whereIn('country_id', $countryIds);
        }

        // Filtere Locations basierend auf ausgewählten Sprachen
        if ($request->filled('language')) {
            $countryIds = WwdeCountry::where('official_language', $request->language)->pluck('id');
            $query->whereIn('country_id', $countryIds);
        }

        // Filtere Locations basierend auf ausgewählten Visum-Status
        if ($request->filled('visum')) {
            $visumRequired = $request->visum === 'yes' ? 1 : 0;

            // Länder mit passendem Visum-Status abrufen
            $countryIds = WwdeCountry::where('country_visum_needed', $visumRequired)->pluck('id');

            // Locations basierend auf diesen Ländern filtern
            $query->whereIn('country_id', $countryIds);
        }

        if ($request->filled('price_tendency')) {
            // Länder mit der ausgewählten Preistendenz abrufen
            $countryIds = WwdeCountry::where('price_tendency', $request->price_tendency)->pluck('id');

            // Locations basierend auf diesen Ländern filtern
            $query->whereIn('country_id', $countryIds);
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


        // Filter für Aktivitäten
        if ($request->filled('activities')) {
            $activities = $request->activities;

            $query->where(function ($subQuery) use ($activities) {
                foreach ($activities as $activity) {
                    $subQuery->orWhere($activity, 1);
                }
            });
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



        $locations = $query->get();
        $count = $locations->count();

    if ($request->ajax()) {
        // Antwort für AJAX-Anfragen (JSON)
        return response()->json(['count' => $count]);
    }


    $filters = $request->only([
        'month',
        'continent',
        'price',
        'sonnenstunden',
        'wassertemperatur',
        'spezielle',
    ]);

    $repository = app(\App\Repositories\LocationRepository::class);

    $query = $repository->getLocationsByFilters($filters);
    $locations = $query->paginate(10);

    $locations = $repository->formatLocations($locations, false);


    $headerData = $repository->getHeaderContent();
//dd($headerData);

$locations = $query->get();


// Umleitung zur neuen Route mit Daten als Query-Parameter
return redirect()->route('ergebnisse.anzeigen', [
    'locations' => $locations->pluck('id')->toArray(),
    'urlaubType' => $request->get('urlaubType', 'default'),
    'monthId' => $request->get('monthId'),
    'monthName' => $filters['month'] ?? 'Unbekannt',
    'items_per_page' => 10,
]);

        // Rückgabe der JSON-Antwort
        return response()->json(['count' => $count]);
    }


    public function showSearchResults(Request $request)
    {
        // Daten aus der Datenbank abrufen (z. B. gefilterte Locations)
        $locations = WwdeLocation::query()
            ->with(['country', 'country.continent']) // Eager Loading für Beziehungen
            ->when($request->filled('month'), function ($query) use ($request) {
                // Filter nach Monat
                $query->whereJsonContains('best_traveltime_json', $request->month);
            })
            ->when($request->filled('sort_by'), function ($query) use ($request) {
                // Sortierung
                $query->orderBy($request->sort_by, $request->sort_direction ?? 'asc');
            })
            ->paginate($request->items_per_page ?? 10);

        // Header-Daten (falls benötigt)
// Header-Daten abrufen
$repository = app(\App\Repositories\LocationRepository::class);
$headerData = $repository->getHeaderContent();

// Daten an die View übergeben
return view('pages.main.search-results', [
    'locations' => $locations, // Paginierte Suchergebnisse
    'items_per_page' => $request->items_per_page ?? 10, // Anzahl der Ergebnisse pro Seite
    'sort_by' => $request->sort_by, // Aktuelles Sortierkriterium
    'sort_direction' => $request->sort_direction ?? 'asc', // Sortierrichtung
    'headerContent' => $headerData['headerContent'] ?? null, // Header-Inhalt (falls vorhanden)
    'panorama_location_picture' => $headerData['bgImgPath'] ?? null, // Panorama-Bild
    'main_location_picture' => $headerData['mainImgPath'] ?? null, // Haupt-Bild
    'panorama_location_text' => $headerData['mainText'] ?? null, // Haupt-Text
]);
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
