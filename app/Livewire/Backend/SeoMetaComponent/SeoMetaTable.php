<?php

namespace App\Livewire\Backend\SeoMetaComponent;

use Livewire\Component;
use App\Models\ModSeoMeta;
use Livewire\WithPagination;

class SeoMetaTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public function render()
    {
        $seoMetas = ModSeoMeta::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('model_type', 'like', '%' . $this->search . '%')
                      ->orWhere('model_id', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

            return view('livewire.backend.seo-meta-component.seo-meta-table', [
                'seoMetas' => $seoMetas,
        ])->layout('backend.layouts.livewiere-main');
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

    public function edit($id)
    {
        return redirect()->route('verwaltung.seo-table-manager.seo.edit', ['id' => $id]);
    }
}
