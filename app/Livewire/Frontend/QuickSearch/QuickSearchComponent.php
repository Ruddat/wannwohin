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
    public $nurInBesterReisezeit = false;

    public $totalLocations = 0; // Gesamtanzahl aller Standorte
    public $filteredLocations = 0; // Gefilterte Anzahl der Standorte

    public $allLocations; // Für Pagination oder Anzeige, falls nötig

    public $isCollapsed = false; // Zustand für Ein- und Ausklappen

    protected $listeners = [
        'setSidebarState' => 'updateSidebarState',
    ];

    protected $rules = [
        'urlaub' => 'required|numeric|min:1|max:12',
        'continent' => 'nullable|string',
        'price' => 'nullable|string',
        'urlaub' => 'nullable|string',
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
        // Session-Werte laden, falls vorhanden
        $this->continent = session('quicksearch.continent', null);
        $this->price = session('quicksearch.price', null);
//        $this->urlaub = session('quicksearch.urlaub', null);
        $this->urlaub = session('quicksearch.urlaub', date('n')); // Automatisch aktueller Monat

        $this->sonnenstunden = session('quicksearch.sonnenstunden', null);
        $this->wassertemperatur = session('quicksearch.wassertemperatur', null);
        $this->spezielle = session('quicksearch.spezielle', []);
        $this->nurInBesterReisezeit = session('quicksearch.nurInBesterReisezeit', false);

        // Cookie-Wert für Sidebar-Status laden
        $this->isCollapsed = filter_var(cookie('isCollapsed', false), FILTER_VALIDATE_BOOLEAN);
        $this->isCollapsed = session('isCollapsed', false);

        // Header Content und Bilder laden
        $headerData = $repository->getHeaderContent();
        $this->headerContent = $headerData['headerContent'] ?? null;
        $this->bgImgPath = $headerData['bgImgPath'] ?? null;
        $this->mainImgPath = $headerData['mainImgPath'] ?? null;

        // Alle Locations laden
        $this->allLocations = WwdeLocation::where('status', 'active')
            ->where('finished', 1)
            ->get();

        $this->totalLocations = $this->allLocations->count();

        // Laden der gefilterten Locations aus der Session
        $this->filteredLocations = session('quicksearch.filteredLocations', $this->totalLocations);


        //$this->filteredLocations = $this->totalLocations;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->filterLocations();

        // Alle Filter-Werte in die Session speichern
        session([
            'quicksearch.continent' => $this->continent,
            'quicksearch.price' => $this->price,
            'quicksearch.urlaub' => $this->urlaub,
            'quicksearch.sonnenstunden' => $this->sonnenstunden,
            'quicksearch.wassertemperatur' => $this->wassertemperatur,
            'quicksearch.spezielle' => $this->spezielle,
            'quicksearch.nurInBesterReisezeit' => $this->nurInBesterReisezeit,
            'quicksearch.filteredLocations' => $this->filteredLocations, // Speicherung des gefilterten Werts

        ]);
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

        //if (empty($this->urlaub)) {
        //    return; // Keine Filterung ohne Monat
       // }


        if (!empty($this->urlaub)) {
            $monthNumber = (int) $this->urlaub;

            if ($this->nurInBesterReisezeit) {
                $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($monthNumber)]);
            } else {
              //  $query->whereRaw('JSON_CONTAINS(all_traveltime_json, ?)', [json_encode($monthNumber)]);
            }
        }


        if ($this->nurInBesterReisezeit) {
            $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode((int)$this->urlaub)]);
        }


        // Filter: Urlaub (Monat) mit direkten Zahlenwerten (1–12)
      //  if (!empty($this->urlaub) && is_numeric($this->urlaub)) {
       //     $monthNumber = (int) $this->urlaub;
//dd($monthNumber);
            // Stelle sicher, dass der Monat zwischen 1 und 12 liegt
         ////   if ($monthNumber >= 1 && $monthNumber <= 12) {
            //    $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($monthNumber)]);
          //  }
     //   }

// Filter: Sonnenstunden (aktuelle oder historische Daten aus dem letzten Jahr)
// Filter: Sonnenstunden (aktuelle oder historische Daten aus dem letzten Jahr)
if (!empty($this->sonnenstunden) && !empty($this->urlaub)) {
    $minHours = (int) str_replace('more_', '', $this->sonnenstunden);
    $lastYear = now()->subYear()->year; // Das Jahr, das wir betrachten (1 Jahr rückwärts)

    $query->where(function ($q) use ($minHours, $lastYear) {
        // 🔍 Aktuelle Klimadaten
        $q->whereHas('climates', function ($subQuery) use ($minHours) {
            $subQuery->where('month_id', (int) $this->urlaub)
                     ->whereRaw('COALESCE(sunshine_per_day, 0) >= ?', [$minHours]);
        })
        // 🔍 Historische Daten aus dem letzten Jahr
        ->orWhereHas('historicalClimates', function ($subQuery) use ($minHours, $lastYear) {
            $subQuery->where('month', (int) $this->urlaub)
                     ->where('year', $lastYear)
                     ->whereRaw('COALESCE(sunshine_hours, 0) >= ?', [$minHours]);
        });
    });
}




// Filter: Wassertemperatur (aktuelle Daten oder historische Daten aus dem letzten Jahr)
if (!empty($this->wassertemperatur) && !empty($this->urlaub)) {
    $minTemp = (int) str_replace('more_', '', $this->wassertemperatur);
    $lastYear = now()->subYear()->year; // Vorjahr (1 Jahr rückwärts)

    $query->where(function ($q) use ($minTemp, $lastYear) {
        // 🔍 Aktuelle Klimadaten
        $q->whereHas('climates', function ($subQuery) use ($minTemp) {
            $subQuery->where('month_id', (int) $this->urlaub)
                     ->whereRaw('COALESCE(water_temperature, 0) >= ?', [$minTemp]);
        })
        // 🔍 Historische Daten aus dem letzten Jahr
        ->orWhereHas('historicalClimates', function ($subQuery) use ($minTemp, $lastYear) {
            $subQuery->where('month', (int) $this->urlaub)
                     ->where('year', $lastYear)
                     ->whereRaw('COALESCE(temperature_avg, 0) >= ?', [$minTemp]);
        });
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


    // ✅ Livewire zwingen, das Template neu zu rendern
    $this->dispatch('filteredLocationsUpdated', $this->filteredLocations);

    // ✅ Speicherung in die Session für persistente Werte
    session(['quicksearch.filteredLocations' => $this->filteredLocations]);

        // Speicherung in die Session
        session(['quicksearch.filteredLocations' => $this->filteredLocations]);

    }


    // Livewire-Listener-Methode mit Parameter
    #[On('goOn-Sidebarstate')]
    public function updateSidebarState($state)
    {
        $this->isCollapsed = $state;

        // Zustand im Cookie aktualisieren
        cookie()->queue('isCollapsed', $state, 60 * 24 * 30); // 30 Tage

        // Zustand in der Session speichern
        session(['isCollapsed' => $this->isCollapsed]);
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;

        // Zustand im Cookie speichern
        cookie()->queue('isCollapsed', $this->isCollapsed, 60 * 24 * 30); // 30 Tage

        // Zustand in der Session speichern
        session(['isCollapsed' => $this->isCollapsed]);

    }

    public function redirectToResults()
    {
        $this->validate([
            'urlaub' => 'required|numeric|min:1|max:12',
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

        // ✅ Gefilterte Locations direkt hier bestimmen
        $query = WwdeLocation::query()
            ->select('id') // Nur die IDs der Locations abfragen
            ->active()
            ->finished()
            ->filterByContinent($this->continent)
            ->filterByPrice($this->price)
            ->filterBySpecials($this->spezielle);

        // 🌍 **Filter für Reisezeit**
        if (!empty($this->urlaub) && is_numeric($this->urlaub)) {
            $monthNumber = (int) $this->urlaub;

            if ($this->nurInBesterReisezeit) {
                $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($monthNumber)]);
            }
        }

        // 🌞 **Filter: Sonnenstunden (aktuelle oder historische Daten)**
        if (!empty($this->sonnenstunden) && !empty($this->urlaub)) {
            $minHours = (int) str_replace('more_', '', $this->sonnenstunden);
            $lastYear = now()->subYear()->year;

            $query->where(function ($q) use ($minHours, $lastYear) {
                // 🔍 Aktuelle Klimadaten
                $q->whereHas('climates', function ($subQuery) use ($minHours) {
                    $subQuery->where('month_id', (int) $this->urlaub)
                        ->whereRaw('COALESCE(sunshine_per_day, 0) >= ?', [$minHours]);
                })
                // 🔍 Historische Daten aus dem letzten Jahr
                ->orWhereHas('historicalClimates', function ($subQuery) use ($minHours, $lastYear) {
                    $subQuery->where('month', (int) $this->urlaub)
                        ->where('year', $lastYear)
                        ->whereRaw('COALESCE(sunshine_hours, 0) >= ?', [$minHours]);
                });
            });
        }

        // 🌊 **Filter: Wassertemperatur (aktuelle Daten oder historische Daten)**
        if (!empty($this->wassertemperatur) && !empty($this->urlaub)) {
            $minTemp = (int) str_replace('more_', '', $this->wassertemperatur);
            $lastYear = now()->subYear()->year;

            $query->where(function ($q) use ($minTemp, $lastYear) {
                // 🔍 Aktuelle Klimadaten
                $q->whereHas('climates', function ($subQuery) use ($minTemp) {
                    $subQuery->where('month_id', (int) $this->urlaub)
                        ->whereRaw('COALESCE(water_temperature, 0) >= ?', [$minTemp]);
                })
                // 🔍 Historische Daten aus dem letzten Jahr
                ->orWhereHas('historicalClimates', function ($subQuery) use ($minTemp, $lastYear) {
                    $subQuery->where('month', (int) $this->urlaub)
                        ->where('year', $lastYear)
                        ->whereRaw('COALESCE(temperature_avg, 0) >= ?', [$minTemp]);
                });
            });
        }

        // ✅ Ergebnisse filtern und nur IDs speichern
        $filteredIds = $query->pluck('id')->toArray();
        session(['quicksearch.filteredLocationIds' => $filteredIds]);

        // ✅ Debug: Anzahl der gefilterten IDs loggen
        \Log::info('Gefilterte Location-IDs aus der Query:', [
            'count' => count($filteredIds),
            'ids' => $filteredIds,
        ]);

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

        // ✅ Livewire-Frontend über das Event informieren
        $this->dispatch('sidebarCollapsed', true);

        // Zustand im Cookie speichern
        cookie()->queue('isCollapsed', true, 60 * 24 * 30); // 30 Tage

        // Zustand in der Session speichern
        session(['isCollapsed' => $this->isCollapsed]);

        // ✅ Warten auf Frontend-Update vor Redirect
        return redirect()->route('detail_search');
    }

    public function resetFilters()
    {
        // Werte zurücksetzen
        $this->continent = null;
        $this->price = null;
        //$this->urlaub = null;
        $this->urlaub = date('n'); // Automatisch aktueller Monat
        $this->sonnenstunden = null;
        $this->wassertemperatur = null;
        $this->spezielle = [];

        // Session-Werte löschen
        session()->forget([
            'quicksearch.continent',
            'quicksearch.price',
            'quicksearch.urlaub',
            'quicksearch.sonnenstunden',
            'quicksearch.wassertemperatur',
            'quicksearch.spezielle',
            'quicksearch.nurInBesterReisezeit',
            'quicksearch.filteredLocations',

        ]);

        // Nach dem Zurücksetzen erneut filtern
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
