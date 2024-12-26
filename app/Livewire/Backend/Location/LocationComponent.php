<?php

namespace App\Livewire\Backend\Location;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\WwdeLocation;
use Livewire\WithPagination;

class LocationComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $perPage = 10; // Standard-Anzahl pro Seite
    public $locationIdToDelete;

    protected $queryString = ['search', 'filterStatus', 'perPage'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage(); // Reset pagination, wenn der Filter geändert wird
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $location = WwdeLocation::find($id);

        if ($location) {
            $location->status = match ($location->status) {
                'active' => 'pending',
                'pending' => 'inactive',
                'inactive' => 'active',
            };
            $location->save();

            session()->flash('success', 'Status updated successfully!');
        }
    }


    public function confirmDelete($id)
    {
        $this->locationIdToDelete = $id;

        // Trigger SweetAlert confirmation
        $this->locationIdToDelete = $id;

        $this->dispatch('delete-prompt',[
            'title'=>'Are you sure?',
            'text'=>'This record will be permanently deleted!',
            'icon'=>'warning',
            'showCancelButton' => true,
            'confirmEvent' => 'goOn-Delete',
        ]);

    }

    #[On('goOn-Delete')]
    public function deleteConfirmed()
    {

        if ($this->locationIdToDelete) {
            WwdeLocation::findOrFail($this->locationIdToDelete)->delete();
            $this->locationIdToDelete = null;

            $this->dispatch('swal',[
                'title'=>'Success!',
                'text'=>'Data deletd succesfully!',
                'icon'=>'success',
              ]);

            session()->flash('success', 'Location deleted successfully.');
        }
    }

    public function render()
    {
        $locations = WwdeLocation::query()
            ->when($this->search, fn ($query) => $query->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('alias', 'like', '%' . $this->search . '%'))
            ->when($this->filterStatus, fn ($query) => $query->where('status', $this->filterStatus))
            ->when($this->perPage !== 'all', fn ($query) => $query->paginate($this->perPage), fn ($query) => $query->get());

        return view('livewire.backend.location.location-component', compact('locations'));
    }
}
