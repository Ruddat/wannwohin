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
    public $countries = [];
    public $perPage = 10;
    public $sortBy = 'created_at'; // Standard: Sortieren nach Erstellungsdatum
    public $sortDirection = 'desc'; // Standard: Absteigend


    protected $geocodeService;

    public function mount()
    {
        // Länderliste laden
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

    public function updateCoordinates($id)
    {
        $park = AmusementParks::find($id);

        if (!$park || !$park->name) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Fehler',
                'text' => 'Standortinformationen fehlen oder der Park existiert nicht.',
            ]);
            return;
        }

        try {
            // GeocodeService instanziieren
            $geocodeService = app(GeocodeService::class);

            // Koordinaten über den GeocodeService abrufen
            $coordinates = $geocodeService->searchByParkName($park->name);

            if (!isset($coordinates[0]['lat']) || !isset($coordinates[0]['lon'])) {
                throw new \Exception('Ungültige Koordinaten vom GeocodeService erhalten.');
            }


            // Koordinaten speichern
            $park->latitude = (float) $coordinates[0]['lat'];
            $park->longitude = (float) $coordinates[0]['lon'];
          //  dd($park);
            $park->save();

            // Erfolgsmeldung
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Erfolgreich',
                'text' => 'Koordinaten wurden erfolgreich aktualisiert.',
            ]);
        } catch (\Exception $e) {
            // Fehlermeldung anzeigen
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Fehler',
                'text' => $e->getMessage(),
            ]);
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

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Gelöscht!',
                'text' => 'Der Park wurde erfolgreich gelöscht.',
            ]);

            session()->flash('success', 'Park erfolgreich gelöscht.');
        }
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
            ->orderBy($this->sortBy, $this->sortDirection); // Sortieren nach Auswahl

        $parks = $this->perPage === 'all'
            ? $query->get()
            : $query->paginate($this->perPage);

        return view('livewire.backend.park-list-manager.park-list-component', compact('parks'))
            ->layout('backend.layouts.livewiere-main');
    }
}
