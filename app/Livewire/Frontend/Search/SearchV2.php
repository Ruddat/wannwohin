<?php

namespace App\Livewire\Frontend\Search;

use App\Models\WwdeTag;
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

    // Tag-Gruppen (Sport / Parks / Erlebnisse / Natur)
    public array $tags = [];

    public array $availableTagGroups = [];
    public array $tagLookup = [];
    public ?int $sunshine_min = null;
    public ?int $water_temp_min = null;

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

        'tags' => ['except' => []],

    ];


    public function mount(): void
    {
    if (request()->boolean('auto')) {
        cookie()->queue('isCollapsed', true, 60 * 24 * 30);
    }

        if (request()->hasAny([
            'continent',
            'country',
            'month',
            'price',
            'sunshine_min',
            'water_temp_min',
            'activities',
            'daily_temp_min',
            'daily_temp_max',
            'flight_duration',
            'distance',
            'language',
            'currency',
            'visum',
            'price_tendency',
            'tags'
        ])) {
            $this->searchTriggered = true;
        }


        $this->loadApplyTagFilters(null, new SearchFilters()); // nur um die verfügbaren Tags zu laden und zu stabilisieren


    }


    public function getSunPercentProperty(): int
    {
        if ($this->sunshine_min === null) return 0;
        return intval(($this->sunshine_min / 12) * 100);
    }

    public function getWaterPercentProperty(): int
    {
        if ($this->water_temp_min === null) return 0;
        return intval(($this->water_temp_min / 35) * 100);
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
                        fn($x) => (string) $x !== (string) $value
                    ));
                } else {
                    $this->activities = [];
                }
                break;

            case 'tags':

                if (is_string($value)) {
                    $value = json_decode($value, true);
                }

                if ($value && isset($this->tags[$value['group']])) {

                    $group = $value['group'];
                    $slug  = $value['slug'];

                    $this->tags[$group] = array_values(array_filter(
                        (array) $this->tags[$group],
                        fn($x) => $x !== $slug
                    ));

                    if (empty($this->tags[$group])) {
                        unset($this->tags[$group]);
                    }
                }

                break;

            default:
                // Unbekannter Filter-Key
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

        $this->tags = [];


        $this->loadApplyTagFilters(null, new SearchFilters()); // nur um die verfügbaren Tags zu laden und zu stabilisieren


    }



    private function loadApplyTagFilters($query, SearchFilters $filters)
    {

        $this->availableTagGroups = WwdeTag::orderBy('group')
            ->orderBy('title')
            ->get()
            ->groupBy(fn($tag) => \Illuminate\Support\Str::slug($tag->group))
            ->map(function ($tags) {
                return [
                    'label' => $tags->first()->group,
                    'items' => $tags->toArray(),
                ];
            })
            ->toArray();

        // 🔥 Stabilisierung der Tag-Arrays
        foreach (array_keys($this->availableTagGroups) as $groupKey) {
            if (!array_key_exists($groupKey, $this->tags)) {
                $this->tags[$groupKey] = [];
            } elseif (!is_array($this->tags[$groupKey])) {
                $this->tags[$groupKey] = (array) $this->tags[$groupKey];
            }
        }

        // 🔥 Lookup mit gleichen Slug-Keys
        $this->tagLookup = WwdeTag::all()
            ->mapWithKeys(function ($tag) {
                $groupKey = \Illuminate\Support\Str::slug($tag->group);
                return [
                    $groupKey . '.' . $tag->slug => $tag->toArray()
                ];
            })
            ->toArray();

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
        $filters->tags = is_array($this->tags) ? $this->tags : [];

        $filters->dailyTempMin   = $this->daily_temp_min ? (int) $this->daily_temp_min : null;
        $filters->dailyTempMax   = $this->daily_temp_max ? (int) $this->daily_temp_max : null;
        $filters->flightDuration = $this->flight_duration ? (int) $this->flight_duration : null;
        $filters->distance       = $this->distance ? (int) $this->distance : null;

        $filters->language      = $this->language ?: null;
        $filters->currency      = $this->currency ?: null;
        $filters->visum         = ($this->visum === '' || $this->visum === null) ? null : (int) $this->visum;
        $filters->priceTendency = $this->price_tendency ?: null;


        $filters->bestTimeOnly = $this->nurInBesterReisezeit;
        $filters->tags = is_array($this->tags) ? $this->tags : [];
        $filters->tagMode = 'and'; // später umstellbar


        $query = $engine->query($filters);

        $results = $query
            ->paginate($this->perPage)
            ->withQueryString();

        // Match Score berechnen
        $results->getCollection()->transform(function ($loc) use ($filters, $scorer) {
            $loc->match_score = $scorer->score($loc, $filters);

$month = $filters->month
    ? (int)$filters->month
    : now()->month;   // 🔥 Fallback auf aktuellen Monat

$climate = $loc->climates->firstWhere('month_id', $month);

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

        //dd($this->tags);

        $parks = collect();

        if ($this->shouldIncludeParks($filters)) {
            $parks = app(\App\Services\Search\ParkSearchService::class)
                ->search($filters);
        }



        return view('livewire.frontend.search.search-v2', [
            'results' => $results,
            'total' => $results->total(),
            'parks'   => $parks
        ])->layout('layouts.main');

    }

private function shouldIncludeParks(SearchFilters $filters): bool
{
    // Wenn explizit Park-Tag gewählt
    if (!empty($filters->tags['parks'])) {
        return true;
    }

    // Wenn konkretes Land gesetzt
    if ($filters->country) {
        return true;
    }

    return false;
}

    public function getCountriesProperty()
    {
        if (!$this->continent) {
            return collect();
        }

        return \App\Models\WwdeCountry::where('continent_id', (int)$this->continent)
            ->where('status', 'active')
            ->orderBy('title')
            ->get();
    }

public function updatedContinent()
{
    $this->country = null;
}


public function toggleReiseart(string $field): void
{
    $current = collect($this->activities);

    if ($current->contains($field)) {
        $this->activities = $current->reject(fn($v) => $v === $field)->values()->toArray();
    } else {
        $this->activities = $current->push($field)->unique()->values()->toArray();
    }

    $this->resetPage();
    $this->searchTriggered = true;
}


}
