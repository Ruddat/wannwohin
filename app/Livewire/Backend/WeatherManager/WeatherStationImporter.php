<?php

namespace App\Livewire\Backend\WeatherManager;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class WeatherStationImporter extends Component
{
    public $output = ''; // Variable für die Konsolenausgabe
    public $isRunning = false; // Status des Commands

    public function runCommand()
    {
        $this->isRunning = true;
        $this->output = ''; // Zurücksetzen der Ausgabe

        // Erstelle einen gepufferten Output
        $buffer = new BufferedOutput();

        // Führe den Command aus
        Artisan::call('weather:assign-stations', [], $buffer);

        // Speichere die Ausgabe in der Komponente
        $this->output = $buffer->fetch();

        $this->isRunning = false;
    }

    public function render()
    {
        return view('livewire.backend.weather-manager.weather-station-importer')
        ->layout('raadmin.layout.master');
    }
}
