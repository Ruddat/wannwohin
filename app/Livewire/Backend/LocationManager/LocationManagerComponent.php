<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeLocation;

class LocationManagerComponent extends Component
{
    public $locationId;
    public $location;
    public $activeTab = 'info';
    public $isModalOpen = false;

    protected $listeners = [
        'openEditModal' => 'edit', // Lauscht auf das Ereignis
    ];

    public function mount($locationId = null)
    {
        if ($locationId) {
            $this->edit($locationId);
        }
    }

    public function edit($id)
    {
        $this->locationId = $id;
        $this->location = WwdeLocation::find($id);
        $this->isModalOpen = true;
        $this->activeTab = 'info';
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
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
