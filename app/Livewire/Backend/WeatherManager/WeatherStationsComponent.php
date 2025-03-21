<?php

namespace App\Livewire\Backend\WeatherManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WeatherStations;

class WeatherStationsComponent extends Component
{
    use WithPagination;

    public $stationId, $name, $country, $region, $latitude, $longitude, $elevation, $timezone, $inventory;
    public $editMode = false;
    public $showForm = false; // Neues Attribut zum Steuern der Sichtbarkeit des Formulars
    public $search = '';
    public $perPage = 10;

    protected $rules = [
        'stationId' => 'required|string|max:10',
        'name' => 'required|string|max:255',
        'country' => 'nullable|string|max:2',
        'region' => 'nullable|string|max:50',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'elevation' => 'nullable|numeric',
        'timezone' => 'required|string|max:50',
        'inventory' => 'nullable|json',
    ];

    public function resetFields()
    {
        $this->stationId = $this->name = $this->country = $this->region = $this->latitude =
        $this->longitude = $this->elevation = $this->timezone = $this->inventory = null;
        $this->editMode = false;
        $this->showForm = false; // Formular ausblenden
    }

    public function addStation()
    {
        $this->validate();
        WeatherStations::create([
            'station_id' => $this->stationId,
            'name' => $this->name,
            'country' => $this->country,
            'region' => $this->region,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'elevation' => $this->elevation,
            'timezone' => $this->timezone,
            'inventory' => $this->inventory,
        ]);

        session()->flash('message', 'Wetterstation erfolgreich hinzugefügt!');
        $this->resetFields();
    }

    public function editStation($id)
    {
        $station = WeatherStations::findOrFail($id);
        $this->stationId = $station->station_id;
        $this->name = $station->name;
        $this->country = $station->country;
        $this->region = $station->region;
        $this->latitude = $station->latitude;
        $this->longitude = $station->longitude;
        $this->elevation = $station->elevation;
        $this->timezone = $station->timezone;
        $this->inventory = json_encode($station->inventory);
        $this->editMode = $id;
        $this->showForm = true; // Formular anzeigen
    }

    public function updateStation()
    {
        $this->validate();
        $station = WeatherStations::findOrFail($this->editMode);

        $station->update([
            'station_id' => $this->stationId,
            'name' => $this->name,
            'country' => $this->country,
            'region' => $this->region,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'elevation' => $this->elevation,
            'timezone' => $this->timezone,
            'inventory' => $this->inventory,
        ]);

        session()->flash('message', 'Wetterstation erfolgreich aktualisiert!');
        $this->resetFields();
    }

    public function deleteStation($id)
    {
        WeatherStations::findOrFail($id)->delete();
        session()->flash('message', 'Wetterstation erfolgreich gelöscht!');
    }

    public function render()
    {
        $stations = WeatherStations::where('name', 'like', "%{$this->search}%")
            ->orWhere('station_id', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.backend.weather-manager.weather-stations-component', compact('stations'))
        ->layout('raadmin.layout.master');
    }
}
