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
    public $spezielle = []; // Array für mehrere spezielle Wünsche

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
            // Preisbereich abrufen
            $priceRange = WwdeRange::find($this->price);

            if ($priceRange) {
                $rangeToShow = $priceRange->Range_to_show;

                // Prüfen, ob der Wert ein Bereich ist (z.B. "500-1000€")
                if (str_contains($rangeToShow, '-')) {
                    [$minPrice, $maxPrice] = array_map('intval', explode('-', str_replace(['€', ' '], '', $rangeToShow)));
                    $query->whereBetween('price_flight', [$minPrice, $maxPrice]);
                } elseif (str_contains($rangeToShow, '>')) {
                    // Nur Mindestpreis (z.B. ">2000€")
                    $minPrice = (int) filter_var($rangeToShow, FILTER_SANITIZE_NUMBER_INT);
                    $query->where('price_flight', '>=', $minPrice);
                } else {
                    // Einzelner Preiswert (z.B. "250€")
                    $maxPrice = (int) filter_var($rangeToShow, FILTER_SANITIZE_NUMBER_INT);
                    $query->where('price_flight', '<=', $maxPrice);
                }
            }
        }

        if (!empty($this->urlaub)) {
            $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', ['"' . $this->urlaub . '"']);
        }

        if (!empty($this->sonnenstunden)) {
            $query->whereHas('climates', function ($q) {
                $minHours = (int) str_replace('more_', '', $this->sonnenstunden);
                $q->where('sunshine_per_day', '>=', $minHours);
            });
        }

        if (!empty($this->wassertemperatur)) {
            $query->whereHas('climates', function ($q) {
                $minTemp = (int) str_replace('more_', '', $this->wassertemperatur);
                $q->where('water_temperature', '>=', $minTemp);
            });
        }

        if (!empty($this->spezielle)) {
            foreach ($this->spezielle as $wish) {
                $query->where($wish, 1);
            }
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
