<?php

namespace App\Livewire\Backend\RangeManager;

use Livewire\Component;
use App\Models\WwdeRange;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class RangeManagerComponent extends Component
{
    use WithPagination;

    public $rangeId, $sort, $range_to_show, $type;
    public $search = '';
    public $editMode = false;
    public $perPage = 10;
    public $selectedType = '';
    public $showForm = false;

    protected $rules = [
        'sort' => 'required|integer',
        'range_to_show' => 'required|string|max:255',
        'type' => 'required|in:Flight,Hotel,Rental,Travel',
    ];

    protected $listeners = ['confirmDelete'];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $range = WwdeRange::findOrFail($id);
        $this->rangeId = $range->id;
        $this->sort = $range->sort;
        $this->range_to_show = $range->range_to_show;
        $this->type = $range->type;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'sort' => $this->sort,
            'range_to_show' => $this->range_to_show,
            'type' => $this->type,
        ];

        if ($this->editMode) {
            $range = WwdeRange::findOrFail($this->rangeId);
            $range->update($data);
            $this->clearRangeCache($range);
            $this->dispatch('success', 'Range updated successfully.');
        } else {
            $range = WwdeRange::create($data);
            $this->clearRangeCache($range);
            $this->dispatch('success', 'Range created successfully.');
        }

        $this->resetInputFields();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $this->dispatch('confirmDelete', [
            'id' => $id,
            'message' => 'Are you sure you want to delete this range?',
        ]);
    }

    #[On('confirmDelete')]
    public function confirmDelete($id)
    {
        $range = WwdeRange::findOrFail($id);
        $range->delete();
        $this->clearRangeCache($range);
        $this->dispatch('success', 'Range deleted successfully.');
    }

    public function resetInputFields()
    {
        $this->rangeId = null;
        $this->sort = null;
        $this->range_to_show = null;
        $this->type = null;
        $this->editMode = false;
        $this->showForm = false;
    }

    private function clearRangeCache($range)
    {
        if ($range) {
            Cache::forget("range_{$range->id}");
            Cache::forget("ranges_{$range->type}");
            Cache::forget("ranges_list");
        }
    }

    public function render()
    {
        $ranges = WwdeRange::where(function ($query) {
                $query->where('range_to_show', 'like', "%{$this->search}%")
                      ->orWhere('type', 'like', "%{$this->search}%");
            })
            ->when($this->selectedType, function ($query) {
                $query->where('type', $this->selectedType);
            })
            ->orderBy('sort')
            ->paginate($this->perPage);

        // Debugging: Prüfen, ob Daten an View übergeben werden
        // Wenn du das ausprobieren willst, uncomment die nächste Zeile
        // dd($ranges);

        return view('livewire.backend.range-manager.range-manager-component', ['ranges' => $ranges])
            ->layout('raadmin.layout.master');
    }
}
