<?php

namespace App\Livewire\Frontend\Search;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\DetailSearchRepository;

class DetailSearchV2 extends Component
{
    use WithPagination;

    // =========================
    // Filter
    // =========================
    public array $continents = [];
    public string $country = '';
    public string $climate_zone = '';
    public string $month = '';
    public string $price_tendency = '';
    public string $currency = '';
    public string $language = '';
    public string $flight_duration = '';
    public array $activities = [];

    // Preise
    public string $price_flight = '';
    public string $price_hotel = '';
    public string $price_mietwagen = '';
    public string $price_pauschalreise = '';


// Generelle Infos
public string $range_flight = '';   // Preis pro Person


    // Search
    public string $search = '';

    // Sorting
    public string $sortBy = 'title';
    public string $sortDirection = 'asc';

    // Pagination
    public int $perPage = 12;

    protected DetailSearchRepository $repository;

    // =========================
    // Query String (SEO-safe)
    // =========================
    protected $queryString = [
        'continents'      => ['except' => [], 'as' => 'c'],
        'country'         => ['except' => '', 'as' => 'co'],
        'climate_zone'    => ['except' => '', 'as' => 'cz'],
        'month'           => ['except' => '', 'as' => 'm'],
        'price_tendency'  => ['except' => '', 'as' => 'pt'],
        'search'          => ['except' => '', 'as' => 's'],
        'sortBy'          => ['except' => 'title'],
        'sortDirection'   => ['except' => 'asc'],
        'page'            => ['except' => 1],
        // Preise
        'price_flight'        => ['except' => '', 'as' => 'pf'],
        'price_hotel'         => ['except' => '', 'as' => 'ph'],
        'price_mietwagen'     => ['except' => '', 'as' => 'pm'],
        'price_pauschalreise' => ['except' => '', 'as' => 'pp'],


    ];

    // =========================
    // Boot
    // =========================
    public function boot(DetailSearchRepository $repository): void
    {
        $this->repository = $repository;
    }

    // =========================
    // Lifecycle
    // =========================
    public function updated($property): void
    {
        if ($property === 'page') {
            return;
        }

        $this->resetPage();
    }

    // =========================
    // Actions
    // =========================
    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function resetAllFilters(): void
    {
        $this->reset([
            'continents',
            'country',
            'climate_zone',
            'month',
            'price_tendency',
            'currency',
            'language',
            'flight_duration',
            'activities',
            'search',

            // ✅ Preise resetten
            'price_flight',
            'price_hotel',
            'price_mietwagen',
            'price_pauschalreise',
            // 
            'range_flight',
        ]);

        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    // =========================
    // Computed Properties
    // =========================
    public function getResultsProperty()
    {
        return $this->repository->getFilteredResults([
            'continents'      => array_filter($this->continents),
            'country'         => $this->country,
            'climate_zone'    => $this->climate_zone,
            'month'           => $this->month,
            'price_tendency'  => $this->price_tendency,
            'currency'        => $this->currency,
            'language'        => $this->language,
            'flight_duration' => $this->flight_duration,
            'activities'      => array_filter($this->activities),
            'search'          => $this->search,

            // 🔥 PREISFILTER (FEHLTEN!)
            'price_flight'        => $this->price_flight,
            'price_hotel'         => $this->price_hotel,
            'price_mietwagen'     => $this->price_mietwagen,
            'price_pauschalreise' => $this->price_pauschalreise,

            'sort_by'         => $this->sortBy,
            'sort_direction'  => $this->sortDirection,

'range_flight'  => $this->range_flight,




        ], $this->perPage);
    }

    public function getTotalCountProperty(): int
    {
        return $this->repository->getFilteredCount([
            'continents'      => array_filter($this->continents),
            'country'         => $this->country,
            'climate_zone'    => $this->climate_zone,
            'month'           => $this->month,
            'price_tendency'  => $this->price_tendency,
            'currency'        => $this->currency,
            'language'        => $this->language,
            'flight_duration' => $this->flight_duration,
            'activities'      => array_filter($this->activities),
            'search'          => $this->search,

            // ✅ Preise
            'price_flight'        => $this->price_flight,
            'price_hotel'         => $this->price_hotel,
            'price_mietwagen'     => $this->price_mietwagen,
            'price_pauschalreise' => $this->price_pauschalreise,

            'range_flight'  => $this->range_flight,

        ]);
    }


    public function hasActiveFilters(): bool
    {
        return collect([
            $this->continents,
            $this->country,
            $this->climate_zone,
            $this->month,
            $this->price_tendency,
            $this->search,
        ])->filter()->isNotEmpty();
    }

    public function getActiveFilters(): array
    {
        return array_filter([
            'search' => $this->search,
            'country' => $this->country,
            'month' => $this->month,
            'price_tendency' => $this->price_tendency,
        ]);
    }

    public function removeFilter(string $filter): void
    {
        if (property_exists($this, $filter)) {
            $this->$filter = is_array($this->$filter) ? [] : '';
            $this->resetPage();
        }
    }

    public function hasActivities($location): bool
    {
        return !empty($location->activities);
    }

    public function getActivityIcons($location): array
    {
        return $location->activities
            ? $location->activities->pluck('icon')->toArray()
            : [];
    }












    // =========================
    // Render
    // =========================
    public function render()
    {
        return view('livewire.frontend.search.detail-search-v2', [
            'filterOptions' => $this->repository->getFilterOptions(),
            'results'       => $this->results,
            'totalCount'    => $this->totalCount,
        ]);
    }
}
