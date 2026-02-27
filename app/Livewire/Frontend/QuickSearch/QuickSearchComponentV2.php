<?php

namespace App\Livewire\Frontend\QuickSearch;

use App\Models\WwdeContinent;
use App\Models\WwdeRange;
use App\Repositories\LocationRepository;
use App\Services\Search\SearchEngineV2;
use App\Services\Search\SearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickSearchComponentV2 extends Component
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

    public $isCollapsed = false;

    public $headerContent;
    public $bgImgPath;
    public $mainImgPath;

    protected $listeners = [
        'setSidebarState' => 'updateSidebarState',
    ];

    protected $rules = [
        'urlaub' => 'required|numeric|min:1|max:12',
        'continent' => 'nullable|integer',
        'price' => 'nullable|integer',
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
    ];

public function mount(LocationRepository $repository)
{
    $this->urlaub = date('n');

    // 🔥 Collapse über URL steuern
    if (request()->boolean('collapse')) {
        $this->isCollapsed = true;
    } else {
        $this->isCollapsed = false;
    }

    $headerData = $repository->getHeaderContent();
    $this->headerContent = $headerData['headerContent'] ?? null;
    $this->bgImgPath = $headerData['bgImgPath'] ?? null;
    $this->mainImgPath = $headerData['mainImgPath'] ?? null;

    $this->updatePreviewCount();
}

    public function updated()
    {
        $this->validateOnly(func_get_arg(0));
        $this->updatePreviewCount();
    }

    private function updatePreviewCount()
    {
        $filters = new SearchFilters();

        $filters->continent     = $this->continent ? (int) $this->continent : null;
        $filters->month         = $this->urlaub ? (int) $this->urlaub : null;
        $filters->priceRange    = $this->price ? (int) $this->price : null;

        $filters->sunshineMin   = $this->sonnenstunden
            ? (int) str_replace('more_', '', $this->sonnenstunden)
            : null;

        $filters->waterTempMin  = $this->wassertemperatur
            ? (int) str_replace('more_', '', $this->wassertemperatur)
            : null;

        $filters->activities    = $this->spezielle ?: [];
        $filters->bestTimeOnly  = $this->nurInBesterReisezeit;

        $engine = app(SearchEngineV2::class);

        $query = $engine->query($filters);

        $this->filteredLocations = $query->count();
        $this->totalLocations    = $engine->query(new SearchFilters())->count();

        $this->dispatch('filteredLocationsUpdated', $this->filteredLocations);
    }

    #[On('goOn-Sidebarstate')]
    public function updateSidebarState($state)
    {
        $this->isCollapsed = $state;
        cookie()->queue('isCollapsed', $state, 60 * 24 * 30);
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;
        cookie()->queue('isCollapsed', $this->isCollapsed, 60 * 24 * 30);
    }

public function redirectToResults()
{
    $this->validate();

    $params = [
        'auto' => 1,
        'collapse' => 1,   // 🔥 wichtig
        'continent' => $this->continent,
        'price' => $this->price,
        'month' => $this->urlaub,
        'sunshine_min' => $this->sonnenstunden
            ? (int) str_replace('more_', '', $this->sonnenstunden)
            : null,
        'water_temp_min' => $this->wassertemperatur
            ? (int) str_replace('more_', '', $this->wassertemperatur)
            : null,
        'activities' => !empty($this->spezielle)
            ? array_values($this->spezielle)
            : null,
        'nurInBesterReisezeit' => $this->nurInBesterReisezeit ? 1 : null,
    ];

    return redirect()->route('search.v2', array_filter(
        $params,
        fn ($v) => $v !== null && $v !== '' && $v !== []
    ));
}

    public function resetFilters()
    {
        $this->continent = null;
        $this->price = null;
        $this->urlaub = date('n');
        $this->sonnenstunden = null;
        $this->wassertemperatur = null;
        $this->spezielle = [];
        $this->nurInBesterReisezeit = false;

        $this->updatePreviewCount();
    }

    public function render()
    {
        return view('livewire.frontend.quick-search.quick-search-component-v2', [
            'continents' => WwdeContinent::select('id', 'title')->get(),
            'ranges' => WwdeRange::where('type', 'flight')->orderBy('sort')->get(),
            'months' => config('custom.months'),
        ]);
    }
}
