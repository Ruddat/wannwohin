<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeLocation;
use App\Models\WwdeRange;
use App\Models\WwdeContinent;

class QuickSearchComponent extends Component
{
    public $continent;
    public $price;
    public $urlaub;
    public $sonnenstunden;
    public $wassertemperatur;
    public $spezielle = '';

    public $totalLocations = 0; // Gesamtanzahl aller Standorte
    public $filteredLocations = 0; // Gefilterte Anzahl der Standorte

    public $allLocations; // Für Pagination oder Anzeige, falls nötig

    public $isCollapsed = false; // Zustand für Ein- und Ausklappen

    protected $listeners = [
        'setSidebarState' => 'updateSidebarState',
    ];

    protected $rules = [
        'continent' => 'nullable|string',
        'price' => 'nullable|string',
        'urlaub' => 'nullable|string',
        'sonnenstunden' => 'nullable|string',
        'wassertemperatur' => 'nullable|string',
        'spezielle' => 'nullable|string',
    ];

    public function mount()
    {
        $this->allLocations = WwdeLocation::all();
        $this->totalLocations = $this->allLocations->count();
        $this->filteredLocations = $this->totalLocations;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->filterLocations();
    }

    public function filterLocations()
    {
        $query = WwdeLocation::query();

        if (!empty($this->continent)) {
            $query->where('continent_id', $this->continent);
        }

        if (!empty($this->price)) {
            $query->where('range_flight', $this->price);
        }

        if (!empty($this->urlaub)) {
            $query->where('best_traveltime', 'like', "%{$this->urlaub}%");
        }

        if (!empty($this->sonnenstunden)) {
            $hours = (int) str_replace('more_', '', $this->sonnenstunden);
            $query->where('climate_details_id', '>=', $hours);
        }

        if (!empty($this->wassertemperatur)) {
            $temp = (int) str_replace('more_', '', $this->wassertemperatur);
            $query->where('climate_details_lnam', '>=', $temp);
        }

        if (!empty($this->spezielle)) {
            $query->where($this->spezielle, 1);
        }

        $this->filteredLocations = $query->count();
    }

    // Livewire-Listener-Methode mit Parameter
    public function updateSidebarState($state)
    {
        $this->isCollapsed = $state;
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function redirectToResults()
    {
        $queryParams = [
            'continent' => $this->continent,
            'price' => $this->price,
            'urlaub' => $this->urlaub,
            'sonnenstunden' => $this->sonnenstunden,
            'wassertemperatur' => $this->wassertemperatur,
            'spezielle' => $this->spezielle,
        ];

        return redirect()->route('search.results', array_filter($queryParams));
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
