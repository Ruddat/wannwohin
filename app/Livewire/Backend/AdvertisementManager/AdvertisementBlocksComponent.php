<?php

namespace App\Livewire\Backend\AdvertisementManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModAdvertisementBlocks;
use App\Models\ModProviders;

class AdvertisementBlocksComponent extends Component
{
    use WithPagination;

    public $title;
    public $code;
    public $type = 'banner';
    public $position = []; // Jetzt ein Array für Multiselect
    public $advertisementId;
    public $providerId;
    public $isEditing = false;

    public $availablePositions = [
        '' => 'Keine feste Position',
        'header' => 'Header',
        'sidebar' => 'Seitenleiste',
        'footer' => 'Footer',
        'inline' => 'Zwischen Inhalten (z. B. Kacheln)',
        'above-experience' => 'Oberhalb Experience-Sektion',
        'below-experience' => 'Unterhalb Experience-Sektion',
        'above-destination' => 'Oberhalb Destination-Sektion',
        'below-destination' => 'Unterhalb Destination-Sektion',
        'above-destination-list' => 'Oberhalb Destination-Liste',
        'below-destination-list' => 'Unterhalb Destination-Liste',
        'above-locations' => 'Oberhalb Location-Sektion',
        'below-locations' => 'Unterhalb Location-Sektion',
        'kiwi-widget' => 'Kiwi-Widget',
        'above-compare' => 'Oberhalb Vergleichs-Sektion',
        'below-compare' => 'Unterhalb Vergleichs-Sektion',
        // Neue Positionen Filtersektion
        'above-filter' => 'Oberhalb Filter-Sektion',
        'below-filter' => 'Unterhalb Filter-Sektion',
        'inline-timeline' => 'Zwischen Timeline-Einträgen',
        'sidebar-ad' => 'Sidebar-Werbung',
    ];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'code' => 'required|string',
            'type' => 'required|in:banner,widget,script',
            'position' => 'nullable|array', // Array von Positionen
            'position.*' => 'in:' . implode(',', array_keys($this->availablePositions)), // Jede Position muss gültig sein
            'providerId' => 'required|exists:mod_providers,id',
        ];
    }

    public function render()
    {
        return view('livewire.backend.advertisement-manager.advertisement-blocks-component', [
            'advertisements' => ModAdvertisementBlocks::with('provider')->paginate(10),
            'providers' => ModProviders::where('is_active', true)->get(),
        ])->layout('backend.layouts.livewiere-main');
    }

    public function resetFields()
    {
        $this->title = '';
        $this->code = '';
        $this->type = 'banner';
        $this->position = []; // Leeres Array
        $this->providerId = null;
        $this->advertisementId = null;
        $this->isEditing = false;
    }

    public function updatedType($value)
    {
        $this->code = ''; // Code zurücksetzen bei Typänderung
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'content' => null, // Nicht mehr verwendet
            'link' => null,    // Nicht mehr verwendet
            'type' => $this->type,
            'script' => $this->code,
            'position' => $this->position, // Array speichern
            'provider_id' => $this->providerId,
            'is_active' => true,
        ];

        if ($this->isEditing) {
            ModAdvertisementBlocks::find($this->advertisementId)->update($data);
            session()->flash('message', 'Werbeblock aktualisiert!');
        } else {
            ModAdvertisementBlocks::create($data);
            session()->flash('message', 'Werbeblock erstellt!');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $advertisement = ModAdvertisementBlocks::findOrFail($id);
        $this->advertisementId = $advertisement->id;
        $this->title = $advertisement->title;
        $this->code = $advertisement->script;
        $this->type = $advertisement->type;
        $this->position = $advertisement->position ?? []; // Array oder leer
        $this->providerId = $advertisement->provider_id;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        ModAdvertisementBlocks::findOrFail($id)->delete();
        session()->flash('message', 'Werbeblock gelöscht!');
    }

    public function toggleActive($id)
    {
        $advertisement = ModAdvertisementBlocks::findOrFail($id);
        $newStatus = !$advertisement->is_active;
        $advertisement->update(['is_active' => $newStatus]);
        session()->flash('message', 'Status von Werbeblock #' . $advertisement->id . ' geändert auf ' . ($newStatus ? 'aktiv' : 'inaktiv') . '!');
    }
}
