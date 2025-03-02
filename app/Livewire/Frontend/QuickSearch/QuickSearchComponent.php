<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeRange;
use Livewire\Attributes\On;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Repositories\LocationRepository;

class QuickSearchComponent extends Component
{
    public $continent;
    public $price;
    public $urlaub;
    public $sonnenstunden;
    public $wassertemperatur;
    public $spezielle = [];
    public $nurInBesterReisezeit = false;

    public $totalLocations = 0;
    public $filteredLocations = 0;

    public $allLocations;
    public $isCollapsed = false;

    public $headerContent;
    public $bgImgPath;
    public $mainImgPath;

    protected $listeners = [
        'setSidebarState' => 'updateSidebarState',
    ];

    protected $rules = [
        'urlaub' => 'required|numeric|min:1|max:12',
        'continent' => 'nullable|string',
        'price' => 'nullable|string',
        'sonnenstunden' => 'nullable|string',
        'wassertemperatur' => 'nullable|string',
        'spezielle' => 'nullable|array',
    ];

    public $specialWishes = [
        'list_beach' => 'Strandurlaub',
        'list_citytravel' => 'Städtereise',
        'list_sports' => 'Sporturlaub',
        'list_island' => 'Inselurlaub',
        'list_culture' => 'Kulturreise',
        'list_nature' => 'Natururlaub',
       // 'list_watersport' => 'Wassersport',
       // 'list_wintersport' => 'Wintersport',
       // 'list_mountainsport' => 'Bergsport',
       // 'list_biking' => 'Fahrradurlaub',
       // 'list_fishing' => 'Angelurlaub',
       // 'list_amusement_park' => 'Freizeitpark',
       // 'list_water_park' => 'Wasserpark',
       // 'list_animal_park' => 'Tierpark',
    ];

    public function mount(LocationRepository $repository)
    {
        $this->continent = session('quicksearch.continent', null);
        $this->price = session('quicksearch.price', null);
        $this->urlaub = session('quicksearch.urlaub', date('n')); // Standard: aktueller Monat
        $this->sonnenstunden = session('quicksearch.sonnenstunden', null);
        $this->wassertemperatur = session('quicksearch.wassertemperatur', null);
        $this->spezielle = session('quicksearch.spezielle', []);
        $this->nurInBesterReisezeit = session('quicksearch.nurInBesterReisezeit', false);

        $this->isCollapsed = session('isCollapsed', filter_var(cookie('isCollapsed', false), FILTER_VALIDATE_BOOLEAN));

        $headerData = $repository->getHeaderContent();
        $this->headerContent = $headerData['headerContent'] ?? null;
        $this->bgImgPath = $headerData['bgImgPath'] ?? null;
        $this->mainImgPath = $headerData['mainImgPath'] ?? null;

        $this->allLocations = WwdeLocation::where('status', 'active')
            ->where('finished', 1)
            ->get();

        $this->totalLocations = $this->allLocations->count();
        $this->filteredLocations = session('quicksearch.filteredLocations', $this->totalLocations);

        $this->filterLocations(); // Initiale Filterung beim Mount
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        \Log::info('Updated Property', [
            'property' => $propertyName,
            'sonnenstunden' => $this->sonnenstunden,
            'urlaub' => $this->urlaub,
        ]);


        $this->filterLocations();

        session([
            'quicksearch.continent' => $this->continent,
            'quicksearch.price' => $this->price,
            'quicksearch.urlaub' => $this->urlaub,
            'quicksearch.sonnenstunden' => $this->sonnenstunden,
            'quicksearch.wassertemperatur' => $this->wassertemperatur,
            'quicksearch.spezielle' => $this->spezielle,
            'quicksearch.nurInBesterReisezeit' => $this->nurInBesterReisezeit,
            'quicksearch.filteredLocations' => $this->filteredLocations,
        ]);
    }

    public function filterLocations()
    {
        $query = WwdeLocation::query();
        $this->applyFilters($query);
        $this->filteredLocations = $query->count();
        $filteredIds = $query->pluck('id')->toArray();
        \Log::info('Filtered Locations with Sonnenstunden', [
            'count' => $this->filteredLocations,
            'ids' => $filteredIds,
            'sonnenstunden' => $this->sonnenstunden,
        ]);
        session(['quicksearch.filteredLocations' => $this->filteredLocations]);
        $this->dispatch('filteredLocationsUpdated', $this->filteredLocations);
    }

    private function applyFilters($query)
    {
        $query->active()->finished();

        if (empty($this->continent) && empty($this->price) && empty($this->urlaub) &&
            empty($this->sonnenstunden) && empty($this->wassertemperatur) && empty($this->spezielle)) {
            return;
        }

        if (!empty($this->continent)) {
            $query->where('continent_id', $this->continent);
        }

        if (!empty($this->price)) {
            $this->applyPriceFilter($query);
        }

        if (!empty($this->urlaub) && is_numeric($this->urlaub)) {
            $monthNumber = (int) $this->urlaub;
            if ($this->nurInBesterReisezeit) {
                $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($monthNumber)]);
            }
        }

        if (!empty($this->sonnenstunden) && !empty($this->urlaub) && is_numeric($this->urlaub)) {
            $minHours = (int) str_replace('more_', '', $this->sonnenstunden);
            $currentYear = now()->subYear()->year; // Aktuelles Jahr für Fallback

            $query->where(function ($q) use ($minHours, $currentYear) {
                // Primär: wwde_climates
                $q->whereHas('climates', function ($subQuery) use ($minHours) {
                    $subQuery->where('month_id', (int) $this->urlaub)
                             ->whereRaw('COALESCE(sunshine_per_day, 0) >= ?', [$minHours]);
                })->orWhereHas('monthlyClimates', function ($subQuery) use ($minHours, $currentYear) {
                    // Fallback: climate_monthly_data
                    $subQuery->where('month', (int) $this->urlaub)
                             ->where('year', $currentYear)
                             ->whereRaw('COALESCE(sunshine_hours, 0) >= ?', [$minHours]);
                });
            });
        }

        if (!empty($this->wassertemperatur) && !empty($this->urlaub) && is_numeric($this->urlaub)) {
            $minTemp = (int) str_replace('more_', '', $this->wassertemperatur);
            $currentYear = now()->subYear()->year; // 2025 für aktuelle Daten

            $query->where(function ($q) use ($minTemp, $currentYear) {
                $q->whereHas('climates', function ($subQuery) use ($minTemp) {
                    $subQuery->where('month_id', (int) $this->urlaub)
                             ->whereRaw('COALESCE(water_temperature, 0) >= ?', [$minTemp]);
                })->orWhereHas('monthlyClimates', function ($subQuery) use ($minTemp, $currentYear) {
                    $subQuery->where('month', (int) $this->urlaub)
                             ->where('year', $currentYear)
                             ->whereRaw('COALESCE(temperature_avg, 0) >= ?', [$minTemp]);
                });
            });
        }

        if (!empty($this->spezielle)) {
            foreach ((array)$this->spezielle as $wish) {
                $query->where($wish, 1);
            }
        }

        $filteredIds = $query->pluck('id')->toArray();
        \Log::info('Applied Filters Result', [
            'count' => count($filteredIds),
            'ids' => $filteredIds,
            'sonnenstunden' => $this->sonnenstunden,
            'urlaub' => $this->urlaub,
          //  'year' => $currentYear,
        ]);
    }

    #[On('goOn-Sidebarstate')]
    public function updateSidebarState($state)
    {
        $this->isCollapsed = $state;
        cookie()->queue('isCollapsed', $state, 60 * 24 * 30); // 30 Tage
        session(['isCollapsed' => $this->isCollapsed]);
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;
        cookie()->queue('isCollapsed', $this->isCollapsed, 60 * 24 * 30);
        session(['isCollapsed' => $this->isCollapsed]);
    }

    public function redirectToResults()
    {
        $this->validate(['urlaub' => 'required|numeric|min:1|max:12']);

        $query = WwdeLocation::query()->select('id');
        $this->applyFilters($query);

        $filteredIds = $query->pluck('id')->toArray();
        session(['quicksearch.filteredLocationIds' => $filteredIds]);

        \Log::info('Redirect to Results', [
            'filteredIds' => $filteredIds,
            'queryParams' => [
                'continent' => $this->continent,
                'price' => $this->price,
                'urlaub' => $this->urlaub,
                'sonnenstunden' => $this->sonnenstunden,
                'wassertemperatur' => $this->wassertemperatur,
                'spezielle' => $this->spezielle,
                'nurInBesterReisezeit' => $this->nurInBesterReisezeit,
            ],
        ]);

        $queryParams = [
            'continent' => $this->continent,
            'price' => $this->price,
            'urlaub' => $this->urlaub,
            'sonnenstunden' => $this->sonnenstunden,
            'wassertemperatur' => $this->wassertemperatur,
            'spezielle' => $this->spezielle,
            'nurInBesterReisezeit' => $this->nurInBesterReisezeit ? 1 : 0,
        ];

        $this->toggleCollapse();

        return redirect()->route('search.results', array_filter($queryParams));
    }

    private function applyPriceFilter($query)
    {
        $priceRange = WwdeRange::find($this->price);

        if ($priceRange) {
            $rangeToShow = $priceRange->Range_to_show;

            if (str_contains($rangeToShow, '-')) {
                [$minPrice, $maxPrice] = array_map('intval', explode('-', str_replace(['€', ' '], '', $rangeToShow)));
                $query->whereBetween('price_flight', [$minPrice, $maxPrice]);
            } elseif (str_contains($rangeToShow, '>')) {
                $minPrice = (int) filter_var($rangeToShow, FILTER_SANITIZE_NUMBER_INT);
                $query->where('price_flight', '>=', $minPrice);
            } else {
                $maxPrice = (int) filter_var($rangeToShow, FILTER_SANITIZE_NUMBER_INT);
                $query->where('price_flight', '<=', $maxPrice);
            }
        }
    }

    public function collapseSidebar()
    {
        $this->isCollapsed = true;
        $this->dispatch('sidebarCollapsed', true);
        cookie()->queue('isCollapsed', true, 60 * 24 * 30);
        session(['isCollapsed' => $this->isCollapsed]);

        return redirect()->route('detail_search');
    }

    public function resetFilters()
    {
        $this->continent = null;
        $this->price = null;
        $this->urlaub = date('n'); // Zurück auf aktuellen Monat
        $this->sonnenstunden = null;
        $this->wassertemperatur = null;
        $this->spezielle = [];
        $this->nurInBesterReisezeit = false;

        session()->forget([
            'quicksearch.continent',
            'quicksearch.price',
            'quicksearch.urlaub',
            'quicksearch.sonnenstunden',
            'quicksearch.wassertemperatur',
            'quicksearch.spezielle',
            'quicksearch.nurInBesterReisezeit',
            'quicksearch.filteredLocations',
            'quicksearch.filteredLocationIds',
        ]);

        $this->filterLocations();
    }

    public function render()
    {
        return view('livewire.frontend.quick-search.quick-search-component', [
            'continents' => WwdeContinent::select('id', 'title')->get(),
            'ranges' => WwdeRange::where('Type', 'Flight')->orderBy('sort')->get(),
            'months' => config('custom.months'),
        ]);
    }
}
