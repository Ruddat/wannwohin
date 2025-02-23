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

    public $continent;
    public $price;
    public $urlaub;
    public $sonnenstunden;
    public $wassertemperatur;
    public $spezielle;
    public $nurInBesterReisezeit = false;

    public $sortBy = 'title';
    public $sortDirection = 'asc';

    public $headerContent;
    public $bgImgPath;
    public $mainImgPath;

    public $perPage = 10; // Standardmäßig 10 Ergebnisse pro Seite

    public $page;
    public $activeFilters = [];
    public $totalResults = 0;

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
        // Header Content und Bilder laden
        $headerData = $repository->getHeaderContent();
        session()->put('headerData', [
            'headerContent' => $headerData['headerContent'] ?? null,
            'bgImgPath' => $headerData['bgImgPath'] ?? null,
            'mainImgPath' => $headerData['mainImgPath'] ?? null,
        ]);

         // HeaderContent abrufen
         $headerContent = HeaderContent::inRandomOrder()->first();

         // Bildpfade validieren
         $bgImgPath = $headerContent->bg_img ?
             (Storage::exists($headerContent->bg_img)
                 ? Storage::url($headerContent->bg_img)
                 : (file_exists(public_path($headerContent->bg_img))
                     ? asset($headerContent->bg_img)
                     : null))
             : null;

         $mainImgPath = $headerContent->main_img ?
             (Storage::exists($headerContent->main_img)
                 ? Storage::url($headerContent->main_img)
                 : (file_exists(public_path($headerContent->main_img))
                     ? asset($headerContent->main_img)
                     : null))
             : null;

             session([
                'headerData' => [
                    'bgImgPath' => $bgImgPath,
                    'mainImgPath' => $mainImgPath,
                    'title' => $headerContent->title,
                    'title_text' => $headerContent->main_text,
                    'main_text' => $headerContent->content,
                ]
            ]);



        // Suchparameter aus der URL laden
        $this->continent = request('continent');
        $this->price = request('price');
        $this->urlaub = request('urlaub');
        $this->sonnenstunden = request('sonnenstunden');
        $this->wassertemperatur = request('wassertemperatur');
        $this->spezielle = request('spezielle');
        $this->nurInBesterReisezeit = request('nurInBesterReisezeit', false);

       // ✅ Aktive Filter direkt setzen
       $this->activeFilters['continent'] = $this->continent;
       $this->activeFilters['price'] = $this->price;
       $this->activeFilters['urlaub'] = $this->urlaub;
       $this->activeFilters['sonnenstunden'] = $this->sonnenstunden;
       $this->activeFilters['wassertemperatur'] = $this->wassertemperatur;
       $this->activeFilters['spezielle'] = $this->spezielle;


    }

    public function updatedSortBy()
    {
        $this->resetPage(); // Pagination zurücksetzen, wenn die Sortierung geändert wird
    }

    public function sortBy($field)
    {
        // Wenn das aktuelle Sortierfeld erneut angeklickt wird, die Richtung umkehren
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Neues Feld setzen und Standardrichtung aufsteigend
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        // Pagination zurücksetzen
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage(); // Pagination zurücksetzen, wenn sich die Anzahl der Ergebnisse ändert
    }

    public function toggleSortDirection()
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }


    public function updated($property)
    {
        if (in_array($property, ['continent', 'price', 'urlaub', 'sonnenstunden', 'wassertemperatur', 'spezielle'])) {
            $this->activeFilters[$property] = $this->$property;
            $this->resetPage(); // Pagination zurücksetzen
        }
    }


    public function removeFilter($filterKey)
    {
        \Log::info('Removing filter:', ['filterKey' => $filterKey, 'activeFilters' => $this->activeFilters]);
        unset($this->activeFilters[$filterKey]);
        $this->$filterKey = null;
        $this->resetPage();

        // Aktualisiere die gefilterten Location-IDs in der Session
        $this->updateFilteredLocationIds();

        $this->render();
    }

    private function updateFilteredLocationIds()
    {
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
        \Log::info('Aktualisierte gefilterte Location-IDs:', [
            'count' => count($filteredIds),
            'ids' => $filteredIds,
        ]);
    }


public function getFilterLabel($key, $value)
{
    switch ($key) {
        case 'price':
            $priceRange = WwdeRange::find($value);
            return $priceRange ? $priceRange->Range_to_show : $value;
        case 'urlaub':
            return config('custom.months')[$value] ?? $value;
        case 'sonnenstunden':
            $hours = str_replace('more_', '', $value);
            return "Mehr als {$hours} Sonnenstunden"; // Angepasste Formatierung
        case 'wassertemperatur':
            $temp = str_replace('more_', '', $value);
            return "Mehr als {$temp}°C Wassertemperatur"; // Angepasste Formatierung
        case 'spezielle':
            return $this->specialWishes[$value] ?? $value;
        default:
            return $value;
    }
}

    public function render()
    {
        // ✅ Gefilterte Location-IDs aus der Session laden
        $filteredLocationIds = session('quicksearch.filteredLocationIds', []);
        \Log::info('Gefilterte Location-IDs aus der Session:', ['ids' => $filteredLocationIds]);

        if (empty($filteredLocationIds)) {
            $locations = collect(); // Leere Collection
        } else {
            // Unterabfrage für die Aggregation der Klimadaten
            $subQuery = WwdeLocation::query()
                ->select('wwde_locations.id')
                ->leftJoin('climate_monthly_data as cmd', function ($join) {
                    $join->on('wwde_locations.id', '=', 'cmd.location_id')
                        ->where('cmd.year', '>=', now()->subYear()->year);
                })
                ->groupBy('wwde_locations.id')
                ->orderByRaw("COALESCE(
                    MAX(cmd.temperature_avg),
                    MAX(cmd.temperature_max),
                    MAX(cmd.temperature_min)
                ) {$this->sortDirection}");

            // Hauptabfrage
            $query = WwdeLocation::query()
                ->select('wwde_locations.*')
                ->with(['climates', 'historicalClimates' => function ($q) {
                    $lastYear = now()->subYear()->year;
                    $q->where('year', '>=', $lastYear); // Daten aus dem letzten Jahr
                }])
                ->active()
                ->whereIn('wwde_locations.id', session('quicksearch.filteredLocationIds', []));

                // Dynamische Filter anwenden
                foreach ($this->activeFilters as $key => $value) {
                    if ($value) {
                        switch ($key) {
                            case 'continent':
                                $query->filterByContinent($value);
                                break;
                            case 'price':
                                $query->filterByPrice($value);
                                break;
                            case 'urlaub':
                                $query->filterBySunshine($value);
                                break;
                            case 'sonnenstunden':
                                $query->filterBySunshine($value);
                                break;
                            case 'wassertemperatur':
                                $query->filterByWaterTemperature($value);
                                break;
                            case 'spezielle':
                                $query->filterBySpecials($value);
                                break;
                        }
                    }
                }

            // Filter für Reisezeit
            if (!empty($this->urlaub) && is_numeric($this->urlaub)) {
                $monthNumber = (int) $this->urlaub;

                if ($this->nurInBesterReisezeit) {
                    $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($monthNumber)]);
                }
            }

            // Sichere Sortierung
            $allowedSortFields = ['price_flight', 'title', 'continent_id', 'country_id', 'flight_hours'];
            if (in_array($this->sortBy, $allowedSortFields)) {
                $query->orderBy($this->sortBy, $this->sortDirection);
            } elseif ($this->sortBy === 'climate_data->main->temp') {
                // Join mit der Unterabfrage
                $query->joinSub($subQuery, 'sub', function ($join) {
                    $join->on('wwde_locations.id', '=', 'sub.id');
                });

                // 🔥 Sortierrichtung explizit setzen
                if ($this->sortDirection === 'asc') {
                    $query->orderByRaw("COALESCE(
                        (SELECT MAX(cmd.temperature_avg) FROM climate_monthly_data cmd WHERE cmd.location_id = wwde_locations.id AND cmd.year >= ?),
                        (SELECT MAX(cmd.temperature_max) FROM climate_monthly_data cmd WHERE cmd.location_id = wwde_locations.id AND cmd.year >= ?),
                        (SELECT MAX(cmd.temperature_min) FROM climate_monthly_data cmd WHERE cmd.location_id = wwde_locations.id AND cmd.year >= ?)
                    ) ASC", [now()->subYear()->year, now()->subYear()->year, now()->subYear()->year]);
                } else {
                    $query->orderByRaw("COALESCE(
                        (SELECT MAX(cmd.temperature_avg) FROM climate_monthly_data cmd WHERE cmd.location_id = wwde_locations.id AND cmd.year >= ?),
                        (SELECT MAX(cmd.temperature_max) FROM climate_monthly_data cmd WHERE cmd.location_id = wwde_locations.id AND cmd.year >= ?),
                        (SELECT MAX(cmd.temperature_min) FROM climate_monthly_data cmd WHERE cmd.location_id = wwde_locations.id AND cmd.year >= ?)
                    ) DESC", [now()->subYear()->year, now()->subYear()->year, now()->subYear()->year]);
                }
            } else {
                $query->orderBy('title', 'asc');
            }

            // Ergebnisse paginieren (ohne Cache)
            $locations = $query->paginate($this->perPage)->withQueryString();

            // 🔍 Fallback für fehlende Klimadaten aus historischen Daten
            $locations->transform(function ($location) {
                if (empty($location->climates)) {
                    $historicalClimate = $location->historicalClimates->last(); // Letzte verfügbare Daten
                    if ($historicalClimate) {
                        $location->climate_data = [
                            'main' => ['temp' => $historicalClimate->temperature_avg],
                            'temp_max' => $historicalClimate->temperature_max,
                            'temp_min' => $historicalClimate->temperature_min,
                            'sunshine_hours' => $historicalClimate->sunshine_hours,
                        ];
                    }
                }
                return $location;
            });
        }

    // Ergebnis zählen
    if ($locations instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $this->totalResults = $locations->total();
    } else {
        $this->totalResults = $locations->count();
    }


        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $locations,
            'selectedMonth' => $this->urlaub,
        ]);
    }






}
