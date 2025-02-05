<?php

namespace App\Livewire\Backend\LocationFilters;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModLocationFilter;

class LocationFilterTable extends Component
{
    use WithPagination;

    public $search = ''; // Suchfeld
    public $perPage = 10; // Anzahl der Einträge pro Seite

    public function updatingSearch()
    {
        $this->resetPage(); // Zurück auf Seite 1, wenn sich die Suche ändert
    }

    public function delete($id)
    {
        $filter = ModLocationFilter::find($id);

        if ($filter) {
            $filter->delete();
            session()->flash('message', 'Eintrag wurde gelöscht.');
        }
    }

    public function render()
    {
        $filters = ModLocationFilter::with('location')
            ->where(function ($query) {
                $query->where('text_type', 'like', '%' . $this->search . '%')
                    ->orWhere('uschrift', 'like', '%' . $this->search . '%')
                    ->orWhere('text', 'like', '%' . $this->search . '%')
                    ->orWhereHas('location', function ($subQuery) {
                        $subQuery->where('title', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.backend.location-filters.location-filter-table', compact('filters'))
            ->layout('backend.layouts.livewiere-main');
    }
}
