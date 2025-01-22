<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeLocation;
use App\Models\HeaderContent;
use Livewire\WithPagination;
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

        session()->put('headerData', [
            'headerContent' => $headerData['headerContent'] ?? null,
            'bgImgPath' => $headerData['bgImgPath'] ?? null,
            'mainImgPath' => $headerData['mainImgPath'] ?? null,
        ]);

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
            ->active() // Filter: Nur aktive und fertige Locations
            ->filterByContinent($this->continent)
            ->filterByPrice($this->price)
            ->filterByTravelTime($this->urlaub)
            ->filterBySunshine($this->sonnenstunden)
            ->filterByWaterTemperature($this->wassertemperatur)
            ->filterBySpecials($this->spezielle);

        // Ergebnisse sortieren und paginieren
        $locations = $query->orderBy("wwde_locations.{$this->sortBy}", $this->sortDirection)
            ->paginate(10);

        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $locations,
        ]);
    }
}
