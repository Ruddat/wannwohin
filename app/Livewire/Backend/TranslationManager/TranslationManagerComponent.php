<?php

namespace App\Livewire\Backend\TranslationManager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AutoTranslations;

class TranslationManagerComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedLocale = '';
    public $locales = [];

    public $editingTranslation = null;
    public $editKey = '';
    public $editLocale = '';
    public $editText = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        // Lade die verfügbaren Sprachen aus der Datenbank
        $this->locales = AutoTranslations::distinct()->pluck('locale')->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $this->editingTranslation = AutoTranslations::find($id);

        if ($this->editingTranslation) {
            $this->editKey = $this->editingTranslation->key;
            $this->editLocale = $this->editingTranslation->locale;
            $this->editText = $this->editingTranslation->text;
        }
    }

    public function cancelEdit()
    {
        $this->editingTranslation = null;
        $this->editKey = '';
        $this->editLocale = '';
        $this->editText = '';
    }

    public function updateTranslation()
    {
        if ($this->editingTranslation) {
            $this->validate([
                'editKey' => 'required|string|max:1024',
                'editLocale' => 'required|string|max:5',
                'editText' => 'required|string',
            ]);

            $this->editingTranslation->update([
                'key' => $this->editKey,
                'locale' => $this->editLocale,
                'text' => $this->editText,
            ]);

            $this->cancelEdit();

            session()->flash('message', 'Übersetzung erfolgreich aktualisiert.');
        }
    }

    public function delete($id)
    {
        $translation = AutoTranslations::find($id);

        if ($translation) {
            $translation->delete();

            session()->flash('message', 'Übersetzung erfolgreich gelöscht.');
        }
    }

    public function render()
    {
        $translations = AutoTranslations::query()
            ->when($this->search, function ($query) {
                $query->where('key', 'like', "%{$this->search}%")
                      ->orWhere('text', 'like', "%{$this->search}%");
            })
            ->when($this->selectedLocale, function ($query) {
                $query->where('locale', $this->selectedLocale);
            })

            ->orderBy('key')
            ->paginate($this->perPage);

        return view('livewire.backend.translation-manager.translation-manager-component', [
            'translations' => $translations,
        ])->layout('backend.layouts.livewiere-main');
    }
}
