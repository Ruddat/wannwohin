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
        $this->loadHeaderData($repository);
        $this->loadSearchParams();
        $this->updateActiveFilters();

        // Session zurücksetzen und neu berechnen
        session()->forget('quicksearch.filteredLocationIds');
        if (!session()->has('quicksearch.filteredLocationIds')) {
            $this->updateFilteredLocationIds();
        }
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
        $this->spezielle = request('spezielle') ?? [];
        $this->nurInBesterReisezeit = filter_var(request('nurInBesterReisezeit', false), FILTER_VALIDATE_BOOLEAN);
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
        if (array_key_exists($property, $this->activeFilters) || $property === 'nurInBesterReisezeit') {
            $this->activeFilters[$property] = $this->$property;
            $this->updateFilteredLocationIds();
            $this->resetPage();
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
        if ($filterKey === 'urlaub') {
            return; // Monat darf nicht entfernt werden
        }

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
        $query = WwdeLocation::query()->select('id');
        $this->applyFilters($query);

        $filteredIds = $query->pluck('id')->toArray();
        session(['quicksearch.filteredLocationIds' => $filteredIds]);

        \Log::info('Updated filtered Location-IDs:', ['count' => count($filteredIds), 'ids' => $filteredIds]);
    }

    private function applyFilters($query)
    {
        $query->active()->finished();

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

        // Filter nur über wwde_climates für Sonnenstunden
        if (!empty($this->sonnenstunden) && !empty($this->urlaub) && is_numeric($this->urlaub)) {
            $minHours = (int) str_replace('more_', '', $this->sonnenstunden);

            $query->whereHas('climates', function ($subQuery) use ($minHours) {
                $subQuery->where('month_id', (int) $this->urlaub)
                         ->whereRaw('COALESCE(sunshine_per_day, 0) >= ?', [$minHours]);
            });
        }

        // Filter nur über wwde_climates für Wassertemperatur
        if (!empty($this->wassertemperatur) && !empty($this->urlaub) && is_numeric($this->urlaub)) {
            $minTemp = (int) str_replace('more_', '', $this->wassertemperatur);

            $query->whereHas('climates', function ($subQuery) use ($minTemp) {
                $subQuery->where('month_id', (int) $this->urlaub)
                         ->whereRaw('COALESCE(water_temperature, 0) >= ?', [$minTemp]);
            });
        }

        if (!empty($this->spezielle)) {
            foreach ((array)$this->spezielle as $wish) {
                $query->where($wish, 1);
            }
        }
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

    public function getFilterLabel($key, $value)
    {
        return match ($key) {
            'continent' => $this->getContinentName($value),
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
                ->with(['climates', 'country'])
                ->where('wwde_locations.status', 'active') // Explizite Tabellenangabe
                ->whereNotNull('country_id')
                ->whereIn('wwde_locations.id', $filteredLocationIds);

            $allowedSortFields = ['price_flight', 'title', 'continent_id', 'country_id', 'flight_hours', 'water_temperature'];

            if (in_array($this->sortBy, $allowedSortFields)) {
                if ($this->sortBy === 'country_id') {
                    $query->select('wwde_locations.*')
                          ->leftJoin('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id')
                          ->orderByRaw('wwde_countries.title COLLATE utf8mb4_german2_ci ' . $this->sortDirection)
                          ->with('country');
                } elseif ($this->sortBy === 'water_temperature') {
                    $query->select('wwde_locations.*')
                          ->leftJoin('wwde_climates', function ($join) {
                              $join->on('wwde_locations.id', '=', 'wwde_climates.location_id')
                                   ->where('wwde_climates.month_id', '=', (int) $this->urlaub);
                          })->orderByRaw('COALESCE(wwde_climates.water_temperature, 0) ' . $this->sortDirection);
                } else {
                    $query->orderBy($this->sortBy, $this->sortDirection);
                }
            } elseif ($this->sortBy === 'climate_data->main->temp') {
                $query->select('wwde_locations.*')
                      ->leftJoin('wwde_climates', function ($join) {
                          $join->on('wwde_locations.id', '=', 'wwde_climates.location_id')
                               ->where('wwde_climates.month_id', '=', (int) $this->urlaub);
                      })->orderByRaw('COALESCE(wwde_climates.daily_temperature, 0) ' . $this->sortDirection);
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
        $month = (int) $this->urlaub;
        $minHours = !empty($this->sonnenstunden) ? (int) str_replace('more_', '', $this->sonnenstunden) : null;

        // Nur wwde_climates verwenden
        $climate = $location->climates
            ->where('month_id', $month)
            ->filter(function ($data) use ($minHours) {
                return $minHours === null || ($data->sunshine_per_day && $data->sunshine_per_day >= $minHours);
            })
            ->first();

        if ($climate) {
            $location->climate_data = [
                'main' => ['temp' => $climate->daily_temperature ?? null],
                'temp_max' => $climate->daily_temperature ?? null,
                'temp_min' => $climate->night_temperature ?? null,
                'sunshine_hours' => $climate->sunshine_per_day ?? null,
                'water_temperature' => $climate->water_temperature ?? null,
                'rainy_days' => $climate->rainy_days ?? null, // Annahme: Spalte 'rainy_days' existiert in wwde_climates
            ];
        } else {
            $location->climate_data = [
                'main' => ['temp' => null],
                'temp_max' => null,
                'temp_min' => null,
                'sunshine_hours' => null,
                'water_temperature' => null,
                'rainy_days' => null,
            ];
        }

        \Log::info('Enriched Location Data', [
            'location_id' => $location->id,
            'sunshine_hours' => $location->climate_data['sunshine_hours'] ?? 'N/A',
            'water_temperature' => $location->climate_data['water_temperature'] ?? 'N/A',
            'rainy_days' => $location->climate_data['rainy_days'] ?? 'N/A',
            'month' => $month,
            'source' => $climate ? 'wwde_climates' : 'none',
        ]);

        return $location;
    }
}
