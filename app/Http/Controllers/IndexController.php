<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\WwdeLocation;
use App\Services\SeoService;
use App\Helpers\HeaderHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Repositories\LocationRepository;
use App\Library\WeatherDataManagerLibrary;

class IndexController extends Controller
{
    protected $locationRepository;
    protected $seoService;

    public function __construct(LocationRepository $locationRepository, SeoService $seoService)
    {
        $this->locationRepository = $locationRepository;
        $this->seoService = $seoService;
    }

    public function __invoke()
    {
        Log::info('IndexController: Start');
        $startTime = microtime(true);

        // Top 10 Locations mit Klimadaten laden
        $topTenLocations = $this->getTopTenLocations();

//dd($topTenLocations);


        // Gesamtanzahl der Locations
        $totalLocations = Cache::remember('total_finished_locations', 5 * 60, fn() => WwdeLocation::count());

        // Header-Daten laden
        $headerData = HeaderHelper::getHeaderContent();


        session(['headerData' => $headerData]);

        // Kategorien und Vorschläge
        $categories = [
            'inspiration' => 'Inspiration',
            'wetter' => 'Wetter',
            'erlebnis' => 'Erlebnis',
            'sport' => 'Sport',
            'freizeitpark' => 'Freizeitpark',
        ];

        $suggestions = Cache::remember('suggestions', 15 * 60, fn() => DB::table('mod_location_filters')
            ->whereIn('text_type', array_values($categories))
            ->get()
            ->groupBy('text_type'));

        $icons = [
            'inspiration' => 'fas fa-th-large',
            'wetter' => 'fas fa-sun',
            'erlebnis' => 'fas fa-map-signs',
            'sport' => 'fas fa-running',
            'freizeitpark' => 'fas fa-ticket-alt',
        ];

        Log::info('IndexController: Gesamtladezeit ' . (microtime(true) - $startTime) . ' Sekunden');

    // SEO für die Startseite abrufen oder generieren
    $seo = $this->seoService->getSeoData([
        'model_type'  => 'homepage',
        'model_id'    => 1,
        'title'       => 'Wann-Wohin - Deine Reiseplattform',
        'description' => 'Finde die besten Reiseziele, Wetterdaten und Top-Locations für deinen nächsten Urlaub.',
        'image'       => asset('img/homepage.jpg'),
        'canonical'   => url('/'),
    ]);


        return view('pages.main.index', [
            'seo' => $seo,
            'top_ten' => $topTenLocations,
            'total_locations' => $totalLocations,
            'panorama_location_picture' => $headerData['bgImgPath'],
            'main_location_picture' => $headerData['mainImgPath'],
            'panorama_location_text' => $headerData['title_text'] ?? null,
            'categories' => $categories,
            'suggestions' => $suggestions,
            'icons' => $icons,
        ]);
    }

private function getTopTenLocations(): array
{
    return Cache::remember('homepage_top_ten', 600, function () {

        $months = [
            now()->format('Y-m'),
            now()->subMonth()->format('Y-m'),
            now()->subMonths(2)->format('Y-m'),
        ];

        // 1️⃣ Ranking aggregieren
        $ranking = DB::table('stat_location_search_histories')
            ->select('location_id', DB::raw('SUM(search_count) as total'))
            ->whereIn('month', $months)
            ->groupBy('location_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        if ($ranking->isEmpty()) {
            return [];
        }

        $topTenIds = $ranking->pluck('location_id')->toArray();

        $currentMonth = (int) now()->format('n');

        // 2️⃣ Letztes verfügbares Klima-Jahr (gecached)
        $latestClimateYear = Cache::remember('latest_climate_year', 3600, function () {
            return \App\Models\WwdeClimate::max('year');
        });

        if (!$latestClimateYear) {
            return [];
        }

        // 3️⃣ Nur benötigte Klimadaten eager laden
        $locations = WwdeLocation::query()
            ->whereIn('id', $topTenIds)
            ->with([
                'continent:id,alias',
                'country:id,alias',
                'climates' => function ($q) use ($currentMonth, $latestClimateYear) {
                    $q->where('month_id', $currentMonth)
                      ->where('year', $latestClimateYear);
                }
            ])
            ->orderByRaw('FIELD(id,' . implode(',', $topTenIds) . ')')
            ->get();

        // 4️⃣ Mapping
        return $locations->map(function ($location) {

            $climate = $location->climates->first();

            $temp  = $climate->daily_temperature ?? null;
            $rain  = $climate->rain_probability ?? null;
            $sun   = $climate->sunshine_per_day ?? null;
            $humid = $climate->humidity ?? null;

$climateMeta = $this->resolveClimateMeta($sun, $rain, $temp);

            return [
                'location_id'    => $location->id,
                'location_title' => $location->title,
                'location_alias' => $location->alias,
                'iso2'           => $location->iso2,
                'iso3'           => $location->iso3,
                'continent'      => $location->continent->alias ?? null,
                'country'        => $location->country->alias ?? null,
                'climate_data'   => [
                    'daily_temperature' => $temp,
                    'night_temperature' => $climate->night_temperature ?? null,
                    'humidity'          => $humid,
                    'sunshine_per_day'  => $sun,
                    'water_temperature' => $climate->water_temperature ?? null,
                   // 'icon'              => $this->resolveClimateIcon($sun, $rain, $temp),


'icon'        => $climateMeta['icon'],
'description' => $climateMeta['description'],

                ],
            ];
        })->toArray();

    });
}


private function resolveClimateMeta($sun, $rain, $temp): array
{
    if (!$sun && !$rain && !$temp) {
        return [
            'icon' => 'default',
            'description' => 'Keine Klimadaten verfügbar'
        ];
    }

    if ($rain > 60) {
        return [
            'icon' => 'rainy',
            'description' => 'Regnerische Bedingungen'
        ];
    }

    if ($temp !== null && $temp <= 0) {
        return [
            'icon' => 'snowy',
            'description' => 'Winterliche Temperaturen'
        ];
    }

    if ($sun > 8 && $temp > 25) {
        return [
            'icon' => 'sunny',
            'description' => 'Sonnig und warm'
        ];
    }

    if ($sun > 5) {
        return [
            'icon' => 'partly-cloudy',
            'description' => 'Teilweise sonnig'
        ];
    }

    return [
        'icon' => 'cloudy',
        'description' => 'Überwiegend bewölkt'
    ];
}


private function resolveClimateIcon($sun, $rain, $temp): string
{
    if (!$sun && !$rain && !$temp) {
        return 'default';
    }

    if ($rain > 60) return 'rainy';
    if ($temp <= 0) return 'snowy';
    if ($sun > 8 && $temp > 25) return 'sunny';
    if ($sun > 5) return 'partly-cloudy';

    return 'cloudy';
}


    private function updateMissingIsoCodes($location): void
    {
        if (empty($location->iso2) || empty($location->iso3)) {
            if (empty($location->lat) || empty($location->lon)) {
                Log::warning("Ungültige Koordinaten für Location ID {$location->id}");
                return;
            }

            try {
                $geocodeData = (new \App\Services\GeocodeService())->searchByCoordinates(
                    (float)$location->lat,
                    (float)$location->lon
                );

                if ($iso2 = strtoupper($geocodeData['address']['country_code'] ?? '')) {
                    $iso3 = strtoupper($geocodeData['address']['ISO3166-2-lvl4'] ?? 'unknown');
                    $location->update(['iso2' => $iso2, 'iso3' => $iso3]);
                    Log::info("ISO-Codes aktualisiert für Location ID {$location->id}: {$iso2} / {$iso3}");
                }
            } catch (\Exception $e) {
                Log::error("Fehler bei Geodaten für Location ID {$location->id}: {$e->getMessage()}");
            }
        }
    }

    public function searchResults($urlaubType, $monthId)
    {
        if (!is_numeric($monthId) || $monthId < 1 || $monthId > 12) {
            abort(400, "Ungültiger Monat: $monthId");
        }

        $monthName = Carbon::create()->month((int)$monthId)->translatedFormat('F');
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

        if (!isset($urlaubTypeMap[$urlaubType])) {
            abort(400, "Ungültiger Urlaubstyp: $urlaubType");
        }

        $urlaubTypeText = ucfirst(str_replace(['-reise', '-'], [' ', ' '], $urlaubType));

    // SEO für Suchergebnisse abrufen oder generieren
    $seo = $this->seoService->getSeoData([
        'model_type'  => 'search_results',
        'model_id'    => "{$urlaubType}-{$monthId}",
        'title'       => "{$urlaubTypeText} im {$monthName} - Beste Reiseziele",
        'description' => "Hier findest du die besten Orte für eine {$urlaubTypeText} im {$monthName}. Entdecke Klima, Wetter und Preise.",
        'canonical'   => url("/suche/{$urlaubType}/{$monthId}"),
        'image'       => asset("img/search/{$urlaubType}.jpg"),
    ]);



        $column = $urlaubTypeMap[$urlaubType];
        $urlaubTypeIcon = $iconMap[$column] ?? null;

        $itemsPerPage = (int)request('items_per_page', 10);
        $sortBy = request('sort_by', 'title');
        $sortDirection = request('sort_direction', 'asc');

        $locations = $this->locationRepository->getLocationsByTypeAndMonth($urlaubType, (int)$monthId)
            ->with('country')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($itemsPerPage);

        $weatherDataManager = new WeatherDataManagerLibrary();
        foreach ($locations as $location) {
            $location->climate_data = Cache::remember("weather_{$location->id}", 60 * 60, fn() =>
                $weatherDataManager->fetchAndStoreWeatherData($location->lat, $location->lon, $location->id)
            );
            $location->icons = $this->generateIcons($location, $iconMap);
        }

        $headerContent = \App\Models\ModQuickFilterItem::where('slug', $urlaubType)->firstOrFail();
        [$bgImgPath, $mainImgPath] = $this->resolveImagePaths($headerContent);

        session(['headerData' => [
            'bgImgPath' => $bgImgPath,
            'mainImgPath' => $mainImgPath,
            'title' => $headerContent->title,
            'title_text' => $headerContent->title_text,
            'main_text' => $headerContent->content,
        ]]);

        session(compact('locations', 'urlaubType', 'monthId', 'monthName', 'itemsPerPage', 'urlaubTypeIcon'));

        return view('pages.main.search-results', [
            'locations' => $locations,
            'urlaubType' => $urlaubType,
            'monthId' => $monthId,
            'monthName' => $monthName,
            'items_per_page' => $itemsPerPage,
            'sort_by_criteria' => [
                'title' => ['title' => 'Name'],
                'price_flight' => ['title' => 'Preis'],
                'flight_hours' => ['title' => 'Flugzeit'],
                'country_title' => ['title' => 'Land'],
            ],
            'header_title' => $headerContent->title,
            'header_title_text' => $headerContent->title_text,
            'main_text' => $headerContent->content,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
        ]);
    }

    private function generateIcons($location, array $iconMap): string
    {
        $icons = array_filter(array_map(fn($flag, $icon) => $location->$flag ? $icon : null, array_keys($iconMap), $iconMap));
        return implode(' ', $icons);
    }

    private function resolveImagePaths($headerContent): array
    {
        $bgImgPath = $headerContent->panorama
            ? (Storage::exists($headerContent->panorama)
                ? Storage::url($headerContent->panorama)
                : (file_exists(public_path($headerContent->panorama)) ? asset($headerContent->panorama) : null))
            : null;

        $mainImgPath = $headerContent->image
            ? (Storage::exists($headerContent->image)
                ? Storage::url($headerContent->image)
                : (file_exists(public_path($headerContent->image)) ? asset($headerContent->image) : null))
            : null;

        return [$bgImgPath, $mainImgPath];
    }
}
