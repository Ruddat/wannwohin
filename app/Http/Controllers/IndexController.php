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

    protected $repository;

    public function __construct(LocationRepository $locationRepository, WeatherService $weatherService, LocationRepository $repository)
    {
        $this->locationRepository = $locationRepository;
        $this->weatherService = $weatherService;
        $this->repository = $repository; // Neues Repository injizieren
    }

    public function __invoke()
    {
        // Ladezeit-Start
        Log::info('IndexController: Start');
        $startTime = microtime(true);

        // 1. Top 10 Locations abrufen (nach Klicks sortiert)
        $step1Start = microtime(true);
        $topTenLocationIds = Cache::remember('top_ten_location_ids', 5 * 60, function () {
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

// Schleife bleibt unverändert
foreach ($topTenLocationsWithClima as $location) {

    // Prüfe, ob lat oder lon null sind
    if (empty($location->lat) || empty($location->lon)) {
        Log::warning("GeocodeService kann nicht aufgerufen werden: Ungültige Koordinaten für Location ID {$location->location_id}");
        continue; // Überspringt diesen Eintrag und fährt mit dem nächsten fort
    }

    if (empty($location->iso2) || empty($location->iso3)) {
        try {
            $geocodeService = new GeocodeService();
            $geocodeData = $geocodeService->searchByCoordinates((float) $location->lat, (float) $location->lon);

            // Prüfe, ob gültige Daten zurückkommen
            if (!empty($geocodeData['address']['country_code'])) {
                $iso2 = strtoupper($geocodeData['address']['country_code']);
                $iso3 = strtoupper($geocodeData['address']['ISO3166-2-lvl4'] ?? 'unknown');

                DB::table('wwde_locations')
                    ->where('id', $location->location_id)
                    ->update([
                        'iso2' => $iso2,
                        'iso3' => $iso3,
                    ]);

                Log::info("ISO-Codes aktualisiert für Location ID {$location->location_id}: {$iso2} / {$iso3}");
            } else {
                Log::warning("GeocodeService konnte keine Daten für Location ID {$location->location_id} liefern.");
            }
        } catch (\Exception $e) {
            Log::error("Fehler beim Abrufen der Geodaten für Location ID {$location->location_id}: " . $e->getMessage());
        }
    }

    // Sicherstellen, dass Climate-Daten ein Array sind, um Fehler zu vermeiden
    $climateData = [
        'daily_temperature' => isset($location->daily_temperature) ? intval($location->daily_temperature) : 'N/A',
        'night_temperature' => isset($location->night_temperature) ? intval($location->night_temperature) : 'N/A',
        'humidity' => isset($location->humidity) ? intval($location->humidity) : 'N/A',
        'sunshine_per_day' => isset($location->sunshine_per_day) ? intval($location->sunshine_per_day) : 'N/A',
        'water_temperature' => isset($location->water_temperature) ? intval($location->water_temperature) : 'N/A',
        'weather_description' => $location->weather_description ?? 'N/A',
        'weather_icon' => $location->icon ?? 'default.png',
    ];

//dd($climateData);


    // Daten in das Array einfügen
    $TopTenLocationWithClima[] = [
        'location_id' => $location->location_id,
        'location_title' => $location->location_title ?? 'Unbekannte Location',
        'location_alias' => $location->location_alias ?? '',
        'iso2' => $location->iso2 ?? 'N/A',
        'iso3' => $location->iso3 ?? 'N/A',
        'continent' => $location->continent_alias ?? 'Unbekannt',
        'country' => $location->country_alias ?? 'Unbekannt',
        'climate_data' => $climateData,
    ];
}

// Begrenzung auf 10 Einträge
$TopTenLocationWithClima = array_slice($TopTenLocationWithClima, 0, 10);

//dd($TopTenLocationWithClima);

// Optional: Logge die Ergebnisse zur Überprüfung
Log::info('TopTenLocationWithClima:', $TopTenLocationWithClima);


        //dd($TopTenLocationWithClima);

        // Optional: Logge die Ergebnisse zur Überprüfung
        Log::info('TopTenLocationWithClima:', $TopTenLocationWithClima);

        // 4. Gesamtanzahl der Locations abrufen
        $totalLocations = Cache::remember('total_finished_locations', 5 * 60, function () {
            return DB::table('wwde_locations')->count();
        });

         // HeaderContent abrufen
         $headerContent = HeaderContent::inRandomOrder()->first();

         // Bildpfade validieren
         $bgImgPath = $headerContent->bg_img ?
             (Storage::exists($headerContent->bg_img)
                 ? Storage::url($headerContent->bg_img)
                 : (file_exists(public_path($headerContent->bg_img))
                     ? asset($headerContent->bg_img)
                     : null))
             : null;

         $mainImgPath = $headerContent->main_img ?
             (Storage::exists($headerContent->main_img)
                 ? Storage::url($headerContent->main_img)
                 : (file_exists(public_path($headerContent->main_img))
                     ? asset($headerContent->main_img)
                     : null))
             : null;

         // Gesamtladezeit loggen
         Log::info('IndexController: Gesamtladezeit ' . (microtime(true) - $startTime) . ' Sekunden');

        // Gesamtladezeit loggen
        //Log::info('IndexController: Gesamtladezeit ' . (microtime(true) - $startTime) . ' Sekunden');

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
        // Sicherstellen, dass monthId eine gültige Zahl zwischen 1 und 12 ist
        if (!is_numeric($monthId) || $monthId < 1 || $monthId > 12) {
            abort(400, "Ungültiger Monat: $monthId");
        }

        // Mapping der Urlaubstypen zu Datenbank-Feldern
        $urlaubTypeMap = [
            'strand-reise' => 'list_beach',
            'staedte-reise' => 'list_citytravel',
            'sport-reise' => 'list_sports',
            'insel-reise' => 'list_island',
            'kultur-reise' => 'list_culture',
            'natur-reise' => 'list_nature',
            'wassersport-reise' => 'list_watersport',
            'wintersport-reise' => 'list_wintersport',
            'mountainsport-reise' => 'list_mountainsport',
            'biking-reise' => 'list_biking',
            'fishing-reise' => 'list_fishing',
            'amusement-park-reise' => 'list_amusement_park',
            'water-park-reise' => 'list_water_park',
            'animal-park-reise' => 'list_animal_park',
        ];

        // Mapping der Urlaubstypen zu Icons
        $iconMap = [
            'list_beach' => '<i class="fas fa-umbrella-beach fa-lg me-1" title="Strand"></i>',
            'list_citytravel' => '<i class="fas fa-city fa-lg me-1" title="Städtereise"></i>',
            'list_sports' => '<i class="fas fa-biking fa-lg me-1" title="Sport"></i>',
            'list_island' => '<img style="margin-top: -3px;height: 30px;" src="' . asset('img/insel-icon.png') . '" alt="Insel" title="Insel">',
            'list_culture' => '<i class="fas fa-theater-masks fa-lg me-1" title="Kultur"></i>',
            'list_nature' => '<i class="fas fa-tree fa-lg me-1" title="Natur"></i>',
            'list_watersport' => '<i class="fas fa-swimmer fa-lg me-1" title="Wassersport"></i>',
            'list_wintersport' => '<i class="fas fa-snowflake fa-lg me-1" title="Wintersport"></i>',
            'list_mountainsport' => '<i class="fas fa-mountain fa-lg me-1" title="Bergsport"></i>',
            'list_biking' => '<i class="fas fa-biking fa-lg me-1" title="Radfahren"></i>',
            'list_fishing' => '<i class="fas fa-fish fa-lg me-1" title="Angeln"></i>',
            'list_amusement_park' => '<i class="fas fa-ticket-alt fa-lg me-1" title="Freizeitpark"></i>',
            'list_water_park' => '<i class="fas fa-water fa-lg me-1" title="Wasserpark"></i>',
            'list_animal_park' => '<i class="fas fa-paw fa-lg me-1" title="Tierpark"></i>',
        ];

        // Sicherstellen, dass der Urlaubstyp existiert
        if (!isset($urlaubTypeMap[$urlaubType])) {
            abort(400, "Ungültiger Urlaubstyp: $urlaubType");
        }

        // Das entsprechende Datenbankfeld für diesen Urlaubstyp abrufen
        $column = $urlaubTypeMap[$urlaubType];

        // Das passende Icon für den Urlaubstyp abrufen (falls vorhanden)
        $urlaubTypeIcon = $iconMap[$column] ?? null;

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

        // Abfrage mit Pagination (ohne Umwandlung des Monatsnamens)
        $locations = $this->locationRepository->getLocationsByTypeAndMonth($urlaubType, (int) $monthId)
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
        $headerContent = HeaderContent::inRandomOrder()->first();

        // Bildpfade validieren
        $bgImgPath = $headerContent->bg_img ?
            (Storage::exists($headerContent->bg_img)
                ? Storage::url($headerContent->bg_img)
                : (file_exists(public_path($headerContent->bg_img))
                    ? asset($headerContent->bg_img)
                    : null))
            : null;

        $mainImgPath = $headerContent->main_img ?
            (Storage::exists($headerContent->main_img)
                ? Storage::url($headerContent->main_img)
                : (file_exists(public_path($headerContent->main_img))
                    ? asset($headerContent->main_img)
                    : null))
            : null;

            foreach ($locations as $location) {
                $icons = [];
                foreach ($iconMap as $flag => $icon) {
                    if ($location->$flag) {
                        $icons[] = $icon;
                    }
                }
                $location->icons = implode(' ', $icons);
            }


        return view('pages.main.search-results', [
            'locations' => $locations,
            'urlaubType' => $urlaubType,
            'urlaubTypeIcon' => $urlaubTypeIcon, // Icon an die View übergeben
            'monthId' => $monthId,
            'items_per_page' => $itemsPerPage,
            'sort_by_criteria' => $sort_by_criteria,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
        ]);
    }



}
