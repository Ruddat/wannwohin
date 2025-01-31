<?php

namespace App\Livewire\Backend\WeatherManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ClimateMonthlyData;

class ClimateDataManager extends Component
{
    use WithPagination;

    public $year;
    public $location_id;
    public $editing = [];
    public $newEntry = [
        'location_id' => '',
        'month' => '',
        'year' => '',
        'temperature_avg' => '',
        'temperature_max' => '',
        'temperature_min' => '',
        'precipitation' => '',
    ];

    public $locations;

    protected $rules = [
        'editing.temperature_avg' => 'nullable|numeric',
        'editing.temperature_max' => 'nullable|numeric',
        'editing.temperature_min' => 'nullable|numeric',
        'editing.precipitation' => 'nullable|numeric',
    ];

    public function mount()
    {
        $this->locations = \App\Models\WwdeLocation::all();
    }

    public function updatedYear()
    {
        $this->resetPage(); // Setzt die Pagination zurück
    }

    public function updatedLocationId($value)
    {
        $this->location_id = $value;
        $this->resetPage(); // Paginierung zurücksetzen
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['year', 'location_id'])) {
            $this->resetPage(); // Setzt die Paginierung zurück
        }
    }

    public function render()
    {
        $query = ClimateMonthlyData::with('location');

        if (!empty($this->year)) {
            $query->where('year', (int) $this->year); // Sicherstellen, dass year als Integer verglichen wird
        }

        if (!empty($this->location_id)) {
            $query->where('location_id', (int) $this->location_id);
        }

        $climateData = $query->orderBy('year', 'desc')->orderBy('month', 'asc')->paginate(12);

        return view('livewire.backend.weather-manager.climate-data-manager', compact('climateData'))
            ->layout('backend.layouts.livewiere-main');
    }

    public function edit($id)
    {
        $this->editing = ClimateMonthlyData::find($id)->toArray();
    }

    public function save()
    {
        $this->validate();

        ClimateMonthlyData::find($this->editing['id'])->update($this->editing);
        $this->editing = [];
        session()->flash('message', 'Eintrag erfolgreich aktualisiert.');
    }

    public function addNew()
    {
        $this->validate([
            'newEntry.location_id' => 'required|integer',
            'newEntry.month' => 'required|integer|min:1|max:12',
            'newEntry.year' => 'required|integer',
            'newEntry.temperature_avg' => 'nullable|numeric',
            'newEntry.temperature_max' => 'nullable|numeric',
            'newEntry.temperature_min' => 'nullable|numeric',
            'newEntry.precipitation' => 'nullable|numeric',
        ]);

        ClimateMonthlyData::create($this->newEntry);
        $this->newEntry = [];
        session()->flash('message', 'Neuer Eintrag hinzugefügt.');
    }
}
