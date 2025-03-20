<?php
// ============================
// LIVEWIRE COMPONENT (mit Bearbeiten-Funktion)
// ============================

namespace App\Livewire\Backend\QuickFilterManager;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ModQuickFilterItem;

class QuickFilterComponent extends Component
{
    use WithFileUploads;

    public $title;
    public $title_text;
    public $content;
    public $thumbnail;
    public $panorama;
    public $image;
    public $filter_months = [];
    public $slug;
    public $status = 1;
    public $editId = null;
    public $existingThumbnail = null;
    public $existingPanorama = null;
    public $existingImage = null;
    public $showForm = false; // Neue Eigenschaft, um das Formular zu steuern
    public $orderedItems = [];

    protected $listeners = ['updateOrder'];


    public $slugOptions = [
        'strand-reise' => 'Strand & Meer',
        'staedte-reise' => 'Städtereise',
        'sport-reise' => 'Sporturlaub',
        'insel-reise' => 'Inselurlaub',
        'kultur-reise' => 'Kultur & Geschichte',
        'natur-reise' => 'Natururlaub',
        'wassersport-reise' => 'Wassersport',
        'wintersport-reise' => 'Wintersport',
        'mountainsport-reise' => 'Bergsport',
        'biking-reise' => 'Radreisen',
        'fishing-reise' => 'Angeln',
        'amusement-park-reise' => 'Freizeitpark',
        'water-park-reise' => 'Wasserpark',
        'animal-park-reise' => 'Tierpark',
    ];


    protected $rules = [
        'title'       => 'required|string|max:255',
        'title_text'  => 'nullable|string',
        'content'     => 'nullable|string',
        'thumbnail'   => 'nullable|image|max:1024',
        'panorama'    => 'nullable|image|max:2048',
        'image'       => 'nullable|image|max:4096',
        'filter_months'         => 'nullable|array',
        'filter_months.*'       => 'integer|min:1|max:12',
        'status'      => 'required|boolean',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->edit($id);
        }

        // Alle bereits verwendeten Slugs abrufen
        $usedSlugs = ModQuickFilterItem::pluck('slug')->toArray();

        // Wenn ein Item bearbeitet wird, entferne seinen Slug aus der "verwendeten" Liste
        if ($this->editId) {
            $currentItem = ModQuickFilterItem::find($this->editId);
            if ($currentItem) {
                $usedSlugs = array_diff($usedSlugs, [$currentItem->slug]);
            }
        }

        // Nur Slugs anzeigen, die noch nicht verwendet wurden, aber den aktuellen Slug im Bearbeitungsmodus behalten
        $this->slugOptions = array_filter($this->slugOptions, function ($value, $slugKey) use ($usedSlugs) {
            return !in_array($slugKey, $usedSlugs);
        }, ARRAY_FILTER_USE_BOTH);

        // Falls alle Slugs verbraucht sind (außer der aktuell bearbeitete), $showForm deaktivieren
        if (empty($this->slugOptions) && !$this->editId) {
            $this->showForm = false;
            session()->flash('error', 'Alle Slug-Kategorien sind bereits vergeben. Es können keine neuen Einträge erstellt werden.');
        }
    }



    public function save()
    {


        // Falls alle Slugs vergeben sind, abbrechen
        if (empty($this->slugOptions)) {
            session()->flash('error', 'Alle verfügbaren Kategorien wurden bereits verwendet.');
            return;
        }

        $this->validate();

        if ($this->editId) {
            $galleryItem = ModQuickFilterItem::findOrFail($this->editId);
            $galleryItem->sort_order = ModQuickFilterItem::max('sort_order') + 1; // Nächste Sortiernummer setzen
        } else {
            $galleryItem = new ModQuickFilterItem();
            //$galleryItem->slug = Str::slug($this->title); // Slug automatisch setzen
        }

        $galleryItem->title = $this->title;
        $galleryItem->title_text = $this->title_text;
        $galleryItem->content = $this->content;
        $galleryItem->slug = $this->slug; // Slug wird jetzt aus Dropdown gesetzt


        if ($this->thumbnail) {
            $galleryItem->thumbnail = $this->thumbnail->store('thumbnails', 'public');
        }
        if ($this->panorama) {
            $galleryItem->panorama = $this->panorama->store('panoramas', 'public');
        }
        if ($this->image) {
            $galleryItem->image = $this->image->store('images', 'public');
        }

        $galleryItem->filter_months = $this->filter_months;
        $galleryItem->status = $this->status;
        $galleryItem->save();

        session()->flash('message', 'QuickFilter-Item erfolgreich gespeichert!');
        // **Slug-Optionen nach dem Speichern aktualisieren**
        $this->updateSlugOptions();

        $this->resetFields();
    }

    public function edit($id)
    {
        $galleryItem = ModQuickFilterItem::findOrFail($id);
        $this->editId = $galleryItem->id;
        $this->title = $galleryItem->title;
        $this->title_text = $galleryItem->title_text;
        $this->content = $galleryItem->content;
        $this->slug = $galleryItem->slug;
        $this->filter_months = $galleryItem->filter_months;
        $this->status = $galleryItem->status;

        // Bestehende Bilder für die Vorschau setzen
        $this->existingThumbnail = $galleryItem->thumbnail;
        $this->existingPanorama = $galleryItem->panorama;
        $this->existingImage = $galleryItem->image;

        // Formular anzeigen
        $this->showForm = true;


        // ✅ Slug-Liste aktualisieren und den aktuellen Slug erlauben
        $usedSlugs = ModQuickFilterItem::pluck('slug')->toArray();

        // Debugging: Zeige alle verwendeten Slugs vor der Korrektur
        \Log::info("Alle verwendeten Slugs (vor array_diff):", $usedSlugs);

        // Entferne den aktuellen Slug, wenn er existiert (damit er beim Bearbeiten bleibt)
        $usedSlugs = array_values(array_diff($usedSlugs, [$this->slug]));

        // Debugging: Zeige die Liste nach dem Entfernen des aktuellen Slugs
        \Log::info("Alle verwendeten Slugs (nach array_diff):", $usedSlugs);

        // Filtere Slug-Optionen und erhalte nur die nicht verwendeten Slugs + den aktuellen
        $this->slugOptions = array_filter($this->slugOptions, function ($value, $slugKey) use ($usedSlugs) {
            return !in_array($slugKey, $usedSlugs);
        }, ARRAY_FILTER_USE_BOTH);

        // Debugging: Zeige die finalen verfügbaren Slug-Optionen
        \Log::info("Verfügbare Slug-Optionen:", $this->slugOptions);

        // Event auslösen, um den Editor-Inhalt zu aktualisieren
        $this->dispatch('contentUpdated', ['content' => $this->content]);
    }

    public function delete($id)
    {
        ModQuickFilterItem::findOrFail($id)->delete();
        // **Slug-Optionen nach dem Löschen aktualisieren**
        $this->updateSlugOptions();
        session()->flash('message', 'QuickFilter-Item wurde gelöscht!');
    }

    public function resetFields()
    {
        $this->editId = null;
        $this->title = '';
        $this->title_text = '';
        $this->content = '';
        $this->thumbnail = null;
        $this->panorama = null;
        $this->image = null;
        $this->filter_months = [];
        $this->slug = null;
        $this->status = 1;
        $this->showForm = false; // Formular ausblenden

        // ✅ Bildvorschau zurücksetzen
        $this->existingThumbnail = null;
        $this->existingPanorama = null;
        $this->existingImage = null;

        // **Slug-Optionen nach dem Löschen aktualisieren**
        $this->updateSlugOptions();
    }

    public function updateOrder($items)
    {
        // Sicherstellen, dass $items ein Array ist
        if (!is_array($items)) {
            $items = json_decode($items, true); // Falls es JSON ist, umwandeln
        }

        if (!is_array($items)) {
            \Log::error("updateOrder Fehler: items ist kein Array!", ['items' => $items]);
            return;
        }

        \Log::info('updateOrder wurde aufgerufen!', ['items' => $items]);

        foreach ($items as $index => $id) {
            \Log::info("Update Item ID: {$id} auf Sortierung {$index}");
            ModQuickFilterItem::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->orderedItems = ModQuickFilterItem::orderBy('sort_order')->get();
    }


    public function updateSlugOptions()
    {
        // Alle verwendeten Slugs abrufen
        $usedSlugs = ModQuickFilterItem::pluck('slug')->toArray();
//dd($usedSlugs);
        // Falls im Bearbeitungsmodus, den aktuellen Slug aus der Liste entfernen
        if ($this->editId && in_array($this->slug, $usedSlugs)) {
            $usedSlugs = array_diff($usedSlugs, [$this->slug]);
        }

        // Nur nicht verwendete Slugs anzeigen
        $this->slugOptions = array_filter($this->slugOptions, function ($value, $slugKey) use ($usedSlugs) {
            return !in_array($slugKey, $usedSlugs);
        }, ARRAY_FILTER_USE_BOTH);
    //dd($this->slugOptions);

    }

    public function toggleStatus($id)
    {
        $item = ModQuickFilterItem::findOrFail($id);
        $item->status = !$item->status;
        $item->save();
    }

    public function render()
    {
        $this->orderedItems = ModQuickFilterItem::orderBy('sort_order')->get();

        $this->updateSlugOptions();

        return view('livewire.backend.quick-filter-manager.quick-filter-component', [
            'galleryItems' => $this->orderedItems
        ])->layout('raadmin.layout.master');
    }


}
