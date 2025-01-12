<?php

namespace App\Livewire\Backend\CommandManager;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;

class ElectricScraperComponent extends Component
{
    public $output;

    public function runScraper()
    {
        try {
            // Artisan Command ausführen
            Artisan::call('scrape:electric-standards');

            // Ausgabe speichern
           // $this->output = Artisan::output();

            session()->flash('message', 'Scraping erfolgreich ausgeführt.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Scraping: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.backend.command-manager.electric-scraper-component');
    }
}
