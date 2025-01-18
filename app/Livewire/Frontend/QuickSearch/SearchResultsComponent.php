<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeLocation;
use App\Models\HeaderContent;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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

    public function mount()
    {
        // Header Content und Bilder laden
        $this->headerContent = Cache::remember('header_content_random', 5 * 60, function () {
            return HeaderContent::inRandomOrder()->first();
        });

        
        $this->bgImgPath = $this->headerContent->bg_img
        ? (Storage::exists($this->headerContent->bg_img)
            ? Storage::url($this->headerContent->bg_img)
            : (file_exists(public_path($this->headerContent->bg_img))
                ? asset($this->headerContent->bg_img)
                : null))
        : null;

    $this->mainImgPath = $this->headerContent->main_img
        ? (Storage::exists($this->headerContent->main_img)
            ? Storage::url($this->headerContent->main_img)
            : (file_exists(public_path($this->headerContent->main_img))
                ? asset($this->headerContent->main_img)
                : null))
        : null;

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
        // Header-Content und Bilder an das Template übergeben
        view()->share([
            'panorama_location_picture' => $this->bgImgPath,
            'main_location_picture' => $this->mainImgPath,
            'panorama_location_text' => $this->headerContent->main_text ?? null,
        ]);

        // Abfrage für die Suchergebnisse
        $query = WwdeLocation::query()
            ->where('status', 'active')
            ->where('finished', 1);

        if (!empty($this->continent)) {
            $query->where('continent_id', $this->continent);
        }

        if (!empty($this->price)) {
            $query->where('price_flight', '<=', $this->price);
        }

        if (!empty($this->urlaub)) {
            $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($this->urlaub)]);
        }

        if (!empty($this->sonnenstunden)) {
            $query->whereHas('climates', function ($q) {
                $q->where('sunshine_per_day', '>=', $this->sonnenstunden);
            });
        }

        if (!empty($this->wassertemperatur)) {
            $query->whereHas('climates', function ($q) {
                $q->where('water_temperature', '>=', $this->wassertemperatur);
            });
        }

        if (!empty($this->spezielle)) {
            foreach ($this->spezielle as $wish) {
                $query->where($wish, 1);
            }
        }

        // Ergebnisse sortieren und paginieren
        $locations = $query->orderBy($this->sortBy, $this->sortDirection)->paginate(10);

        // Template mit den Suchergebnissen rendern
        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $locations,
        ]);
    }
}
