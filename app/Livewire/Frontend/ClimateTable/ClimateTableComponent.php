<?php

namespace App\Livewire\Frontend\ClimateTable;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ClimateTableComponent extends Component
{
    public $locationId;
    public $year; // Ausgewähltes Jahr
    public $availableYears = []; // Dynamisch verfügbare Jahre

    public function mount($locationId)
    {
        $this->locationId = $locationId;

        // Dynamisch verfügbare Jahre aus der Datenbank abrufen
        $this->availableYears = DB::table('climate_monthly_data')
            ->where('location_id', $this->locationId)
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Standardwert: Das neueste verfügbare Jahr
        $this->year = $this->availableYears[0] ?? null;
    }

    public function render()
    {
        // Daten für das ausgewählte Jahr abrufen und korrekt sortieren
        $monthlyData = DB::table('climate_monthly_data')
            ->where('location_id', $this->locationId)
            ->where('year', $this->year) // Filter für das Jahr
            ->selectRaw('
                month_name,
                month,
                temperature_avg as daily_temperature,
                temperature_min as night_temperature,
                precipitation as rainfall,
                sunshine_hours as sunshine_per_day
            ')
            ->orderBy('month') // Nach Monat sortieren
            ->get();

        return view('livewire.frontend.climate-table.climate-table-component', compact('monthlyData'));
    }

    public function updatedYear()
    {
        // Wird aufgerufen, wenn das Jahr geändert wird
        $this->render();
    }
}
