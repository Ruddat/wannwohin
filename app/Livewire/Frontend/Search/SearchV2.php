<?php

namespace App\Livewire\Frontend\Search;

use App\Services\Search\SearchEngineV2;
use App\Services\Search\SearchFilters;
use App\Services\Search\SearchScoreV2;
use Livewire\Component;
use Livewire\WithPagination;

class SearchV2 extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Aktive Filter (werden erst bei Apply übernommen)
    public $continent, $country, $month, $price;
    public $sunshine_min, $water_temp_min;
    public $activities = [];

    public $sortBy = 'match';
    public $sortDir = 'desc';
    public $perPage = 12;

    // Interner Trigger
    public $searchTriggered = false;


public $mode = 'inspiration'; // inspiration | detail
public $nurInBesterReisezeit = false;

// optional: advanced filters (nur Detailmodus)
public $daily_temp_min, $daily_temp_max;
public $flight_duration, $distance;
public $language, $currency, $visum, $price_tendency;


    protected $queryString = [
        'continent' => ['except' => null],
        'country' => ['except' => null],
        'month' => ['except' => null],
        'price' => ['except' => null],
        'sunshine_min' => ['except' => null],
        'water_temp_min' => ['except' => null],
        'activities' => ['except' => []],
        'sortBy' => ['except' => 'match'],
        'sortDir' => ['except' => 'desc'],
        'perPage' => ['except' => 12],

    'mode' => ['except' => 'inspiration'],
    'nurInBesterReisezeit' => ['except' => false],

    'daily_temp_min' => ['except' => null],
    'daily_temp_max' => ['except' => null],
    'flight_duration' => ['except' => null],
    'distance' => ['except' => null],
    'language' => ['except' => null],
    'currency' => ['except' => null],
    'visum' => ['except' => null],
    'price_tendency' => ['except' => null],


    ];


public function mount(): void
{
    if (request()->boolean('auto')) {
        $this->searchTriggered = true;
    }

    // Wenn jemand manuell URL mit Filtern teilt, auch triggern:
    if (request()->hasAny(['continent','country','month','price','sunshine_min','water_temp_min','activities'])) {
        $this->searchTriggered = true;
    }
}



    public function applyFilters()
    {
        $this->resetPage();
        $this->searchTriggered = true;
    }

public function removeFilter(string $key, $value = null): void
{
    switch ($key) {
        case 'continent':
            $this->continent = null;
            break;

        case 'country':
            $this->country = null;
            break;

        case 'month':
            $this->month = null;
            break;

        case 'price':
            $this->price = null;
            break;

        case 'nurInBesterReisezeit':
            $this->nurInBesterReisezeit = false;
            break;

        case 'sunshine_min':
            $this->sunshine_min = null;
            break;

        case 'water_temp_min':
            $this->water_temp_min = null;
            break;

        case 'daily_temp_min':
            $this->daily_temp_min = null;
            break;

        case 'daily_temp_max':
            $this->daily_temp_max = null;
            break;

        case 'flight_duration':
            $this->flight_duration = null;
            break;

        case 'distance':
            $this->distance = null;
            break;

        case 'language':
            $this->language = null;
            break;

        case 'currency':
            $this->currency = null;
            break;

        case 'visum':
            $this->visum = null;
            break;

        case 'price_tendency':
            $this->price_tendency = null;
            break;

        case 'activities':
            if ($value !== null) {
                $this->activities = array_values(array_filter(
                    (array) $this->activities,
                    fn ($x) => (string) $x !== (string) $value
                ));
            } else {
                $this->activities = [];
            }
            break;
    }

    $this->resetPage();
    $this->searchTriggered = true; // damit nach Chip-Entfernung sofort neu gerendert wird
}

public function resetAllFilters(): void
{
    $this->continent = null;
    $this->country = null;
    $this->month = null;
    $this->price = null;

    $this->sunshine_min = null;
    $this->water_temp_min = null;

    $this->nurInBesterReisezeit = false;

    $this->daily_temp_min = null;
    $this->daily_temp_max = null;
    $this->flight_duration = null;
    $this->distance = null;
    $this->language = null;
    $this->currency = null;
    $this->visum = null;
    $this->price_tendency = null;

    $this->activities = [];

    $this->resetPage();
    $this->searchTriggered = true;
}




    public function render(SearchEngineV2 $engine, SearchScoreV2 $scorer)
    {
        if (!$this->searchTriggered && !request()->hasAny([
            'continent',
            'country',
            'month',
            'price',
            'sunshine_min',
            'water_temp_min',
            'activities'
        ])) {
            return view('livewire.frontend.search.search-v2', [
                'results' => collect(),
                'total' => 0
            ]);
        }

        $filters = new SearchFilters();

        $filters->continent     = $this->continent ? (int) $this->continent : null;
        $filters->country       = $this->country ? (int) $this->country : null;
        $filters->month         = $this->month ? (int) $this->month : null;
        $filters->priceRange    = $this->price ? (int) $this->price : null;

        $filters->sunshineMin   = $this->sunshine_min ? (int) $this->sunshine_min : null;
        $filters->waterTempMin  = $this->water_temp_min ? (int) $this->water_temp_min : null;

        $filters->activities    = is_array($this->activities) ? $this->activities : [];

$filters->dailyTempMin   = $this->daily_temp_min ? (int) $this->daily_temp_min : null;
$filters->dailyTempMax   = $this->daily_temp_max ? (int) $this->daily_temp_max : null;
$filters->flightDuration = $this->flight_duration ? (int) $this->flight_duration : null;
$filters->distance       = $this->distance ? (int) $this->distance : null;

$filters->language      = $this->language ?: null;
$filters->currency      = $this->currency ?: null;
$filters->visum         = ($this->visum === '' || $this->visum === null) ? null : (int) $this->visum;
$filters->priceTendency = $this->price_tendency ?: null;


        $query = $engine->query($filters);

        $results = $query
            ->paginate($this->perPage)
            ->withQueryString();

        // Match Score berechnen
$results->getCollection()->transform(function ($loc) use ($filters, $scorer) {
    $loc->match_score = $scorer->score($loc, $filters);

    $month = $filters->month ? (int)$filters->month : null;

    $climate = $month
        ? $loc->climates->firstWhere('month_id', $month)
        : null;

    $loc->climate_data = [
        'sunshine_hours' => $climate->sunshine_per_day ?? null,
        'water_temperature' => $climate->water_temperature ?? null,
        'rainy_days' => $climate->rainy_days ?? null,
    ];

    return $loc;
});

        // Sortierung nach Match
        if ($this->sortBy === 'match') {
            $sorted = $results->getCollection()
                ->sortByDesc('match_score')
                ->values();
            $results->setCollection($sorted);
        }

        return view('livewire.frontend.search.search-v2', [
            'results' => $results,
            'total' => $results->total()
        ])->layout('layouts.main');
    }
}
