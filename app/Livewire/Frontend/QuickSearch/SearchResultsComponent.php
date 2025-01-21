<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeLocation;
use App\Models\HeaderContent;
use Livewire\WithPagination;
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

    public $sortBy = 'title';
    public $sortDirection = 'asc';

    public $headerContent;
    public $bgImgPath;
    public $mainImgPath;

    public function mount(LocationRepository $repository)
    {
        // Header Content und Bilder laden
        $headerData = $repository->getHeaderContent();

        // Header-Daten in der Session speichern
        session()->put('headerData', [
            'headerContent' => $headerData['headerContent'] ?? null,
            'bgImgPath' => $headerData['bgImgPath'] ?? null,
            'mainImgPath' => $headerData['mainImgPath'] ?? null,
        ]);

        // Header-Daten in Livewire-Variablen setzen (falls benötigt)
        $this->headerContent = $headerData['headerContent'] ?? null;
        $this->bgImgPath = $headerData['bgImgPath'] ?? null;
        $this->mainImgPath = $headerData['mainImgPath'] ?? null;

        // Suchparameter aus der URL laden
        $this->continent = request('continent');
        $this->price = request('price');
        $this->urlaub = request('urlaub');
        $this->sonnenstunden = request('sonnenstunden');
        $this->wassertemperatur = request('wassertemperatur');
        $this->spezielle = request('spezielle');
    }


    public function updatedSortBy()
    {
        $this->resetPage(); // Pagination zurücksetzen, wenn die Sortierung geändert wird
    }

    public function render()
    {
        $query = WwdeLocation::query()
            ->select('wwde_locations.*') // Eindeutige Spaltenauswahl
            ->join('wwde_climates', 'wwde_locations.id', '=', 'wwde_climates.location_id')
            ->where('wwde_locations.status', 'active')
            ->where('wwde_locations.finished', 1);

        // Filter: Kontinent
        if (!empty($this->continent)) {
            $query->where('wwde_locations.continent_id', $this->continent);
        }

        // Filter: Preis
        if (!empty($this->price)) {
            $query->where('wwde_locations.price_flight', '<=', $this->price);
        }

        // Filter: Reisezeit
        if (!empty($this->urlaub)) {
            $query->whereRaw('JSON_CONTAINS(wwde_locations.best_traveltime_json, ?)', [json_encode($this->urlaub)]);
        }

        // Filter: Sonnenstunden
        if (!empty($this->sonnenstunden)) {
            $query->where('wwde_climates.sunshine_per_day', '>=', $this->sonnenstunden);
        }

        // Filter: Wassertemperatur
        if (!empty($this->wassertemperatur)) {
            $query->where('wwde_climates.water_temperature', '>=', $this->wassertemperatur);
        }

        // Filter: Spezielle Wünsche
        if (!empty($this->spezielle)) {
            foreach ($this->spezielle as $wish) {
                $query->where("wwde_locations.$wish", 1);
            }
        }

        // Ergebnisse sortieren und paginieren
        $locations = $query->orderBy("wwde_locations.{$this->sortBy}", $this->sortDirection)
            ->paginate(10);
//dd($locations, $this->headerContent->main_text);
        // Ergebnisse an die View übergeben
        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $locations,

        ]);
    }

}
