<?php

namespace App\Livewire\Backend\ParkListManager;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\AmusementParks;
use App\Services\GeocodeService;

class ParkListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $country = '';
    public $status = '';
    public $parkIdToDelete;
    public $parkIdToEdit;
    public $countries = [];
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $geocodeService;

    public function mount()
    {
        $this->countries = AmusementParks::select('country')->distinct()->pluck('country')->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCountry()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingSortDirection()
    {
        $this->resetPage();
    }


    public function openCreateModal()
    {
        $this->parkIdToEdit = null; // Kein Park zum Bearbeiten, also leer
        $this->dispatch('open-modal');
    }

    public function updateCoordinates($id)
    {
        $park = AmusementParks::find($id);
        if (!$park || !$park->name) {
            $this->dispatch('show-toast', type: 'error', message: 'Standortinformationen fehlen oder der Park existiert nicht.');
            return;
        }
        try {
            $geocodeService = app(GeocodeService::class);
            $coordinates = $geocodeService->searchByParkName($park->name);
            if (!isset($coordinates[0]['lat']) || !isset($coordinates[0]['lon'])) {
                throw new \Exception('Ungültige Koordinaten vom GeocodeService erhalten.');
            }
            $park->latitude = (float) $coordinates[0]['lat'];
            $park->longitude = (float) $coordinates[0]['lon'];
            $park->save();
            $this->dispatch('show-toast', type: 'success', message: 'Koordinaten wurden erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Fehler: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->parkIdToDelete = $id;
        $this->dispatch('delete-prompt', [
            'title' => 'Sind Sie sicher?',
            'text' => 'Dieser Park wird dauerhaft gelöscht. Dies kann nicht rückgängig gemacht werden.',
            'icon' => 'warning',
            'confirmButtonText' => 'Ja, löschen!',
            'cancelButtonText' => 'Abbrechen',
        ]);
    }

    #[On('goOn-Delete')]
    public function deleteConfirmed()
    {
        if ($this->parkIdToDelete) {
            $park = AmusementParks::findOrFail($this->parkIdToDelete);
            $park->delete();
            $this->parkIdToDelete = null;
            $this->dispatch('show-toast', type: 'success', message: 'Park erfolgreich gelöscht.');
        }
    }

    #[On('close-modal')]
    public function closeModal()
    {
        $this->parkIdToEdit = null;
    }

    public function openEditModal($id)
    {
        $this->parkIdToEdit = $id;
        $this->dispatch('open-modal'); // Neues Event zum Öffnen des Modals
    }

    public function render()
    {
        $query = AmusementParks::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->when($this->country, function ($query) {
                $query->where('country', $this->country);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $parks = $this->perPage === 'all'
            ? $query->get()
            : $query->paginate($this->perPage);

        return view('livewire.backend.park-list-manager.park-list-component', compact('parks'))
            ->layout('raadmin.layout.master');
    }
}
