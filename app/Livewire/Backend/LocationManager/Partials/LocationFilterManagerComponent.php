<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\ModLocationFilter;

class LocationFilterManagerComponent extends Component
{
    public $locationId;
    public $selectedTypes = [];
    public $selectedUschrift = [];
    public $textTypeOptions = [];
    public $uschrifts = [];
    public $texts = [];

    // Eingabefelder für neue Texte
    public $newTextType;
    public $newUschrift;
    public $newText;

    public function mount($locationId)
    {
        $this->locationId = $locationId;

        // Lade alle verfügbaren Kategorien (text_type)
        $this->textTypeOptions = ModLocationFilter::distinct()->pluck('text_type')->toArray();

        $this->loadTexts();
    }

    public function updatedSelectedTypes()
    {
        // Lade alle passenden Überschriften basierend auf der Auswahl der Kategorie
        $this->uschrifts = ModLocationFilter::where('location_id', $this->locationId)
            ->whereIn('text_type', $this->selectedTypes)
            ->distinct()
            ->pluck('uschrift')
            ->toArray();
    }

    public function updatedSelectedUschrift()
    {
        $this->loadTexts();
    }

    public function loadTexts()
    {
        $query = ModLocationFilter::where('location_id', $this->locationId);

        if (!empty($this->selectedTypes)) {
            $query->whereIn('text_type', $this->selectedTypes);
        }

        if (!empty($this->selectedUschrift)) {
            $query->whereIn('uschrift', $this->selectedUschrift);
        }

        $this->texts = $query->get();
    }

    public function addText()
    {
        $this->validate([
            'newTextType' => 'required',
            'newUschrift' => 'required',
            'newText' => 'required',
        ]);

        ModLocationFilter::create([
            'location_id' => $this->locationId,
            'text_type' => $this->newTextType,
            'uschrift' => $this->newUschrift,
            'text' => $this->newText,
        ]);

        $this->newTextType = '';
        $this->newUschrift = '';
        $this->newText = '';

        $this->loadTexts();
    }

    public function deleteText($id)
    {
        ModLocationFilter::find($id)?->delete();
        $this->loadTexts();
    }

    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-filter-manager-component');
    }
}
