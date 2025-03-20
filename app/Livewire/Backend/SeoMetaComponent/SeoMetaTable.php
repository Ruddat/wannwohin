<?php

namespace App\Livewire\Backend\SeoMetaComponent;

use Livewire\Component;
use App\Models\ModSeoMeta;
use App\Models\WwdeLocation;
use App\Models\WwdeCountry;
use App\Models\WwdeContinent;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class SeoMetaTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $deleteId = null;

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

        $seoMetas->getCollection()->transform(function ($seoMeta) {
            $seoMeta->model_name = $this->getModelName($seoMeta->model_type, $seoMeta->model_id);
            return $seoMeta;
        });

        return view('livewire.backend.seo-meta-component.seo-meta-table', [
            'seoMetas' => $seoMetas,
        ])->layout('raadmin.layout.master');
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

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $seoMeta = ModSeoMeta::find($this->deleteId);
            if ($seoMeta) {
                $cacheKey = "seo_{$seoMeta->model_type}_{$seoMeta->model_id}";
                $seoMeta->delete();
                Cache::forget($cacheKey);
                session()->flash('message', 'SEO-Eintrag erfolgreich gelöscht.');
            }
            $this->deleteId = null;
        }
    }

    public function togglePreventOverride($id)
    {
        $seoMeta = ModSeoMeta::find($id);
        if ($seoMeta) {
            $seoMeta->prevent_override = !$seoMeta->prevent_override;
            $seoMeta->save();
            $cacheKey = "seo_{$seoMeta->model_type}_{$seoMeta->model_id}";
            Cache::forget($cacheKey);
            session()->flash('message', "Überschreiben für ID {$id} wurde " . ($seoMeta->prevent_override ? 'gesperrt' : 'erlaubt') . '.');
        }
    }

    // Hook zum Zurücksetzen der Pagination bei Änderungen an $search oder $perPage
    public function updating($name, $value)
    {
        if ($name === 'search' || $name === 'perPage') {
            $this->resetPage();
        }
    }

    protected function getModelName($modelType, $modelId)
    {
        switch ($modelType) {
            case WwdeLocation::class:
                $model = WwdeLocation::find($modelId);
                return $model ? "{$model->title} ({$modelId})" : "Location ({$modelId})";
            case WwdeCountry::class:
                $model = WwdeCountry::find($modelId);
                return $model ? "{$model->title} ({$modelId})" : "Country ({$modelId})";
            case WwdeContinent::class:
                $model = WwdeContinent::find($modelId);
                return $model ? "{$model->title} ({$modelId})" : "Continent ({$modelId})";
            default:
                return "Unknown ({$modelId})";
        }
    }
}
