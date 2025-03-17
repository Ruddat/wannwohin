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
    public $editCategory = '';
    public $editUschrift = '';
    public $editText = '';
    public $editAddinfo = '';
    public $editIsActive = 1;
    public $editLocationId = '';

    // Erstellen-Properties
    public $creating = false;
    public $newTextType = '';
    public $newCategory = '';
    public $newUschrift = '';
    public $newText = '';
    public $newAddinfo = '';
    public $newIsActive = 1;
    public $newLocationId = '';

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

    public function startCreating()
    {
        $this->creating = true;
    }

    public function create()
    {
        $this->validate([
            'newTextType' => 'required|string|max:255',
            'newCategory' => 'nullable|string|max:255',
            'newUschrift' => 'required|string|max:255',
            'newText' => 'required|string',
            'newAddinfo' => 'nullable|string',
            'newIsActive' => 'required|boolean',
            'newLocationId' => 'required|exists:wwde_locations,id',
        ]);

        ModLocationFilter::create([
            'text_type' => $this->newTextType,
            'category' => $this->newCategory,
            'uschrift' => $this->newUschrift,
            'text' => $this->newText,
            'addinfo' => $this->newAddinfo,
            'is_active' => $this->newIsActive,
            'location_id' => $this->newLocationId,
        ]);

        $this->resetCreateForm();
        $this->dispatch('show-toast', type: 'success', message: 'Filter erfolgreich erstellt');
    }

    public function resetCreateForm()
    {
        $this->creating = false;
        $this->newTextType = '';
        $this->newCategory = '';
        $this->newUschrift = '';
        $this->newText = '';
        $this->newAddinfo = '';
        $this->newIsActive = 1;
        $this->newLocationId = '';
    }

    public function delete($id)
    {
        try {
            $filter = ModLocationFilter::findOrFail($id);
            $filter->delete();
            $this->dispatch('show-toast', type: 'success', message: 'Eintrag erfolgreich gelöscht');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Fehler beim Löschen: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $filter = ModLocationFilter::findOrFail($id);
        $this->editingFilterId = $id;
        $this->editTextType = $filter->text_type;
        $this->editCategory = $filter->category;
        $this->editUschrift = $filter->uschrift;
        $this->editText = $filter->text;
        $this->editAddinfo = $filter->addinfo;
        $this->editIsActive = $filter->is_active;
        $this->editLocationId = $filter->location_id;
    }

    public function update()
    {
        $this->validate([
            'editTextType' => 'required|string|max:255',
            'editCategory' => 'nullable|string|max:255',
            'editUschrift' => 'required|string|max:255',
            'editText' => 'required|string',
            'editAddinfo' => 'nullable|string',
            'editIsActive' => 'required|boolean',
            'editLocationId' => 'required|exists:wwde_locations,id',
        ]);

        $filter = ModLocationFilter::findOrFail($this->editingFilterId);
        $filter->update([
            'text_type' => $this->editTextType,
            'category' => $this->editCategory,
            'uschrift' => $this->editUschrift,
            'text' => $this->editText,
            'addinfo' => $this->editAddinfo,
            'is_active' => $this->editIsActive,
            'location_id' => $this->editLocationId,
        ]);

        $this->cancelEdit();
        $this->dispatch('show-toast', type: 'success', message: 'Eintrag erfolgreich aktualisiert');
    }

    public function cancelEdit()
    {
        $this->editingFilterId = null;
        $this->editTextType = '';
        $this->editCategory = '';
        $this->editUschrift = '';
        $this->editText = '';
        $this->editAddinfo = '';
        $this->editIsActive = 1;
        $this->editLocationId = '';
    }


// Neue Methode zum Togglen des Aktiv-Status
public function toggleActive($id)
{
    try {
        $filter = ModLocationFilter::findOrFail($id);
        $filter->update([
            'is_active' => !$filter->is_active,
        ]);
        $this->dispatch('show-toast', type: 'success', message: 'Status erfolgreich geändert');
    } catch (\Exception $e) {
        $this->dispatch('show-toast', type: 'error', message: 'Fehler beim Ändern des Status: ' . $e->getMessage());
    }
}




public function render()
    {
        $query = ModLocationFilter::with('location')
            ->leftJoin('wwde_locations', 'mod_location_filters.location_id', '=', 'wwde_locations.id')
            ->select('mod_location_filters.*')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('mod_location_filters.text_type', 'like', "%{$this->search}%")
                        ->orWhere('mod_location_filters.category', 'like', "%{$this->search}%")
                        ->orWhere('mod_location_filters.uschrift', 'like', "%{$this->search}%")
                        ->orWhere('mod_location_filters.text', 'like', "%{$this->search}%")
                        ->orWhere('mod_location_filters.addinfo', 'like', "%{$this->search}%")
                        ->orWhere('wwde_locations.title', 'like', "%{$this->search}%");
                });
            });

        $query->orderBy(
            $this->sortField === 'location.title' ? 'wwde_locations.title' : 'mod_location_filters.' . $this->sortField,
            $this->sortDirection
        );

        $filters = $query->paginate($this->perPage);
        $locations = WwdeLocation::select('id', 'title')->orderBy('title')->get();

        return view('livewire.backend.location-filters.location-filter-table', [
            'filters' => $filters,
            'locations' => $locations,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ])->layout('backend.layouts.livewiere-main');
    }
}

