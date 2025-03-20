<?php

namespace App\Livewire\Backend\ElectricManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModElectricStandards;

class ElectricManagerComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCountry = '';
    public $perPage = 10;

    public $showForm = false; // Steuerung für Formulare
    public $editMode = false;
    public $electricStandardId;
    public $country_name, $country_code, $power, $info;
    public $typ_a, $typ_b, $typ_c, $typ_d, $typ_e, $typ_f, $typ_g, $typ_h, $typ_i, $typ_j, $typ_k, $typ_l, $typ_m, $typ_n;

    protected $rules = [
        'country_name' => 'required|string|max:255',
        'country_code' => 'nullable|string|max:3',
        'power' => 'nullable|string|max:50',
        'info' => 'nullable|string',
        'typ_a' => 'boolean',
        'typ_b' => 'boolean',
        'typ_c' => 'boolean',
        'typ_d' => 'boolean',
        'typ_e' => 'boolean',
        'typ_f' => 'boolean',
        'typ_g' => 'boolean',
        'typ_h' => 'boolean',
        'typ_i' => 'boolean',
        'typ_j' => 'boolean',
        'typ_k' => 'boolean',
        'typ_l' => 'boolean',
        'typ_m' => 'boolean',
        'typ_n' => 'boolean',
    ];

    public function resetInputFields()
    {
        $this->reset([
            'country_name', 'country_code', 'power', 'info',
            'typ_a', 'typ_b', 'typ_c', 'typ_d', 'typ_e', 'typ_f',
            'typ_g', 'typ_h', 'typ_i', 'typ_j', 'typ_k', 'typ_l',
            'typ_m', 'typ_n',
        ]);

        $this->editMode = false;
        $this->showForm = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
        $this->showForm = true;
    }

    public function store()
    {
        $this->validate();

        ModElectricStandards::create([
            'country_name' => $this->country_name,
            'country_code' => $this->country_code,
            'power' => $this->power,
            'info' => $this->info,
            'typ_a' => $this->typ_a,
            'typ_b' => $this->typ_b,
            'typ_c' => $this->typ_c,
            'typ_d' => $this->typ_d,
            'typ_e' => $this->typ_e,
            'typ_f' => $this->typ_f,
            'typ_g' => $this->typ_g,
            'typ_h' => $this->typ_h,
            'typ_i' => $this->typ_i,
            'typ_j' => $this->typ_j,
            'typ_k' => $this->typ_k,
            'typ_l' => $this->typ_l,
            'typ_m' => $this->typ_m,
            'typ_n' => $this->typ_n,
        ]);

        session()->flash('message', 'Electric standard successfully added.');
        $this->resetInputFields();
        $this->dispatch('refreshTable');
    }

    public function edit($id)
    {
        $standard = ModElectricStandards::findOrFail($id);
        $this->electricStandardId = $id;
        $this->country_name = $standard->country_name;
        $this->country_code = $standard->country_code;
        $this->power = $standard->power;
        $this->info = $standard->info;

        $this->typ_a = $standard->typ_a;
        $this->typ_b = $standard->typ_b;
        $this->typ_c = $standard->typ_c;
        $this->typ_d = $standard->typ_d;
        $this->typ_e = $standard->typ_e;
        $this->typ_f = $standard->typ_f;
        $this->typ_g = $standard->typ_g;
        $this->typ_h = $standard->typ_h;
        $this->typ_i = $standard->typ_i;
        $this->typ_j = $standard->typ_j;
        $this->typ_k = $standard->typ_k;
        $this->typ_l = $standard->typ_l;
        $this->typ_m = $standard->typ_m;
        $this->typ_n = $standard->typ_n;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $standard = ModElectricStandards::findOrFail($this->electricStandardId);
        $standard->update([
            'country_name' => $this->country_name,
            'country_code' => $this->country_code,
            'power' => $this->power,
            'info' => $this->info,
            'typ_a' => $this->typ_a,
            'typ_b' => $this->typ_b,
            'typ_c' => $this->typ_c,
            'typ_d' => $this->typ_d,
            'typ_e' => $this->typ_e,
            'typ_f' => $this->typ_f,
            'typ_g' => $this->typ_g,
            'typ_h' => $this->typ_h,
            'typ_i' => $this->typ_i,
            'typ_j' => $this->typ_j,
            'typ_k' => $this->typ_k,
            'typ_l' => $this->typ_l,
            'typ_m' => $this->typ_m,
            'typ_n' => $this->typ_n,
        ]);

        session()->flash('message', 'Electric standard successfully updated.');
        $this->resetInputFields();
        $this->dispatch('refreshTable');
    }

    public function delete($id)
    {
        ModElectricStandards::findOrFail($id)->delete();
        session()->flash('message', 'Electric standard successfully deleted.');
        $this->dispatch('refreshTable');
    }

    public function render()
    {
        $standards = ModElectricStandards::where('country_name', 'like', "%{$this->search}%")
            ->orWhere('country_code', 'like', "%{$this->search}%")
            ->paginate($this->perPage);

        return view('livewire.backend.electric-manager.electric-manager-component', [
            'standards' => $standards,
        ])->layout('raadmin.layout.master');
    }
}
