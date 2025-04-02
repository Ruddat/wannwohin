<?php

namespace App\Livewire\Backend\ContinentManager;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Helpers\ImageHelper;
use Livewire\WithPagination;
use App\Models\WwdeContinent;
use Livewire\WithFileUploads;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ContinentManagerComponent extends Component
{
    use WithFileUploads, WithPagination;

    public $continentId, $title, $alias, $iso2, $iso3, $area_km, $population, $no_countries, $no_climate_tables, $continent_text, $continent_header_text;
    public $image1_path, $image2_path, $image3_path;
    public $custom_images = '';
    public $status = 'active';
    public $search = '';
    public $editMode = false;

    public $searchKeyword = '';
    public $pixabayImages = [];
    protected $listeners = ['confirmDelete'];
    public $perPage = 10;

    public $fact_card_image;

    protected $rules = [
        'title' => 'required|string|max:120',
        'alias' => 'required|string|max:120',
        'iso2' => 'nullable|string|size:2',
        'iso3' => 'nullable|string|size:3',
        'area_km' => 'nullable|integer',
        'population' => 'nullable|integer',
        'no_countries' => 'nullable|integer',
        'no_climate_tables' => 'nullable|integer',
        'continent_text' => 'nullable|string',
        'continent_header_text' => 'nullable|string',
        'image1_path' => 'nullable|image|max:2048',
        'image2_path' => 'nullable|image|max:2048',
        'image3_path' => 'nullable|image|max:2048',
        'custom_images' => 'boolean',
        'fact_card_image' => 'nullable|image|max:2048',
        'status' => 'required|in:active,pending,inactive',
    ];

    public function mount()
    {
        $this->loadContinents();
    }

    public function loadContinents()
    {
        $this->continents = WwdeContinent::where('title', 'like', "%{$this->search}%")
            ->orWhere('alias', 'like', "%{$this->search}%")
            ->orderBy('title')
            ->paginate(10);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
    }

    public function edit($id)
    {
        $continent = WwdeContinent::findOrFail($id);
        $this->fill($continent->toArray());
        $this->continentId = $continent->id;
        $this->continent_text = $continent->continent_text;
        $this->continent_header_text = $continent->continent_header_text;
        $this->editMode = true;
    }

    public function save()
    {
        $data = $this->prepareData();

        if ($this->editMode) {
            $continent = WwdeContinent::findOrFail($this->continentId);
            $continent->update($data);

            // Cache löschen bei Update
            $this->clearContinentCache($continent);
        } else {
            $continent = WwdeContinent::create($data);
            $this->continentId = $continent->id;

            // Cache löschen bei Neuerstellung
            $this->clearContinentCache($continent);
        }

        $this->resetInputFields();
        $this->dispatch('success', 'Continent saved successfully.');
    }

    public function delete($id)
    {
        $continent = WwdeContinent::findOrFail($id);

        if ($continent->no_countries > 0) {
            $this->dispatch('error', 'This continent cannot be deleted because it has associated countries.');
            return;
        }

        $this->dispatch('confirmDelete', [
            'id' => $id,
            'message' => 'Are you sure you want to delete this continent?',
        ]);
    }

    #[On('confirmDelete')]
    public function confirmDelete($id)
    {
        $continent = WwdeContinent::findOrFail($id);

        for ($i = 1; $i <= 3; $i++) {
            $imageField = "image{$i}_path";
            if ($continent->$imageField && Storage::exists($continent->$imageField)) {
                Storage::delete($continent->$imageField);
            }
        }

        $continent->delete();

        // Cache löschen bei Löschung
        $this->clearContinentCache($continent);

        $this->dispatch('success', 'Continent deleted successfully.');
    }

    public function uploadImages()
    {
        if (!Storage::exists('uploads/images/continents')) {
            Storage::makeDirectory('uploads/images/continents');
        }

        $images = [];
        for ($i = 1; $i <= 3; $i++) {
            $imageField = "image{$i}_path";
            if ($this->$imageField) {
                $path = $this->$imageField->store('uploads/images/continents');
                $images[$imageField] = $path;
            }
        }

        return $images;
    }

    public function fetchImagesFromPixabay()
    {
        $keyword = $this->searchKeyword ?: $this->title;

        $response = Http::get("https://pixabay.com/api/", [
            'key' => env('PIXABAY_API_KEY'),
            'q' => $keyword,
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'category' => 'nature',
            'per_page' => 10,
        ]);

        if ($response->ok() && isset($response['hits'])) {
            $this->pixabayImages = collect($response['hits'])->map(function ($image) {
                return [
                    'previewURL' => $image['previewURL'],
                    'largeImageURL' => $image['largeImageURL'],
                ];
            })->take(10);
        } else {
            $this->dispatch('error', 'No images found on Pixabay.');
        }
    }

    public function selectPixabayImage($index)
    {
        if (isset($this->pixabayImages[$index])) {
            $imageData = $this->pixabayImages[$index];
            $path = "uploads/images/continents/" . uniqid() . ".jpg";
            Storage::put($path, file_get_contents($imageData['largeImageURL']));

            if (!$this->image1_path) {
                $this->image1_path = $path;
            } elseif (!$this->image2_path) {
                $this->image2_path = $path;
            } elseif (!$this->image3_path) {
                $this->image3_path = $path;
            } else {
                $this->dispatch('error', 'You can only select up to 3 images.');
            }
        }
    }

    public function toggleStatus($id)
    {
        $continent = WwdeContinent::findOrFail($id);
        $newStatus = match ($continent->status) {
            'active' => 'pending',
            'pending' => 'inactive',
            default => 'active',
        };
        $continent->update(['status' => $newStatus]);

        // Cache löschen bei Statusänderung
        $this->clearContinentCache($continent);

        $this->loadContinents();
        $this->dispatch('success', 'Status updated successfully.');
        session()->flash('status', 'Status updated successfully. Status: ' . $newStatus);
    }

    public function prepareData()
    {
        $data = $this->validate([
            'title' => 'required|string|max:120',
            'alias' => 'required|string|max:120',
            'iso2' => 'nullable|string|size:2',
            'iso3' => 'nullable|string|size:3',
            'area_km' => 'nullable|integer',
            'population' => 'nullable|integer',
            'no_countries' => 'nullable|integer',
            'no_climate_tables' => 'nullable|integer',
            'continent_header_text' => 'nullable|string',
            'continent_text' => 'nullable|string',
            'custom_images' => 'boolean',
            'fact_card_image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,pending,inactive',
        ]);

        if ($this->custom_images) {
            for ($i = 1; $i <= 3; $i++) {
                $imageField = "image{$i}_path";
                if ($this->$imageField instanceof \Livewire\TemporaryUploadedFile) {
                    $data[$imageField] = $this->$imageField->store('uploads/images/continents');
                }
            }
        } else {
            if (empty($this->image1_path) || empty($this->image2_path) || empty($this->image3_path)) {
                $this->fetchImagesFromPixabay();
            }

            $data['image1_path'] = $this->image1_path;
            $data['image2_path'] = $this->image2_path;
            $data['image3_path'] = $this->image3_path;
        }


        if (!empty($this->fact_card_image)){
            // 1. Temporäre Datei speichern im public-Speicher
            $originalPath = $this->fact_card_image->store('uploads/images/continents', 'public'); // gibt relativen Pfad zurück

           // dd($originalPath);
            // 2. WebP erzeugen
            $webpPath = ImageHelper::convertToWebp($originalPath);

            // 3. Wenn WebP erfolgreich erzeugt wurde, speichern wir den Pfad
            if ($webpPath) {
                $data['fact_card_image'] = $webpPath;

                // 4. Optional: Original löschen
                \Storage::disk('public')->delete($originalPath);
            } else {
                // Falls WebP fehlschlug: Fallback auf Original
                $data['fact_card_image'] = $originalPath;
            }
        }
        //dd($data);


        return $data;
    }

    public function deleteImage($index)
    {
        $imageField = "image{$index}_path";

        if ($this->$imageField && Storage::exists($this->$imageField)) {
            Storage::delete($this->$imageField);
        }

        $this->$imageField = null;

        if ($this->editMode) {
            $continent = WwdeContinent::findOrFail($this->continentId);
            $continent->update([$imageField => null]);

            // Cache löschen bei Bildentfernung
            $this->clearContinentCache($continent);
        }
    }

    public function resetInputFields()
    {
        $this->continentId = null;
        $this->title = $this->alias = $this->iso2 = $this->iso3 = null;
        $this->area_km = $this->population = $this->no_countries = $this->no_climate_tables = null;
        $this->continent_text = null;
        $this->image1_path = $this->image2_path = $this->image3_path = null;
        $this->custom_images = false;
        $this->fact_card_image = null;
        $this->status = 'active';
        $this->editMode = false;
    }

    // Neue Methode zum Löschen des Caches
    private function clearContinentCache($continent)
    {
        if ($continent) {
            Cache::forget("continent_{$continent->id}");
            Cache::forget("continent_alias_{$continent->alias}");
            Cache::forget("continent_images_{$continent->id}");
            Cache::forget("continents_list"); // Falls eine allgemeine Liste zwischengespeichert wird
        }
    }

    public function render()
    {
        $continents = WwdeContinent::where('title', 'like', "%{$this->search}%")
            ->orWhere('alias', 'like', "%{$this->search}%")
            ->orderBy('title')
            ->paginate($this->perPage);

        return view('livewire.backend.continent-manager.continent-manager-component', compact('continents'))
            ->layout('raadmin.layout.master');
    }
}
