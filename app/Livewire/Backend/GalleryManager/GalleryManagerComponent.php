<?php

namespace App\Livewire\Backend\GalleryManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModLocationGalerie;

class GalleryManagerComponent extends Component
{
    use WithPagination;

    public $locationId;
    public $locationName;
    public $imagePath;
    public $imageCaption;
    public $activity;
    public $description;
    public $imageHash;
    public $imageType = 'gallery';
    public $isPrimary = 0;

    public $editingId = null;

    protected $rules = [
        'locationId' => 'required|integer',
        'locationName' => 'required|string|max:255',
        'imagePath' => 'required|string|max:255',
        'imageCaption' => 'nullable|string|max:255',
        'activity' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'imageHash' => 'required|string|max:32',
        'imageType' => 'required|string|max:50',
        'isPrimary' => 'required|boolean',
    ];

    public function render()
    {
        return view('livewire.backend.gallery-manager.gallery-manager-component', [
            'galleries' => ModLocationGalerie::paginate(10),
        ])->layout('raadmin.layout.master');
    }

    public function edit($id)
    {
        $gallery = ModLocationGalerie::findOrFail($id);

        $this->fill([
            'locationId' => $gallery->location_id,
            'locationName' => $gallery->location_name,
            'imagePath' => $gallery->image_path,
            'imageCaption' => $gallery->image_caption ?? '', // Standardwert setzen
            'activity' => $gallery->activity ?? '',         // Standardwert setzen
            'description' => $gallery->description ?? '',   // Standardwert setzen
            'imageHash' => $gallery->image_hash,
            'imageType' => $gallery->image_type,
            'isPrimary' => $gallery->is_primary,
        ]);

        $this->editingId = $id;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $gallery = ModLocationGalerie::findOrFail($this->editingId);
            $gallery->update($this->getFormData());
        } else {
            ModLocationGalerie::create($this->getFormData());
        }

        $this->resetInputFields();
        session()->flash('message', 'Gallery entry saved successfully.');
    }

    public function delete($id)
    {
        ModLocationGalerie::findOrFail($id)->delete();
        session()->flash('message', 'Gallery entry deleted successfully.');
    }

    private function getFormData()
    {
        return [
            'location_id' => $this->locationId,
            'location_name' => $this->locationName,
            'image_path' => $this->imagePath,
            'image_caption' => $this->imageCaption,
            'activity' => $this->activity,
            'description' => $this->description,
            'image_hash' => $this->imageHash,
            'image_type' => $this->imageType,
            'is_primary' => $this->isPrimary,
        ];
    }

    private function resetInputFields()
    {
        $this->locationId = null;
        $this->locationName = null;
        $this->imagePath = null;
        $this->imageCaption = null;
        $this->activity = null;
        $this->description = null;
        $this->imageHash = null;
        $this->imageType = 'gallery';
        $this->isPrimary = 0;
        $this->editingId = null;
    }
}
