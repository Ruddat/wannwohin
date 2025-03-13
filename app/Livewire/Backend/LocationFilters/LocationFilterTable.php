<?php

namespace App\Livewire\Backend\LocationFilters;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModLocationFilter;
use App\Models\WwdeLocation;

class LocationFilterTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    // Bearbeitungs-Properties
    public $editingFilterId = null;
    public $editTextType = '';
    public $editUschrift = '';
    public $editText = '';
    public $editLocationId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

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

    public function delete($id)
    {
        try {
            $filter = ModLocationFilter::findOrFail($id);
            $filter->delete();
            //$this->dispatch('notify', ['type' => 'success', 'message' => 'Eintrag erfolgreich gelöscht']);
            $this->dispatch('show-toast', type: 'success', message: 'Eintrag erfolgreich gelöscht');
        } catch (\Exception $e) {
            //$this->dispatch('notify', ['type' => 'error', 'message' => 'Fehler beim Löschen']);
            $this->dispatch('show-toast', type: 'error', message: 'Fehler beim Löschen');
        }
    }

    public function edit($id)
    {
        $filter = ModLocationFilter::findOrFail($id);
        $this->editingFilterId = $id;
        $this->editTextType = $filter->text_type;
        $this->editUschrift = $filter->uschrift;
        $this->editText = $filter->text;
        $this->editLocationId = $filter->location_id;
    }

    public function update()
    {
        $this->validate([
            'editTextType' => 'required|string|max:255',
            'editUschrift' => 'required|string|max:255',
            'editText' => 'required|string',
            'editLocationId' => 'required|exists:wwde_locations,id',
        ]);

        $filter = ModLocationFilter::findOrFail($this->editingFilterId);
        $filter->update([
            'text_type' => $this->editTextType,
            'uschrift' => $this->editUschrift,
            'text' => $this->editText,
            'location_id' => $this->editLocationId,
        ]);

        $this->cancelEdit();
        // $this->dispatch('notify', ['type' => 'success', 'message' => 'Eintrag erfolgreich aktualisiert']);
        $this->dispatch('show-toast', type: 'success', message: 'Eintrag erfolgreich aktualisiert');
    }

    public function cancelEdit()
    {
        $this->editingFilterId = null;
        $this->editTextType = '';
        $this->editUschrift = '';
        $this->editText = '';
        $this->editLocationId = '';
    }

    public function render()
    {
        $query = ModLocationFilter::with('location')
            ->leftJoin('wwde_locations', 'mod_location_filters.location_id', '=', 'wwde_locations.id')
            ->select('mod_location_filters.*') // Vermeide Spaltenkonflikte
            ->where(function ($query) {
                if ($this->search) {
                    $query->where('mod_location_filters.text_type', 'like', "%{$this->search}%")
                        ->orWhere('mod_location_filters.uschrift', 'like', "%{$this->search}%")
                        ->orWhere('mod_location_filters.text', 'like', "%{$this->search}%")
                        ->orWhere('wwde_locations.title', 'like', "%{$this->search}%");
                }
            });

        // Sortierung anpassen
        if ($this->sortField === 'location.title') {
            $query->orderBy('wwde_locations.title', $this->sortDirection);
        } else {
            $query->orderBy('mod_location_filters.' . $this->sortField, $this->sortDirection);
        }

        $filters = $query->paginate($this->perPage);

        // Alle Locations für die Dropdown-Auswahl laden
        $locations = WwdeLocation::select('id', 'title')->orderBy('title')->get();

        return view('livewire.backend.location-filters.location-filter-table', [
            'filters' => $filters,
            'locations' => $locations,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ])->layout('backend.layouts.livewiere-main');
    }
}
