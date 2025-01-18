<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use App\Models\HeaderContent;
use App\Services\ClimateService;
use App\Services\GeocodeService;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Library\WeatherApiClientLibrary;
use App\Repositories\LocationRepository;
use App\Library\WeatherDataManagerLibrary; // Importiere die Klasse

class IndexController extends Controller
{


    protected $locationRepository;
    protected $weatherService;

    public function __construct(LocationRepository $locationRepository, WeatherService $weatherService)
    {
        $this->locationRepository = $locationRepository;
        $this->weatherService = $weatherService;
    }

    public function __invoke()
    {
        // Ladezeit-Start
        Log::info('IndexController: Start');
        $startTime = microtime(true);

        // 1. Top 10 Locations abrufen (nach Klicks sortiert)
        $step1Start = microtime(true);
        $topTenLocationIds = Cache::remember('top_ten_location_ids', 30 * 60, function () {
            return DB::table('stat_top_ten_locations')
                ->orderByDesc('search_count') // Nach Suchanfragen sortieren
                ->limit(10) // Nur die Top 10 IDs holen
                ->pluck('location_id') // Nur die IDs
                ->toArray(); // Als Array
        });
        Log::info('Schritt 1: Top 10 Location-IDs geladen in ' . (microtime(true) - $step1Start) . ' Sekunden');

        // 2. Hole die Location-Daten und Klimadaten in einer Abfrage
        $topTenLocationsWithClima = WwdeLocation::whereIn('wwde_locations.id', $topTenLocationIds)
        ->leftJoin('wwde_climates', 'wwde_locations.id', '=', 'wwde_climates.location_id')
        ->leftJoin('wwde_continents', 'wwde_locations.continent_id', '=', 'wwde_continents.id') // Join mit Kontinente-Tabelle
        ->leftJoin('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id') // Join mit Länder-Tabelle
        ->select(
            'wwde_locations.id as location_id',
            'wwde_locations.title as location_title', // Location-Name
            'wwde_locations.alias as location_alias', // Location-Alias
            'wwde_locations.lat', // Latitude
            'wwde_locations.lon', // Longitude
            'wwde_locations.iso2', // ISO2
            'wwde_locations.iso3', // ISO3
            'wwde_continents.alias as continent_alias', // Alias aus Kontinente-Tabelle
            'wwde_countries.alias as country_alias', // Alias aus Länder-Tabelle
            'wwde_countries.title as country_title', // Land-Name
            'wwde_climates.daily_temperature',
            'wwde_climates.night_temperature',
            'wwde_climates.humidity',
            'wwde_climates.sunshine_per_day',
            'wwde_climates.water_temperature',
            'wwde_climates.weather_description',
            'wwde_climates.icon'
        )
        ->get();

//dd($topTenLocationsWithClima);


        // 3. Erstelle die Variable `TopTenLocationWithClima`
        $TopTenLocationWithClima = [];

        foreach ($topTenLocationsWithClima as $location) {
            // Prüfe, ob iso2 oder iso3 fehlen
            if (empty($location->iso2) || empty($location->iso3)) {
                // Ländercode aus den Koordinaten ermitteln
                $geocodeService = new GeocodeService();
                $geocodeData = $geocodeService->searchByCoordinates($location->lat, $location->lon);

                // ISO2 aus dem country_code ermitteln
                $iso2 = strtoupper($geocodeData['address']['country_code'] ?? 'unknown');

                // ISO3 aus der Tabelle iso_codes ermitteln
                $iso3 = strtoupper($geocodeData['address']['ISO3166-2-lvl4'] ?? 'unknown');

                // Fehlende Felder in der Datenbank aktualisieren
                DB::table('wwde_locations')
                    ->where('id', $location->location_id)
                    ->update([
                        'iso2' => $iso2,
                        'iso3' => $iso3,
                    ]);
            }

            $TopTenLocationWithClima[] = [
                'location_id' => $location->location_id,
                'location_title' => $location->location_title, // Location-Name
                'location_alias' => $location->location_alias, // Location-Alias
                'iso2' => $location->iso2, // ISO2
                'iso3' => $location->iso3, // ISO3
                'continent' => $location->continent_alias, // Kontinent-Daten
                'country' => $location->country_alias, // Land-Daten
                'climate_data' => [
                    'daily_temperature' => $location->daily_temperature,
                    'night_temperature' => $location->night_temperature,
                    'humidity' => $location->humidity,
                    'sunshine_per_day' => $location->sunshine_per_day,
                    'water_temperature' => $location->water_temperature,
                    'weather_description' => $location->weather_description,
                    'weather_icon' => $location->icon,
                    // Weitere Klimadaten...
                ],
            ];
        }

        // Optional: Logge die Ergebnisse zur Überprüfung
        Log::info('TopTenLocationWithClima:', $TopTenLocationWithClima);

        // 4. Gesamtanzahl der Locations abrufen
        $totalLocations = Cache::remember('total_finished_locations', 5 * 60, function () {
            return DB::table('wwde_locations')->count();
        });

        // 5. HeaderContent abrufen
        $headerContent = Cache::remember('header_content_random', 5 * 60, function () {
            return HeaderContent::inRandomOrder()->first();
        });


        // Bildpfade validieren
        $bgImgPath = $headerContent->bg_img ? (Storage::exists($headerContent->bg_img) ? Storage::url($headerContent->bg_img) : (file_exists(public_path($headerContent->bg_img)) ? asset($headerContent->bg_img) : null)) : null;
        $mainImgPath = $headerContent->main_img ? (Storage::exists($headerContent->main_img) ? Storage::url($headerContent->main_img) : (file_exists(public_path($headerContent->main_img)) ? asset($headerContent->main_img) : null)) : null;

        // Gesamtladezeit loggen
        Log::info('IndexController: Gesamtladezeit ' . (microtime(true) - $startTime) . ' Sekunden');

        // Rückgabe der View
        return view('pages.main.index', [
            'top_ten' => $TopTenLocationWithClima,
            'total_locations' => $totalLocations,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
            'panorama_location_text' => $headerContent->main_text ?? null,
        ]);
    }


    private function getCountryCode($countryId)
    {
        // Beispiel: Ländercode aus der Land-ID ermitteln
        $countryCodes = [
            1 => 'deu', // Deutschland
            2 => 'fra', // Frankreich
            3 => 'esp', // Spanien
            // Weitere Länder hinzufügen...
        ];

        return $countryCodes[$countryId] ?? 'unknown';
    }



    public function searchResults($urlaubType, $monthId)
    {
        // Konvertiere numerischen `monthId` in den Monatsnamen
        try {
            $monthName = \Carbon\Carbon::create()->month((int) $monthId)->translatedFormat('F');
        } catch (\Exception $e) {
            abort(400, "Ungültiger Monat: $monthId");
        }

        // Items pro Seite aus der URL abrufen oder Standardwert setzen
        $itemsPerPage = request()->get('items_per_page', 10);
        $sortBy = request()->get('sort_by', 'title');
        $sortDirection = request()->get('sort_direction', 'asc');

        // Sortierkriterien definieren
        $sort_by_criteria = [
            'title' => ['title' => 'Name'],
            'price_flight' => ['title' => 'Preis'],
            'flight_hours' => ['title' => 'Flugzeit'],
            'country_title' => ['title' => 'Land'],
        ];

        // Abfrage mit Pagination
        $locations = $this->locationRepository->getLocationsByTypeAndMonth($urlaubType, $monthName)
            ->with('country') // Country-Daten laden
            ->orderBy($sortBy, $sortDirection)
            ->paginate($itemsPerPage);

        // Klimadaten hinzufügen
        $weatherDataManager = new WeatherDataManagerLibrary();
        foreach ($locations as $location) {
            $location->climate_data = $weatherDataManager->fetchAndStoreWeatherData(
                $location->lat,
                $location->lon,
                $location->id
            );
        }

        // HeaderContent abrufen
        $step4Start = microtime(true);
        $headerContent = Cache::remember('header_content_random', 5 * 60, function () {
            return HeaderContent::inRandomOrder()->first();
        });
        Log::info('Step 4: HeaderContent geladen in ' . (microtime(true) - $step4Start) . ' Sekunden');

        // Validierung und Bildpfade
        $step5Start = microtime(true);
        $bgImgPath = $headerContent->bg_img ? Storage::url($headerContent->bg_img) : null;
        $mainImgPath = $headerContent->main_img ? Storage::url($headerContent->main_img) : null;

// Bildpfade validieren
        $bgImgPath = $headerContent->bg_img ? (Storage::exists($headerContent->bg_img) ? Storage::url($headerContent->bg_img) : (file_exists(public_path($headerContent->bg_img)) ? asset($headerContent->bg_img) : null)) : null;
        $mainImgPath = $headerContent->main_img ? (Storage::exists($headerContent->main_img) ? Storage::url($headerContent->main_img) : (file_exists(public_path($headerContent->main_img)) ? asset($headerContent->main_img) : null)) : null;



        Log::info('Step 5: Bildpfade und Validierung in ' . (microtime(true) - $step5Start) . ' Sekunden');

        return view('pages.main.search-results', [
            'locations' => $locations,
            'urlaubType' => $urlaubType,
            'monthId' => $monthId,
            'monthName' => $monthName,
            'items_per_page' => $itemsPerPage,
            'sort_by_criteria' => $sort_by_criteria,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
        ]);
    }


}
