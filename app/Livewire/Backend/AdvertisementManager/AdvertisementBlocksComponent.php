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
    public $code; // Enthält den gesamten Code inkl. Link für Banner
    public $type = 'banner';
    public $position;
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
    ];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'code' => 'required|string',
            'type' => 'required|in:banner,widget,script',
            'position' => 'nullable|in:' . implode(',', array_keys($this->availablePositions)),
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
        $this->position = '';
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
            'script' => $this->code, // Alles inkl. Link in script
            'position' => $this->position,
            'provider_id' => $this->providerId,
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
        $this->code = $advertisement->script; // Alles aus script
        $this->type = $advertisement->type;
        $this->position = $advertisement->position;
        $this->providerId = $advertisement->provider_id;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        ModAdvertisementBlocks::findOrFail($id)->delete();
        session()->flash('message', 'Werbeblock gelöscht!');
    }
}
