<?php

namespace App\Livewire\Backend\AdvertisementManager;

use Livewire\Component;
use App\Models\ModProviders;

class ProvidersComponent extends Component
{
    public $name, $email, $phone, $website, $description, $contact_person, $providerId, $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:mod_providers,name',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'website' => 'nullable|url',
        'description' => 'nullable|string',
        'contact_person' => 'nullable|string|max:255',
    ];

    public function render()
    {
        return view('livewire.backend.advertisement-manager.providers-component', [
            'providers' => ModProviders::paginate(10),
        ])->layout('raadmin.layout.master');
    }

    public function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->website = '';
        $this->description = '';
        $this->contact_person = '';
        $this->providerId = null;
        $this->isEditing = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'description' => $this->description,
            'contact_person' => $this->contact_person,
        ];

        if ($this->isEditing) {
            ModProviders::find($this->providerId)->update($data);
            session()->flash('message', 'Anbieter aktualisiert!');
        } else {
            ModProviders::create($data);
            session()->flash('message', 'Anbieter erstellt!');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $provider = ModProviders::findOrFail($id);
        $this->providerId = $provider->id;
        $this->name = $provider->name;
        $this->email = $provider->email;
        $this->phone = $provider->phone;
        $this->website = $provider->website;
        $this->description = $provider->description;
        $this->contact_person = $provider->contact_person;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        ModProviders::findOrFail($id)->delete();
        session()->flash('message', 'Anbieter gelöscht!');
    }
}
