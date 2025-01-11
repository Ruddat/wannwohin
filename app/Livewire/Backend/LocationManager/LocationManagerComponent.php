<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;

class LocationManagerComponent extends Component
{
    public $locationId;
    public $activeTab = 'texts';

    public function mount($locationId)
    {
        $this->locationId = $locationId;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.backend.location-manager.location-manager-component', [
            'locationId' => $this->locationId,
            'activeTab' => $this->activeTab,
        ])->layout('backend.layouts.livewiere-main');
    }
}
