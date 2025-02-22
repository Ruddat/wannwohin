<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
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
                ->whereIn('wwde_locations.id', $filteredLocationIds)
                ->filterByContinent($this->continent)
                ->filterByPrice($this->price)
                ->filterBySunshine($this->sonnenstunden)
                ->filterByWaterTemperature($this->wassertemperatur)
                ->filterBySpecials($this->spezielle);

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

        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $locations,
            'selectedMonth' => $this->urlaub,
        ]);
    }






}
