<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeLocation;
use App\Models\WwdeCountry;
use Livewire\WithPagination;

class LocationTableComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterCountry = ''; // Länderfilter
    public $filterStatus = ''; // Statusfilter
    public $sortField = 'id'; // Standard-Sortierfeld
    public $sortDirection = 'asc'; // Standard-Sortierreihenfolge

    protected $listeners = ['refreshLocations' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus($locationId)
    {
        $location = WwdeLocation::find($locationId);

        if ($location) {
            $nextStatus = match ($location->status) {
                'active' => 'pending',
                'pending' => 'inactive',
                default => 'active',
            };
            $location->status = $nextStatus;
            $location->save();
            $this->dispatch('refreshLocations');
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterCountry', 'filterStatus', 'perPage', 'sortField', 'sortDirection']);
        $this->resetPage();
    }

    public function render()
    {
        $locations = WwdeLocation::query()
            ->with('country') // Beziehungen laden
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                      ->orWhere('iata_code', 'like', "%{$this->search}%");
            })
            ->when($this->filterCountry, function ($query) {
                $query->where('country_id', $this->filterCountry);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->sortField === 'country', function ($query) {
                $query->join('wwde_countries as c', 'c.id', '=', 'wwde_locations.country_id')
                      ->orderBy('c.title', $this->sortDirection)
                      ->select('wwde_locations.*');
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate($this->perPage);

        $countries = WwdeCountry::all();

        return view('livewire.backend.location-manager.location-table-component', [
            'locations' => $locations,
            'countries' => $countries,
        ])->layout('backend.layouts.livewiere-main');
    }
}
