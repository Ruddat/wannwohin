<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeLocation;
use Livewire\WithPagination;

class LocationTableComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $listeners = ['refreshLocations' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteLocationConfirmation($locationId)
    {
        $this->dispatch('confirm-delete', [
            'locationId' => $locationId,
        ]);
    }

    public function deleteLocation($locationId)
    {
        $location = WwdeLocation::find($locationId);

        if ($location) {
            $location->delete();
            session()->flash('success', 'Location erfolgreich gelöscht.');
            $this->dispatch('refreshLocations');
        } else {
            session()->flash('status', 'Location konnte nicht gefunden werden.');
        }
    }

    public function render()
    {
        $locations = WwdeLocation::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                      ->orWhere('iata_code', 'like', "%{$this->search}%");
            })
            ->paginate($this->perPage);

        return view('livewire.backend.location-manager.location-table-component', [
            'locations' => $locations,
        ])->layout('backend.layouts.livewiere-main');
    }
}
