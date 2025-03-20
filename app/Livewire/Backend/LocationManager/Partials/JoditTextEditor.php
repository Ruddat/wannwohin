<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;

class JoditTextEditor extends Component
{
    public $content;
    public $buttons = [];
    public $height = 200;

    public function mount($content = '', $buttons = [])
    {
        $this->content = $content;
        $this->buttons = $buttons;
    }

    public function updatedContent($value)
    {
        // Optional: Hier kannst du zusätzliche Logik hinzufügen, wenn der Wert aktualisiert wird
        $this->dispatch('contentUpdated', $value);
    }

    public function render()
    {
        return view('livewire.backend.location-manager.partials.jodit-text-editor');
    }
}
