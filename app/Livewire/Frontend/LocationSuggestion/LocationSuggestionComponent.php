<?php

namespace App\Livewire\Frontend\LocationSuggestion;

use Livewire\Component;

class LocationSuggestionComponent extends Component
{
    public $isOpen = false;

    public function openOverlay()
    {
        $this->isOpen = true;
    }

    public function closeOverlay()
    {
        $this->isOpen = false;
    }

    public function submitSuggestion()
    {
        // Logik, um den neuen Ort zu speichern
        $this->closeOverlay();
        session()->flash('message', 'Vorschlag wurde erfolgreich übermittelt!');
    }

    public function render()
    {
        return view('livewire.frontend.location-suggestion.location-suggestion-component');
    }
}
