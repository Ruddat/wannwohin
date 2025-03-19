<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeLocation;

class LocationEditDataComponent extends Component
{
    public $locationId;
    public $location;

    public $iata_code;
    public $latitude;
    public $longitude;
    public $population;
    public $time_zone;
    public $price_flight;
    public $price_hotel;
    public $price_rental;

    public function mount($locationId)
    {
        $this->locationId = $locationId;
        $this->loadLocationData();
    }

    public function loadLocationData()
    {
        $this->location = WwdeLocation::findOrFail($this->locationId);

        $this->iata_code = $this->location->iata_code;
        $this->latitude = $this->location->lat;
        $this->longitude = $this->location->lon;
        $this->population = $this->location->population;
        $this->time_zone = $this->location->time_zone;
        $this->price_flight = $this->location->price_flight;
        $this->price_hotel = $this->location->price_hotel;
        $this->price_rental = $this->location->price_rental;
    }

    public function save()
    {
        $this->validate([
            'iata_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'population' => 'nullable|integer',
            'time_zone' => 'nullable|string|max:100',
            'price_flight' => 'nullable|numeric',
            'price_hotel' => 'nullable|numeric',
            'price_rental' => 'nullable|numeric',
        ]);

        $this->location->update([
            'iata_code' => $this->iata_code,
            'lat' => $this->latitude,
            'lon' => $this->longitude,
            'population' => $this->population,
            'time_zone' => $this->time_zone,
            'price_flight' => $this->price_flight,
            'price_hotel' => $this->price_hotel,
            'price_rental' => $this->price_rental,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Daten erfolgreich gespeichert.');

        $this->dispatch('refreshLocations');
    }

    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-data-component');
    }
}
