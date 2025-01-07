<?php

namespace App\Livewire\Backend\ParkListManager;

use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\AmusementParks;

class ParkFormComponent extends Component
{
    public $parkId; // Wird für das Bearbeiten eines Parks benötigt
    public $name, $country, $location, $latitude, $longitude, $open_from, $closed_from;

    protected $rules = [
        'name' => 'required|string',
        'country' => 'required|string',
        'location' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'open_from' => 'nullable|date',
        'closed_from' => 'nullable|date|after:open_from',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $park = AmusementParks::find($id);
            if ($park) {
                $this->fill($park->toArray());
                $this->parkId = $park->id;
            }
        }
    }

    public function save()
    {
        $this->validate();

        // Generiere external_id basierend auf dem Namen
        $externalId = Str::slug($this->name, '');

        if ($this->parkId) {
            // Bearbeiten eines bestehenden Parks
            $park = AmusementParks::findOrFail($this->parkId);
            $park->update([
                'name' => $this->name,
                'country' => $this->country,
                'location' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'open_from' => $this->open_from,
                'closed_from' => $this->closed_from,
                'external_id' => $externalId,
            ]);
            session()->flash('success', 'Park erfolgreich aktualisiert.');
        } else {
            // Erstellen eines neuen Parks
            AmusementParks::create([
                'name' => $this->name,
                'country' => $this->country,
                'location' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'open_from' => $this->open_from,
                'closed_from' => $this->closed_from,
                'external_id' => $externalId,
            ]);
            session()->flash('success', 'Park erfolgreich gespeichert.');
        }

        return redirect()->route('verwaltung.park-manager.index');
    }

    public function render()
    {
        return view('livewire.backend.park-list-manager.park-form-component')
            ->layout('backend.layouts.livewiere-main');
    }
}
