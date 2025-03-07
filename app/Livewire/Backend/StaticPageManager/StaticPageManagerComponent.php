<?php

namespace App\Livewire\Backend\StaticPageManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModStaticPage;

class StaticPageManagerComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'slug';
    public $sortDirection = 'asc';
    public $editId = null;
    public $title;
    public $body;

    protected $rules = [
        'title' => 'required|string|max:255',
        'body' => 'required|string',
    ];

    public function render()
    {
        $staticPages = ModStaticPage::query()
            ->when($this->search, function ($query) {
                $query->where('slug', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%')
                      ->orWhere('body', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.backend.static-page-manager.static-page-manager-component', [
            'staticPages' => $staticPages,
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

    public function edit($slug)
    {
        $page = ModStaticPage::findOrFail($slug);
        $this->editId = $slug;
        $this->title = $page->title;
        $this->body = $page->body;
    }

    public function update()
    {
        $this->validate();

        $page = ModStaticPage::findOrFail($this->editId);
        $page->update([
            'title' => $this->title,
            'body' => $this->body,
        ]);

        $this->reset(['editId', 'title', 'body']);
        session()->flash('message', 'Seite erfolgreich aktualisiert.');
    }

    public function cancelEdit()
    {
        $this->reset(['editId', 'title', 'body']);
    }

    public function updating($name, $value)
    {
        if ($name === 'search' || $name === 'perPage') {
            $this->resetPage();
        }
    }
}
