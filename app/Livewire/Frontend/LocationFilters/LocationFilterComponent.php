<?php

namespace App\Livewire\Frontend\LocationFilters;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class LocationFilterComponent extends Component
{
    public $sports = false;
    public $freizeitparks = false;
    public $country = '';
    public $continent = '';

    public $countries = [];
    public $continents = [];

    public function mount()
    {
        // Lade alle verfügbaren Länder und Kontinente für das Dropdown
        $this->countries = DB::table('wwde_countries')->pluck('title', 'id')->toArray();
        $this->continents = DB::table('wwde_continents')->pluck('title', 'id')->toArray();
    }

    public function render()
    {
        $locations = DB::table('wwde_locations')
            ->join('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id')
            ->join('wwde_continents', 'wwde_countries.continent_id', '=', 'wwde_continents.id')
            ->select('wwde_locations.title as location', 'wwde_countries.title as country', 'wwde_continents.title as continent', 'wwde_locations.list_sports', 'wwde_locations.list_amusement_park')
            ->when($this->sports, function ($query) {
                return $query->where('wwde_locations.list_sports', 1);
            })
            ->when($this->freizeitparks, function ($query) {
                return $query->where('wwde_locations.list_amusement_park', 1);
            })
            ->when($this->country, function ($query) {
                return $query->where('wwde_countries.id', $this->country);
            })
            ->when($this->continent, function ($query) {
                return $query->where('wwde_continents.id', $this->continent);
            })
            ->orderBy('wwde_locations.title', 'asc')
            ->get();

        return view('livewire.frontend.location-filters.location-filter-component', [
            'locations' => $locations
        ]);
    }
}
