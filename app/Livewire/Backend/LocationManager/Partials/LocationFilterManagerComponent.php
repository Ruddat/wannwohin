<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModLocationFilter;

class LocationFilterManagerComponent extends Component
{
    use WithPagination;

    public $locationId;
    public $selectedTypes = [];
    public $selectedUschrift = [];
    public $textTypeOptions = [];
    public $uschrifts = [];

    // Formularfelder (für Hinzufügen und Bearbeiten)
    public $formTextType;
    public $formUschrift;
    public $formText;
    public $formCategory;
    public $formAddinfo;
    public $formActiveStatus = true;
    public $isFormVisible = false;
    public $editingTextId = null;

    // Neue Eigenschaft für die Löschbestätigung
    public $confirming = null;

    public function mount($locationId)
    {
        $this->locationId = $locationId;
        $this->textTypeOptions = ModLocationFilter::distinct()->pluck('text_type')->toArray();
    }

    public function updatedSelectedTypes()
    {
        $this->uschrifts = ModLocationFilter::where('location_id', $this->locationId)
            ->whereIn('text_type', $this->selectedTypes)
            ->distinct()
            ->pluck('uschrift')
            ->toArray();
        $this->resetPage();
        $this->isFormVisible = false; // Formular ausblenden
    }

    public function updatedSelectedUschrift()
    {
        $this->resetPage();
        $this->isFormVisible = false; // Formular ausblenden
    }

    public function resetFilters()
    {
        $this->selectedTypes = [];
        $this->selectedUschrift = [];
        $this->uschrifts = [];
        $this->resetPage();
    }

    public function showForm()
    {
        $this->isFormVisible = true;
        $this->resetFormFields();
        $this->editingTextId = null;
    }

    public function hideForm()
    {
        $this->isFormVisible = false;
        $this->resetFormFields();
        $this->editingTextId = null;
    }

    public function addText()
    {
        $this->validate([
            'formTextType' => 'required',
            'formUschrift' => 'required',
            'formText' => 'required',
            'formCategory' => 'nullable|string|max:255',
            'formAddinfo' => 'nullable|string',
            'formActiveStatus' => 'boolean',
        ]);

        ModLocationFilter::create([
            'location_id' => $this->locationId,
            'text_type' => $this->formTextType,
            'uschrift' => $this->formUschrift,
            'text' => $this->formText,
            'category' => $this->formCategory,
            'addinfo' => $this->formAddinfo,
            'is_active' => $this->formActiveStatus,
        ]);

        $this->hideForm();
        $this->dispatch('show-toast', type: 'success', message: 'Text erfolgreich hinzugefügt.');
    }

    public function editFilterText($id)
    {
        $text = ModLocationFilter::find($id);
        if ($text) {
            $this->editingTextId = $id;
            $this->formTextType = $text->text_type;
            $this->formUschrift = $text->uschrift;
            $this->formText = $text->text;
            $this->formCategory = $text->category;
            $this->formAddinfo = $text->addinfo;
            $this->formActiveStatus = (bool) $text->is_active;
            $this->isFormVisible = true;
        }
    }

    public function updateText()
    {
        $this->validate([
            'formTextType' => 'required',
            'formUschrift' => 'required',
            'formText' => 'required',
            'formCategory' => 'nullable|string|max:255',
            'formAddinfo' => 'nullable|string',
            'formActiveStatus' => 'boolean',
        ]);

        $text = ModLocationFilter::find($this->editingTextId);
        if ($text) {
            $text->update([
                'text_type' => $this->formTextType,
                'uschrift' => $this->formUschrift,
                'text' => $this->formText,
                'category' => $this->formCategory,
                'addinfo' => $this->formAddinfo,
                'is_active' => $this->formActiveStatus,
            ]);
            $this->hideForm();
            $this->dispatch('show-toast', type: 'success', message: 'Text erfolgreich aktualisiert.');
        }
    }

    public function toggleActive($id)
    {
        $text = ModLocationFilter::find($id);
        if ($text) {
            $text->is_active = !$text->is_active;
            $text->save();
            $this->dispatch('show-toast', type: 'success', message: 'Status von Text #' . $text->id . ' geändert.');
        }
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function kill($id)
    {
        $text = ModLocationFilter::find($id);
        if ($text) {
            $text->delete();
            $this->dispatch('show-toast', type: 'success', message: 'Text erfolgreich gelöscht.');
        } else {
            $this->dispatch('show-toast', type: 'error', message: 'Text nicht gefunden.');
        }
        $this->confirming = null;
    }

    private function resetFormFields()
    {
        $this->formTextType = '';
        $this->formUschrift = '';
        $this->formText = '';
        $this->formCategory = '';
        $this->formAddinfo = '';
        $this->formActiveStatus = true;
    }

    public function render()
    {
        $query = ModLocationFilter::where('location_id', $this->locationId);

        if (!empty($this->selectedTypes)) {
            $query->whereIn('text_type', $this->selectedTypes);
        }

        if (!empty($this->selectedUschrift)) {
            $query->whereIn('uschrift', $this->selectedUschrift);
        }

        $texts = $query->paginate(10);

        return view('livewire.backend.location-manager.partials.location-filter-manager-component', [
            'texts' => $texts,
        ]);
    }
}
