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

    public $countryId, $continent_id, $title, $alias, $currency_code, $currency_name;
    public $country_code, $country_text, $currency_conversion, $population, $capital;
    public $population_capital, $area, $official_language, $language_ezmz, $bsp_in_USD;
    public $life_expectancy_m, $life_expectancy_w, $population_density, $country_iso_3;
    public $continent_iso_2, $continent_iso_3, $country_visum_needed, $country_visum_max_time;
    public $count_climatezones, $climatezones_ids, $climatezones_lnam, $climatezones_details_lnam;
    public $artikel, $travelwarning_id, $price_tendency, $status = 'active';
    public $image1_path, $image2_path, $image3_path, $custom_images = false;
    public $search = '', $editMode = false;

    // Pixabay-Spezifisch
    public $searchKeyword = '';
    public $pixabayImages = [];

    public $content = 'Standardwert im Editor'; // Initialwert für den Editor

    protected $rules = [
        'continent_id' => 'required|exists:wwde_continents,id',
        'title' => 'required|string|max:255',
        'alias' => 'nullable|string|max:255',
        'currency_code' => 'nullable|string|max:255',
        'currency_name' => 'nullable|string|max:50',
        'country_code' => 'required|string|max:3',
        'country_text' => 'nullable|string|max:500',
        'population' => 'nullable|integer',
        'capital' => 'nullable|string|max:255',
        'area' => 'nullable|integer',
        'official_language' => 'nullable|string|max:255',
        'bsp_in_USD' => 'nullable|integer',
        'life_expectancy_m' => 'nullable|numeric',
        'life_expectancy_w' => 'nullable|numeric',
        'population_density' => 'nullable|numeric',
        'country_iso_3' => 'nullable|string|max:3',
        'status' => 'required|in:active,pending,inactive',
        'image1_path' => 'nullable|image|max:2048',
        'image2_path' => 'nullable|image|max:2048',
        'image3_path' => 'nullable|image|max:2048',
        'custom_images' => 'boolean',
    ];

    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
    }

    public function edit($id)
    {
        $country = WwdeCountry::findOrFail($id);
        $this->fill($country->toArray());
        $this->countryId = $country->id;
        $this->country_text = $country->country_text; // Wert an den Editor übergeben
        $this->editMode = true;
    }

    public function save()
    {
        $this->validate();

       // dd($this->country_text); // Wert aus dem Editor auslesen


        $data = $this->prepareData();

        if ($this->editMode) {
            $country = WwdeCountry::findOrFail($this->countryId);
            $country->update($data);
        } else {
            WwdeCountry::create($data);
        }

        $this->resetInputFields();
        $this->dispatch('success', 'Country saved successfully.');
    }

    public function delete($id)
    {
        $country = WwdeCountry::findOrFail($id);

        for ($i = 1; $i <= 3; $i++) {
            $imageField = "image{$i}_path";
            if ($country->$imageField && Storage::exists($country->$imageField)) {
                Storage::delete($country->$imageField);
            }
        }

        $country->delete();
        $this->dispatch('success', 'Country deleted successfully.');
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
            $path = "uploads/images/countries/" . uniqid() . ".jpg";
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

    public function deleteImage($index)
    {
        $imageField = "image{$index}_path";

        if ($this->$imageField && Storage::exists($this->$imageField)) {
            Storage::delete($this->$imageField);
        }

        $this->$imageField = null;

        if ($this->editMode) {
            $country = WwdeCountry::findOrFail($this->countryId);
            $country->update([$imageField => null]);
        }
    }

    public function prepareData()
    {
        $data = $this->validate();

        for ($i = 1; $i <= 3; $i++) {
            $imageField = "image{$i}_path";
            if ($this->$imageField instanceof \Livewire\TemporaryUploadedFile) {
                $data[$imageField] = $this->$imageField->store('uploads/images/countries');
            }
        }

        return $data;
    }


    public function toggleStatus($id)
    {
        $country = WwdeCountry::findOrFail($id);
        $newStatus = match ($country->status) {
            'active' => 'pending',
            'pending' => 'inactive',
            default => 'active',
        };
        $country->update(['status' => $newStatus]);
        $this->loadContinents();
        $this->dispatch('success', 'Status updated successfully.');
        session()->flash('status', 'Post successfully updated.');

    }

    public function resetInputFields()
    {
        $this->countryId = $this->continent_id = $this->title = $this->alias = null;
        $this->currency_code = $this->currency_name = $this->country_code = $this->country_text = null;
        $this->population = $this->capital = $this->area = $this->official_language = null;
        $this->image1_path = $this->image2_path = $this->image3_path = null;
        $this->pixabayImages = [];
        $this->custom_images = false;
        $this->status = 'active';
        $this->editMode = false;
    }

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
