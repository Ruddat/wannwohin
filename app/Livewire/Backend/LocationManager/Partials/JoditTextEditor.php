<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;

class JoditTextEditor extends Component
{
    public $content; // Der Inhalt des Editors, der mit wire:model gebunden wird
    public $config;  // Konfigurationsoptionen für Jodit
    public $buttons; // Buttons für die Jodit-Toolbar
    public $height = 300; // Standardhöhe des Editors

    public function mount($config = [])
    {
        // Setze die Konfigurationsoptionen
        $this->config = $config;

        // Extrahiere die Buttons aus der Konfiguration, falls vorhanden
        $this->buttons = $config['buttons'] ?? [
            'bold', 'italic', 'underline', 'strikeThrough', '|',
            'left', 'center', 'right', '|', 'link'
        ];

        // Setze die Höhe, falls in der Konfiguration angegeben
        if (isset($config['height'])) {
            $this->height = $config['height'];
        }
    }

    public function render()
    {
        return view('livewire.backend.location-manager.partials.jodit-text-editor');
    }
}
