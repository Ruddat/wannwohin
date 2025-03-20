<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeLocation;

class LocationManagerComponent extends Component
{
    public $locationId = null; // Standardwert null
    public $location = null;
    public $activeTab = 'info';
    public $isModalOpen = false;
    public $isLoading = false; // Neue Variable für Ladezustand

    protected $listeners = [
        'openEditModal' => 'edit',
    ];

    public function mount($locationId = null)
    {
        if ($locationId) {
            $this->edit($locationId);
        }
    }

    public function edit($id)
    {
        $this->isLoading = true; // Ladeanzeige starten
        $this->locationId = $id;
        $this->location = WwdeLocation::find($id);
        if ($this->location) { // Nur öffnen, wenn Location existiert
            $this->isModalOpen = true;
            $this->activeTab = 'info';
        } else {
            $this->locationId = null; // Zurücksetzen, wenn Location nicht gefunden
            $this->isModalOpen = false;
        }
        $this->isLoading = false; // Ladeanzeige beenden
    }

    public function closeModal()
    {
        \Log::info('Modal closed', ['locationId' => $this->locationId]);
        $this->isModalOpen = false;
        $this->reset(['locationId', 'location', 'activeTab']);
        $this->dispatch('modalClosed')->to(LocationTableComponent::class);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.backend.location-manager.location-manager-component');
    }
}
