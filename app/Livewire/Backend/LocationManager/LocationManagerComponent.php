<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeLocation;

class LocationManagerComponent extends Component
{
    public $locationId;
    public $location;
    public $activeTab = 'info';

    public function mount($locationId = null)
    {
        \Log::info("LocationManagerComponent mount() aufgerufen mit locationId: " . ($locationId ?? 'NULL'));

        if ($locationId) {
            $this->edit($locationId);
        }
    }

    public function edit($id)
    {
        $this->locationId = $id;
        $this->location = WwdeLocation::find($id);

        \Log::info("edit() aufgerufen mit ID: " . $id);
        \Log::info("Gefundene Location: " . ($this->location ? $this->location->title : 'NICHT GEFUNDEN'));

        $this->activeTab = 'info';
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.backend.location-manager.location-manager-component', [
            'locationId' => $this->locationId,
            'location' => $this->location,

            'activeTab' => $this->activeTab,
        ])->layout('backend.layouts.livewiere-main');
    }
}
