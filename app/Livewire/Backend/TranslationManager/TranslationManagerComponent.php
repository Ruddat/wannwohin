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

    public $confirmingDeleteId = null;

    protected $queryString = [
        'search'          => ['except' => ''],
        'perPage'         => ['except' => 10],
        'selectedLocale'  => ['except' => ''],
    ];

    public function mount()
    {
        // Verfügbare Sprachen sortiert laden
        $this->locales = AutoTranslations::query()
            ->select('locale')
            ->distinct()
            ->orderBy('locale')
            ->pluck('locale')
            ->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedLocale()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $this->editingTranslation = AutoTranslations::find($id);

        if ($this->editingTranslation) {
            $this->editKey    = $this->editingTranslation->key;
            $this->editLocale = $this->editingTranslation->locale;
            $this->editText   = $this->editingTranslation->text;
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
        if (! $this->editingTranslation) {
            return;
        }

        $this->validate([
            'editKey'    => 'required|string|max:1024',
            'editLocale' => 'required|string|max:5',
            'editText'   => 'required|string|max:5000',
        ]);

        // Prüfen, ob der Key/Locale-Kombi bereits existiert (Duplikate verhindern)
        $exists = AutoTranslations::query()
            ->where('key', $this->editKey)
            ->where('locale', $this->editLocale)
            ->where('id', '!=', $this->editingTranslation->id)
            ->exists();

        if ($exists) {
            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Dieser Schlüssel existiert bereits in dieser Sprache.'
            );

            return;
        }

        $this->editingTranslation->update([
            'key'    => $this->editKey,
            'locale' => $this->editLocale,
            'text'   => $this->editText,
        ]);

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Übersetzung erfolgreich aktualisiert.'
        );

        $this->cancelEdit();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

public function deleteConfirmed()
{
    if (! $this->confirmingDeleteId) {
        return;
    }

    $translation = AutoTranslations::find($this->confirmingDeleteId);

    if ($translation) {
        $translation->delete();

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Übersetzung erfolgreich gelöscht.'
        );
    }

    $this->confirmingDeleteId = null;

    // FIX: Livewire v3 Pagination check
    $currentPage = $this->getPage();

    if ($currentPage > 1 && $this->currentPageIsEmpty()) {
        $this->previousPage();
    }
}


    protected function currentPageIsEmpty(): bool
    {
        $count = AutoTranslations::query()
            ->when($this->search, function ($query) {
                $search = $this->search;

                $query->where(function ($q) use ($search) {
                    $q->where('key', 'like', "%{$search}%")
                      ->orWhere('text', 'like', "%{$search}%");
                });
            })
            ->when($this->selectedLocale, function ($query) {
                $query->where('locale', $this->selectedLocale);
            })
            ->count();

        $maxPage = (int) ceil(max($count, 1) / $this->perPage);

        return $this->page > $maxPage;
    }

    public function render()
    {
        $translations = AutoTranslations::query()
            ->when($this->search, function ($query) {
                $search = $this->search;

                // Gruppierte Suche, damit OR nicht andere Filter sprengt
                $query->where(function ($q) use ($search) {
                    $q->where('key', 'like', "%{$search}%")
                      ->orWhere('text', 'like', "%{$search}%");
                });
            })
            ->when($this->selectedLocale, function ($query) {
                $query->where('locale', $this->selectedLocale);
            })
            ->orderBy('key')
            ->paginate($this->perPage);

        return view('livewire.backend.translation-manager.translation-manager-component', [
            'translations' => $translations,
        ])->layout('raadmin.layout.master');
    }
}
