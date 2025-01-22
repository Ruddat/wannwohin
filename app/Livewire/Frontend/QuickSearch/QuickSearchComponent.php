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
        'spezielle' => 'nullable|array',
    ];


    public function mount(LocationRepository $repository)
    {
        // Header Content und Bilder laden
        $headerData = $repository->getHeaderContent();

        $this->headerContent = $headerData['headerContent'] ?? null;
        $this->bgImgPath = $headerData['bgImgPath'] ?? null;
        $this->mainImgPath = $headerData['mainImgPath'] ?? null;

        // Alle Locations laden (wie zuvor)
        $this->allLocations = WwdeLocation::where('status', 'active')
            ->where('finished', 1)
            ->get();

//dd($this->allLocations);


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

        // Scopes für wiederverwendbare Abfragen
        $query->active()->finished();

        // Wenn kein Filter gesetzt ist, alle Locations abrufen
        if (
            empty($this->continent) &&
            empty($this->price) &&
            empty($this->urlaub) &&
            empty($this->sonnenstunden) &&
            empty($this->wassertemperatur) &&
            empty($this->spezielle)
        ) {
            $this->filteredLocations = $this->totalLocations;
            return; // Keine weiteren Bedingungen anwenden
        }

        // Filter: Kontinent
        if (!empty($this->continent)) {
            $query->where('continent_id', $this->continent);
        }

        // Filter: Preis
        if (!empty($this->price)) {
            $this->applyPriceFilter($query);
        }

        // Filter: Urlaub (Monat)
        if (!empty($this->urlaub)) {
            $monthMapping = [
                'Januar' => 'January',
                'Februar' => 'February',
                'März' => 'March',
                'April' => 'April',
                'Mai' => 'May',
                'Juni' => 'June',
                'Juli' => 'July',
                'August' => 'August',
                'September' => 'September',
                'Oktober' => 'October',
                'November' => 'November',
                'Dezember' => 'December',
            ];

            $englishMonth = $monthMapping[$this->urlaub] ?? $this->urlaub;

            $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($englishMonth)]);
        }

        // Filter: Sonnenstunden
        if (!empty($this->sonnenstunden)) {
            $query->whereHas('climates', function ($q) {
                $minHours = (int) str_replace('more_', '', $this->sonnenstunden);
                $q->where('sunshine_per_day', '>=', $minHours);
            });
        }

        // Filter: Wassertemperatur
        if (!empty($this->wassertemperatur)) {
            $query->whereHas('climates', function ($q) {
                $minTemp = (int) str_replace('more_', '', $this->wassertemperatur);
                $q->where('water_temperature', '>=', $minTemp);
            });
        }

        // Filter: Spezielle Wünsche
        if (!empty($this->spezielle)) {
            foreach ($this->spezielle as $wish) {
                $query->where($wish, 1);
            }
        }

        // Gefilterte Locations zählen
        $this->filteredLocations = $query->count();
    }



    // Livewire-Listener-Methode mit Parameter
    #[On('goOn-Sidebarstate')]
    public function updateSidebarState($state)
    {
        $this->isCollapsed = $state;

        // Zustand im Cookie aktualisieren
        cookie()->queue('isCollapsed', $state, 60 * 24 * 30); // 30 Tage
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;

        // Zustand in einem Cookie speichern
        cookie()->queue('isCollapsed', $this->isCollapsed, 60 * 24 * 30); // 30 Tage
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

//dd($queryParams);


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

        // Optional: Zustand in einem Cookie speichern
        cookie()->queue('isCollapsed', true, 60 * 24 * 30); // 30 Tage

        // Nach dem Einklappen zur "Detailsuche"-Route umleiten
        return redirect()->route('detail_search');
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
