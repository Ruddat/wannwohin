<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeRange;
use App\Models\WwdeLocation;
use Livewire\WithPagination;
use App\Models\HeaderContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Repositories\LocationRepository;

class SearchResultsComponent extends Component
{
    use WithPagination;

    // Suchfilter
    public $continent, $price, $urlaub, $sonnenstunden, $wassertemperatur, $spezielle;
    public $nurInBesterReisezeit = false;

    // Sortierung und Pagination
    public $sortBy = 'title', $sortDirection = 'asc', $perPage = 10;

    // UI-Daten
    public $headerContent, $bgImgPath, $mainImgPath;
    public $activeFilters = [], $totalResults = 0;

    // Spezielle Wünsche
    public $specialWishes = [
        'list_beach' => 'Strandurlaub',
        'list_citytravel' => 'Städtereise',
        'list_sports' => 'Sporturlaub',
        'list_island' => 'Inselurlaub',
        'list_culture' => 'Kulturreise',
        'list_nature' => 'Natururlaub',
        'list_watersport' => 'Wassersport',
        'list_wintersport' => 'Wintersport',
        'list_mountainsport' => 'Bergsport',
        'list_biking' => 'Fahrradurlaub',
        'list_fishing' => 'Angelurlaub',
        'list_amusement_park' => 'Freizeitpark',
        'list_water_park' => 'Wasserpark',
        'list_animal_park' => 'Tierpark',
    ];

    public function mount(LocationRepository $repository)
    {
        // Header-Daten laden und cachen
        $this->loadHeaderData($repository);

        // Suchparameter aus der URL laden
        $this->loadSearchParams();

        // Aktive Filter initialisieren
        $this->updateActiveFilters();
    }

    private function loadHeaderData(LocationRepository $repository)
    {
        $headerContent = Cache::remember('header_content', 3600, fn() => HeaderContent::inRandomOrder()->first());

        $this->bgImgPath = $headerContent->bg_img
            ? $this->resolveImagePath($headerContent->bg_img)
            : null;

        $this->mainImgPath = $headerContent->main_img
            ? $this->resolveImagePath($headerContent->main_img)
            : null;

        session(['headerData' => [
            'bgImgPath' => $this->bgImgPath,
            'mainImgPath' => $this->mainImgPath,
            'title' => $headerContent->title,
            'title_text' => $headerContent->main_text,
            'main_text' => $headerContent->content,
        ]]);
    }

    private function resolveImagePath($path)
    {
        return Storage::exists($path)
            ? Storage::url($path)
            : (file_exists(public_path($path)) ? asset($path) : null);
    }

    private function loadSearchParams()
    {
        $this->continent = request('continent');
        $this->price = request('price');
        $this->urlaub = request('urlaub');
        $this->sonnenstunden = request('sonnenstunden');
        $this->wassertemperatur = request('wassertemperatur');
        $this->spezielle = request('spezielle');
        $this->nurInBesterReisezeit = request('nurInBesterReisezeit', false);
    }

    private function updateActiveFilters()
    {
        $this->activeFilters = array_filter([
            'continent' => $this->continent,
            'price' => $this->price,
            'urlaub' => $this->urlaub,
            'sonnenstunden' => $this->sonnenstunden,
            'wassertemperatur' => $this->wassertemperatur,
            'spezielle' => $this->spezielle,
        ]);
    }

    public function updated($property)
    {
        if (array_key_exists($property, $this->activeFilters)) {
            $this->activeFilters[$property] = $this->$property;
            $this->resetPage();
            $this->updateFilteredLocationIds();
        }
    }

    public function sortBy($field)
    {
        $this->sortDirection = ($this->sortBy === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortBy = $field;
        $this->resetPage();
    }

    public function toggleSortDirection()
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function removeFilter($filterKey, $value = null)
    {
        if ($filterKey === 'spezielle' && $value) {
            $this->spezielle = array_filter((array)$this->spezielle, fn($item) => $item !== $value);
            $this->activeFilters['spezielle'] = $this->spezielle;
        } else {
            $this->$filterKey = null;
            unset($this->activeFilters[$filterKey]);
        }

        $this->updateFilteredLocationIds();
        $this->resetPage();
    }

    private function updateFilteredLocationIds()
    {
        $query = WwdeLocation::query()
            ->select('id')
            ->active()
            ->finished()
            ->filterByContinent($this->continent)
            ->filterByPrice($this->price)
            ->filterBySpecials($this->spezielle);

        // Reisezeit-Filter
        if ($this->urlaub && is_numeric($this->urlaub)) {
            $monthNumber = (int)$this->urlaub;
            if ($this->nurInBesterReisezeit) {
                $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($monthNumber)]);
            }
        }

        // Sonnenstunden-Filter
        if ($this->sonnenstunden && $this->urlaub) {
            $minHours = (int)str_replace('more_', '', $this->sonnenstunden);
            $lastYear = now()->subYear()->year;
            $query->where(function ($q) use ($minHours, $lastYear) {
                $q->whereHas('climates', fn($sub) => $sub->where('month_id', (int)$this->urlaub)->where('sunshine_per_day', '>=', $minHours))
                  ->orWhereHas('historicalClimates', fn($sub) => $sub->where('month', (int)$this->urlaub)->where('year', $lastYear)->where('sunshine_hours', '>=', $minHours));
            });
        }

        // Wassertemperatur-Filter
        if ($this->wassertemperatur && $this->urlaub) {
            $minTemp = (int)str_replace('more_', '', $this->wassertemperatur);
            $lastYear = now()->subYear()->year;
            $query->where(function ($q) use ($minTemp, $lastYear) {
                $q->whereHas('climates', fn($sub) => $sub->where('month_id', (int)$this->urlaub)->where('water_temperature', '>=', $minTemp))
                  ->orWhereHas('historicalClimates', fn($sub) => $sub->where('month', (int)$this->urlaub)->where('year', $lastYear)->where('temperature_avg', '>=', $minTemp));
            });
        }

        $filteredIds = $query->pluck('id')->toArray();
        session(['quicksearch.filteredLocationIds' => $filteredIds]);
    }

    public function getFilterLabel($key, $value)
    {
        return match ($key) {
            'continent' => $this->getContinentName($value), // Hier wird der Kontinent ergänzt
            'price' => WwdeRange::find($value)?->Range_to_show ?? $value,
            'urlaub' => config('custom.months')[$value] ?? $value,
            'sonnenstunden' => "Mehr als " . str_replace('more_', '', $value) . " Sonnenstunden",
            'wassertemperatur' => "Mehr als " . str_replace('more_', '', $value) . "°C Wassertemperatur",
            'spezielle' => $this->specialWishes[$value] ?? $value,
            default => $value,
        };
    }
    
    private function getContinentName($continentId)
    {
        $continents = Cache::remember('continents_list', 3600, fn() =>
            \App\Models\WwdeContinent::pluck('title', 'id')->toArray()
        );

        return $continents[$continentId] ?? 'Unbekannter Kontinent';
    }


    public function render()
    {
        $filteredLocationIds = session('quicksearch.filteredLocationIds', []);
        if (empty($filteredLocationIds)) {
            $locations = collect();
        } else {
            $query = WwdeLocation::query()
                ->with(['climates', 'historicalClimates' => fn($q) => $q->where('year', '>=', now()->subYear()->year)])
                ->active()
                ->whereIn('id', $filteredLocationIds);

            // Sortierung
            $allowedSortFields = ['price_flight', 'title', 'continent_id', 'country_id', 'flight_hours'];
            if (in_array($this->sortBy, $allowedSortFields)) {
                $query->orderBy($this->sortBy, $this->sortDirection);
            } elseif ($this->sortBy === 'climate_data->main->temp') {
                $query->orderByRaw("COALESCE(
                    (SELECT MAX(temperature_avg) FROM climate_monthly_data WHERE location_id = wwde_locations.id AND year >= ?),
                    (SELECT MAX(temperature_max) FROM climate_monthly_data WHERE location_id = wwde_locations.id AND year >= ?),
                    (SELECT MAX(temperature_min) FROM climate_monthly_data WHERE location_id = wwde_locations.id AND year >= ?)
                ) {$this->sortDirection}", [now()->subYear()->year, now()->subYear()->year, now()->subYear()->year]);
            } else {
                $query->orderBy('title', 'asc');
            }

            $locations = $query->paginate($this->perPage)->withQueryString();
            $locations->transform(fn($location) => $this->enrichLocationData($location));
        }

        $this->totalResults = $locations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $locations->total() : $locations->count();

        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $locations,
            'selectedMonth' => $this->urlaub,
        ]);
    }

    private function enrichLocationData($location)
    {
        if (empty($location->climates) && ($historical = $location->historicalClimates->last())) {
            $location->climate_data = [
                'main' => ['temp' => $historical->temperature_avg],
                'temp_max' => $historical->temperature_max,
                'temp_min' => $historical->temperature_min,
                'sunshine_hours' => $historical->sunshine_hours,
            ];
        }
        return $location;
    }
}
