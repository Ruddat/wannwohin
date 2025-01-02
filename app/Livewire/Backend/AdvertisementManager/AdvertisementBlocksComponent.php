<?php

namespace App\Livewire\Backend\AdvertisementManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModAdvertisementBlocks;

class AdvertisementBlocksComponent extends Component
{
    use WithPagination;

    public $title;
    public $content;
    public $link;
    public $advertisementId;
    public $providerId; // Added to link advertisements to providers
    public $isEditing = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'nullable|string',
        'link' => 'nullable|url',
        'providerId' => 'required|exists:providers,id', // Validation for provider ID
    ];

    public function render()
    {
        return view('livewire.backend.advertisement-manager.advertisement-blocks-component', [
            'advertisements' => ModAdvertisementBlocks::with('provider')->paginate(10)
        ])->layout('backend.layouts.livewiere-main');
    }

    public function resetFields()
    {
        $this->title = '';
        $this->content = '';
        $this->link = '';
        $this->providerId = null;
        $this->advertisementId = null;
        $this->isEditing = false;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $advertisement = ModAdvertisementBlocks::find($this->advertisementId);
            $advertisement->update([
                'title' => $this->title,
                'content' => $this->content,
                'link' => $this->link,
                'provider_id' => $this->providerId,
            ]);
        } else {
            ModAdvertisementBlocks::create([
                'title' => $this->title,
                'content' => $this->content,
                'link' => $this->link,
                'provider_id' => $this->providerId,
            ]);
        }

        $this->resetFields();
        session()->flash('message', $this->isEditing ? 'Werbeblock aktualisiert!' : 'Werbeblock erstellt!');
    }

    public function edit($id)
    {
        $advertisement = ModAdvertisementBlocks::findOrFail($id);

        $this->advertisementId = $advertisement->id;
        $this->title = $advertisement->title;
        $this->content = $advertisement->content;
        $this->link = $advertisement->link;
        $this->providerId = $advertisement->provider_id;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        ModAdvertisementBlocks::findOrFail($id)->delete();
        session()->flash('message', 'Werbeblock gelöscht!');
    }
}
