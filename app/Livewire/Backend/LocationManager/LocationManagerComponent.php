<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeLocation;

class LocationManagerComponent extends Component
{
    public $locationId;
    public $location;
    public $activeTab = 'texts';

    public function mount($id = null)
    {
        if ($id) {
            $this->edit($id);
        }
    }

    public function edit($id)
    {
        $this->locationId = $id;
        $this->location = WwdeLocation::findOrFail($id);
        $this->activeTab = 'edit'; // Setzt den Tab auf "Edit"
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
