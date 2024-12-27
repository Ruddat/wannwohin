<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeLocation;

class SearchResultsComponent extends Component
{
    public $continent;
    public $price;
    public $urlaub;
    public $sonnenstunden;
    public $wassertemperatur;
    public $spezielle;

    public $locations; // Ergebnisse der Suche

    public function mount()
    {
        // Suchparameter aus der URL verwenden, um Standorte zu filtern
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
            $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', ['"' . $this->urlaub . '"']);
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

        $this->locations = $query->get();
    }

    public function render()
    {
        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $this->locations,
        ])
        ->layout('layouts.main');
    }
}
