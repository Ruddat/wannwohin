<?php

namespace App\Livewire\Backend\CountryManager;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\WwdeCountry;
use App\Models\WwdeContinent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class CountryManagerComponent extends Component
{
    use WithFileUploads, WithPagination;

    // Felder aus der Datenbanktabelle
    public $countryId, $continent_id, $title, $alias, $currency_code, $currency_name;
    public $country_code, $country_text, $population, $capital, $area;
    public $official_language, $language_ezmz, $bsp_in_USD, $life_expectancy_m, $life_expectancy_w;
    public $population_density, $country_iso_3, $continent_iso_2, $continent_iso_3;
    public $country_visum_needed = false, $country_visum_max_time, $count_climatezones;
    public $climatezones_ids, $climatezones_lnam, $climatezones_details_lnam, $artikel;
    public $travelwarning_id, $price_tendency, $status = 'active';
    public $image1_path, $image2_path, $image3_path, $custom_images = false;

    // Pixabay-Spezifisch
    public $searchKeyword = '';
    public $pixabayImages = [];
    public $content = 'Standardwert im Editor';

    // Suchfeld
    public $search = '';
    public $editMode = false;

    // Validierungsregeln
    public function rules()
    {
        $rules = [
            'continent_id' => 'required|exists:wwde_continents,id',
            'title' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'currency_code' => 'nullable|string|max:3',
            'currency_name' => 'nullable|string|max:50',
            'country_code' => 'required|string|max:3',
            'country_text' => 'nullable|string|max:500',
            //'currency_conversion' => 'nullable|string|max:255',
            'population' => 'nullable|integer',
            'capital' => 'nullable|string|max:255',
           // 'population_capital' => 'nullable|integer',
            'area' => 'nullable|integer',
            'official_language' => 'nullable|string|max:255',
            'language_ezmz' => 'nullable|string|max:255',
            'bsp_in_USD' => 'nullable|integer',
            'life_expectancy_m' => 'nullable|numeric',
            'life_expectancy_w' => 'nullable|numeric',
            'population_density' => 'nullable|numeric',
            'country_iso_3' => 'nullable|string|max:3',
            'continent_iso_2' => 'nullable|string|max:2',
            'continent_iso_3' => 'nullable|string|max:3',
            'country_visum_needed' => 'boolean',
            'country_visum_max_time' => 'nullable|string|max:50',
            'count_climatezones' => 'nullable|integer',
            'climatezones_ids' => 'nullable|string|max:50',
            'climatezones_lnam' => 'nullable|string|max:255',
            'climatezones_details_lnam' => 'nullable|string|max:255',
            'artikel' => 'nullable|string|max:50',
            'travelwarning_id' => 'nullable|integer',
            'price_tendency' => 'nullable|string|max:10',
            'status' => 'required|in:active,pending,inactive',
            'custom_images' => 'boolean',
        ];

        if ($this->custom_images) {
            $rules['image1_path'] = 'nullable|image|max:2048';
            $rules['image2_path'] = 'nullable|image|max:2048';
            $rules['image3_path'] = 'nullable|image|max:2048';
        }

        return $rules;
    }

    // Initialisierung
    public function mount($id = null)
    {
        if ($id) {
            $this->edit($id);
        }
    }

    // Neues Land erstellen
    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
    }

    // Land bearbeiten
    public function edit($id)
    {
        $country = WwdeCountry::findOrFail($id);
        $this->fill($country->toArray());
        $this->countryId = $country->id;
        $this->editMode = true;
    }

    // Land speichern
    public function save()
    {
        $this->validate();

        $data = $this->prepareData();

        WwdeCountry::updateOrCreate(['id' => $this->countryId], $data);

        $this->resetInputFields();
        $this->dispatch('success', 'Country saved successfully.');
    }

    // Land löschen
    public function delete($id)
    {
        $country = WwdeCountry::findOrFail($id);

        foreach ([1, 2, 3] as $i) {
            $imageField = "image{$i}_path";
            if ($country->$imageField && Storage::exists($country->$imageField)) {
                Storage::delete($country->$imageField);
            }
        }

        $country->delete();
        $this->dispatch('success', 'Country deleted successfully.');
    }

    // Bilder von Pixabay holen
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
            $this->pixabayImages = collect($response['hits'])->take(10);
        } else {
            $this->dispatch('error', 'No images found on Pixabay.');
        }
    }

    // Pixabay-Bild auswählen
    public function selectPixabayImage($index)
    {
        if (isset($this->pixabayImages[$index])) {
            $imageData = $this->pixabayImages[$index];
            $path = "uploads/images/countries/" . uniqid() . ".jpg";
            Storage::disk('public')->put($path, file_get_contents($imageData['largeImageURL']));

            foreach ([1, 2, 3] as $i) {
                $imageField = "image{$i}_path";
                if (!$this->$imageField) {
                    $this->$imageField = $path;
                    break;
                }
            }
        }
    }

    // Bild löschen
    public function deleteImage($index)
    {
        $imageField = "image{$index}_path";
        if ($this->$imageField && Storage::exists($this->$imageField)) {
            Storage::delete($this->$imageField);
        }
        $this->$imageField = null;
    }

    // Daten für die Speicherung vorbereiten
    private function prepareData()
    {
        $data = $this->validate();

        // Falls Custom Images aktiv sind, speichere nur diese
        if ($this->custom_images) {
            for ($i = 1; $i <= 3; $i++) {
                $imageField = "image{$i}_path";
                if ($this->$imageField instanceof \Livewire\TemporaryUploadedFile) {
                    $data[$imageField] = $this->$imageField->store('uploads/images/countries', 'public');
                }
            }
        } else {
            // Falls Pixabay genutzt wird, speichere die Pfade der Pixabay-Bilder
            for ($i = 1; $i <= 3; $i++) {
                $imageField = "image{$i}_path";
                if ($this->$imageField && !($this->$imageField instanceof \Livewire\TemporaryUploadedFile)) {
                    $data[$imageField] = $this->$imageField; // Speichere den Pfad direkt
                }
            }
        }

        return $data;
    }

    // Status umschalten
    public function toggleStatus($id)
    {
        $country = WwdeCountry::findOrFail($id);
        $statuses = ['active' => 'pending', 'pending' => 'inactive', 'inactive' => 'active'];
        $country->update(['status' => $statuses[$country->status]]);
    }

    // Custom Images umschalten
    public function updatedCustomImages($value)
    {
        if ($value) {
            // Falls Custom Images aktiviert sind, entferne die Pixabay-Bilder
            $this->reset(['image1_path', 'image2_path', 'image3_path']);
        } else {
            // Falls Pixabay aktiviert ist, entferne hochgeladene Bilder
            $this->reset(['image1_path', 'image2_path', 'image3_path']);
        }
    }

    // Eingabefelder zurücksetzen
    public function resetInputFields()
    {
        $this->reset([
            'countryId', 'continent_id', 'title', 'alias', 'currency_code', 'currency_name',
            'country_code', 'country_text', 'population', 'capital', 'area', 'official_language',
            'language_ezmz', 'bsp_in_USD', 'life_expectancy_m', 'life_expectancy_w',
            'population_density', 'country_iso_3', 'continent_iso_2', 'continent_iso_3',
            'country_visum_needed', 'country_visum_max_time', 'count_climatezones',
            'climatezones_ids', 'climatezones_lnam', 'climatezones_details_lnam', 'artikel',
            'travelwarning_id', 'price_tendency', 'status', 'image1_path', 'image2_path',
            'image3_path', 'pixabayImages', 'custom_images', 'editMode'
        ]);
    }

    // Rendern der Komponente
    public function render()
    {
        return view('livewire.backend.country-manager.country-manager-component', [
            'countries' => WwdeCountry::where('title', 'like', "%{$this->search}%")
                ->orWhere('country_code', 'like', "%{$this->search}%")
                ->orderBy('title')
                ->paginate(10),
            'continents' => WwdeContinent::all(),
        ])->layout('backend.layouts.livewiere-main');
    }
}
