<?php

namespace App\Livewire\Backend\CountryManager;

use Livewire\Component;
use App\Models\WwdeCountry;
use Livewire\WithPagination;
use App\Models\WwdeContinent;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CountryManagerComponent extends Component
{
    use WithFileUploads, WithPagination;

    public $filterContinent = '';
    public $filterStatus = '';
    public $filterPopulation = '';

    // Felder aus der Datenbanktabelle
    public $countryId, $continent_id, $title, $alias, $currency_code, $currency_name;
    public $country_code, $country_text, $population, $capital, $area;
    public $official_language, $language_ezmz, $bsp_in_USD, $life_expectancy_m, $life_expectancy_w;
    public $population_density, $country_iso_3, $continent_iso_2, $continent_iso_3;
    public $country_visum_needed = false, $country_visum_max_time, $count_climatezones;
    public $climatezones_ids, $climatezones_lnam, $climatezones_details_lnam, $artikel;
    public $travelwarning_id, $price_tendency, $status = 'active';
    public $image1_path, $image2_path, $image3_path, $custom_images = false;
    public $country_headert_titel, $country_header_text;

    public $currency_conversion;
    public $population_capital;
    public $panorama_image_path, $header_image_path;
    // Alternativ, falls du einen separaten Upload-Mechanismus nutzen möchtest:
    public $newPanoramaImage, $newHeaderImage;

    // Pixabay-Spezifisch
    public $searchKeyword = '';
    public $pixabayImages = [];
    public $content = 'Standardwert im Editor';

    // Suchfeld
    public $search = '';
    public $editMode = false;

    public $newImages = [];

    protected $queryString = ['search'];


    public $panoramaPixabayImages = [];
    public $headerPixabayImages = [];
    public $panoramaSearchKeyword;
    public $headerSearchKeyword;


    // Validierungsregeln
    public function rules()
    {
        $rules = [
            'continent_id' => 'required|exists:wwde_continents,id',
            'title' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'country_headert_titel' => 'nullable|string|max:255',
            'country_header_text' => 'nullable|string',
            'currency_code' => 'nullable|string|max:3',
            'currency_name' => 'nullable|string|max:50',
            'country_code' => 'required|string|max:3',
            'country_text' => 'nullable|string|max:3000',
            'population' => 'nullable|integer',
            'capital' => 'nullable|string|max:255',
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
            'newPanoramaImage' => 'nullable|image|max:4096',
            'newHeaderImage' => 'nullable|image|max:2048',
        ];

        // Bedingte Regeln für gespeicherte Pfade
        $rules['panorama_image_path'] = $this->custom_images && $this->newPanoramaImage ? 'nullable|image|max:4096' : 'nullable|string';
        $rules['header_image_path'] = $this->custom_images && $this->newHeaderImage ? 'nullable|image|max:2048' : 'nullable|string';

        return $rules;
    }

    // Initialisierung
    public function mount($id = null)
    {
        if ($id) {
            $country = WwdeCountry::find($id);
            if ($country) {
                $this->fill($country->toArray());
                $this->custom_images = (bool) $country->custom_images; // Sicherstellen, dass es bool ist
            }
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
        try {
            $this->validate();
            $data = $this->prepareData();
            $country = WwdeCountry::updateOrCreate(['id' => $this->countryId], $data);

            if ($country) {
                Cache::forget("country_{$country->alias}");
                Cache::forget("countries_{$country->continent_id}");
                Cache::forget("continent_{$country->continent_id}");
                Cache::forget("continent_images_{$country->continent_id}");
            }

            $this->resetInputFields();
            $this->dispatch('success', 'Country saved successfully.');
        } catch (\Exception $e) {
            \Log::error('Fehler beim Speichern: ' . $e->getMessage());
            $this->dispatch('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }
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
    private function prepareData()
    {
        $data = $this->validate();

        if ($this->custom_images) {
            // Standardbilder (max. 3 Bilder)
            if (count($this->newImages) > 0) {
                for ($i = 1; $i <= 3; $i++) {
                    $imageField = "image{$i}_path";
                    if ($this->$imageField && Storage::exists($this->$imageField)) {
                        Storage::delete($this->$imageField);
                    }
                }
                $paths = [];
                foreach ($this->newImages as $index => $image) {
                    $paths[] = $image->store('uploads/images/countries', 'public');
                }
                $data['image1_path'] = $paths[0] ?? null;
                $data['image2_path'] = $paths[1] ?? null;
                $data['image3_path'] = $paths[2] ?? null;
                $this->newImages = [];
            } else {
                $data['image1_path'] = $this->image1_path;
                $data['image2_path'] = $this->image2_path;
                $data['image3_path'] = $this->image3_path;
            }

            // Panorama-Bild
            if ($this->newPanoramaImage) {
                if ($this->panorama_image_path && Storage::exists($this->panorama_image_path)) {
                    Storage::delete($this->panorama_image_path);
                }
                $data['panorama_image_path'] = $this->newPanoramaImage->store('uploads/images/countries', 'public');
                $this->newPanoramaImage = null;
            } else {
                $data['panorama_image_path'] = $this->panorama_image_path;
            }

            // Header-Bild
            if ($this->newHeaderImage) {
                if ($this->header_image_path && Storage::exists($this->header_image_path)) {
                    Storage::delete($this->header_image_path);
                }
                $data['header_image_path'] = $this->newHeaderImage->store('uploads/images/countries', 'public');
                $this->newHeaderImage = null;
            } else {
                $data['header_image_path'] = $this->header_image_path;
            }
        } else {
            // Pixabay-Bilder oder bestehende Pfade
            $data['image1_path'] = $this->image1_path;
            $data['image2_path'] = $this->image2_path;
            $data['image3_path'] = $this->image3_path;
            $data['panorama_image_path'] = $this->panorama_image_path;
            $data['header_image_path'] = $this->header_image_path;
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
            if (!($this->image1_path instanceof \Livewire\TemporaryUploadedFile)) $this->image1_path = null;
            if (!($this->image2_path instanceof \Livewire\TemporaryUploadedFile)) $this->image2_path = null;
            if (!($this->image3_path instanceof \Livewire\TemporaryUploadedFile)) $this->image3_path = null;
            if (!($this->panorama_image_path instanceof \Livewire\TemporaryUploadedFile)) $this->panorama_image_path = null;
            if (!($this->header_image_path instanceof \Livewire\TemporaryUploadedFile)) $this->header_image_path = null;
        } else {
            $this->reset(['image1_path', 'image2_path', 'image3_path', 'panorama_image_path', 'header_image_path']);
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
            'image3_path', 'pixabayImages', 'custom_images', 'editMode',
            'country_headert_titel', 'country_header_text'
        ]);
    }

    public function exportToExcel()
    {
        $countries = WwdeCountry::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'ID', 'Kontinent', 'Land', 'Alias', 'Währungscode', 'Währungsname', 'Ländercode', 'Beschreibung',
            'Währungsumrechnung', 'Bevölkerung', 'Hauptstadt', 'Hauptstadt Bevölkerung', 'Fläche (km²)',
            'Amtssprache', 'EZMZ-Sprache', 'BSP in USD', 'Lebenserwartung M', 'Lebenserwartung W',
            'Bevölkerungsdichte', 'ISO3-Code', 'Kontinent ISO2', 'Kontinent ISO3',
            'Visum benötigt', 'Max. Visumdauer', 'Anzahl Klimazonen', 'Klimazonen-IDs', 'Klimazonen Namen',
            'Klimazonen Details', 'Artikel', 'Reisewarnung ID', 'Preistendenz',
            'Bild 1', 'Bild 2', 'Bild 3', 'Eigene Bilder', 'Status', 'Erstellt am', 'Aktualisiert am'
        ];

        // Header in Zeile 1 setzen (sichere Methode für Spaltenbuchstaben)
        foreach ($headers as $colIndex => $header) {
            $columnLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue("{$columnLetter}1", $header);
        }

        $row = 2;
        foreach ($countries as $country) {
            $values = [
                $country->id,
                optional($country->continent)->title ?? 'N/A',
                $country->title,
                $country->alias,
                $country->currency_code,
                $country->currency_name,
                $country->country_code,
                $country->country_text,
                $country->currency_conversion,
                $country->population,
                $country->capital,
                $country->population_capital,
                $country->area,
                $country->official_language,
                $country->language_ezmz,
                $country->bsp_in_USD,
                $country->life_expectancy_m,
                $country->life_expectancy_w,
                $country->population_density,
                $country->country_iso_3,
                $country->continent_iso_2,
                $country->continent_iso_3,
                $country->country_visum_needed ? 'Ja' : 'Nein',
                $country->country_visum_max_time,
                $country->count_climatezones,
                $country->climatezones_ids,
                $country->climatezones_lnam,
                $country->climatezones_details_lnam,
                $country->artikel,
                $country->travelwarning_id,
                $country->price_tendency,
                $country->image1_path ? asset('storage/' . $country->image1_path) : 'N/A',
                $country->image2_path ? asset('storage/' . $country->image2_path) : 'N/A',
                $country->image3_path ? asset('storage/' . $country->image3_path) : 'N/A',
                $country->custom_images ? 'Ja' : 'Nein',
                ucfirst($country->status),
                $country->created_at,
                $country->updated_at,
            ];

            foreach ($values as $colIndex => $value) {
                $columnLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue("{$columnLetter}{$row}", $value);
            }

            $row++;
        }

        // Datei speichern und ausliefern
        $writer = new Xlsx($spreadsheet);
        $fileName = 'countries_export.xlsx';

        return Response::stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }


    public function fetchCountryData()
    {
        if (!$this->country_code) {
            session()->flash('error', 'Bitte zuerst einen Ländercode eingeben.');
            return;
        }

        $response = Http::get("https://restcountries.com/v3.1/alpha/{$this->country_code}");

        if ($response->successful() && count($response->json()) > 0) {
            $country = $response->json()[0];

            $this->title = $country['name']['common'] ?? '';
            $this->country_iso_3 = $country['cca3'] ?? '';
            $this->capital = $country['capital'][0] ?? '';
            $this->continent = $country['continents'][0] ?? '';
            $this->population = $country['population'] ?? '';
            $this->area = $country['area'] ?? '';
            $this->languages = implode(', ', array_values($country['languages'] ?? []));
            $this->timezone = implode(', ', $country['timezones'] ?? []);
            $this->flag = $country['flags']['png'] ?? '';
            $this->coat_of_arms = $country['coatOfArms']['png'] ?? '';
            $this->google_maps_link = $country['maps']['googleMaps'] ?? '';
           // $this->gini = $country['gini'][array_key_first($country['gini'])] ?? null;

            // Bevölkerungsdichte berechnen (Bevölkerung / Fläche)
            $this->population_density = ($this->area > 0) ? round($this->population / $this->area, 2) : null;

            session()->flash('success', 'Daten erfolgreich geladen!');
        } else {
            session()->flash('error', 'Keine Daten für diesen Code gefunden.');
        }
    }


public function removeImage($index)
{
    unset($this->newImages[$index]);
    $this->newImages = array_values($this->newImages); // Neu indexieren
}

public function deleteImage($index)
{
    $imageField = "image{$index}_path";

    // Falls das Bild existiert, lösche es aus dem Storage
    if (!empty($this->$imageField) && Storage::exists($this->$imageField)) {
        Storage::delete($this->$imageField);
    }

    // Setze das Bildfeld zurück in der Livewire-Variable
    $this->$imageField = null;

    // Falls eine ID existiert, update das Land in der Datenbank
    if ($this->countryId) {
        WwdeCountry::where('id', $this->countryId)->update([$imageField => null]);
    }
}



    // Rendern der Komponente
    public function render()
    {
        $query = WwdeCountry::query();

        // Debugging: Anzeigen der aktuellen Filterwerte
        //\Log::info('Suchfilter angewendet:', ['search' => $this->search]);
        //\Log::info('Kontinent-Filter angewendet:', ['filterContinent' => $this->filterContinent]);
        //\Log::info('Status-Filter angewendet:', ['filterStatus' => $this->filterStatus]);
        //\Log::info('Bevölkerungs-Filter angewendet:', ['filterPopulation' => $this->filterPopulation]);


        // Suchfilter
        if (!empty(trim($this->search))) {
            $query->where(function ($q) {
                $q->where('title', 'LIKE', "%{$this->search}%")
                  ->orWhere('alias', 'LIKE', "%{$this->search}%")
                  ->orWhere('country_code', 'LIKE', "%{$this->search}%");
            });
        }

        // Kontinent-Filter
        if (!empty($this->filterContinent)) {
            $query->whereIn('continent_id', explode(',', $this->filterContinent));
        }

        // Status-Filter
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        // Bevölkerung-Filter
        if (!empty($this->filterPopulation)) {
            switch ($this->filterPopulation) {
                case 'low':
                    $query->where('population', '<', 1000000);
                    break;
                case 'medium':
                    $query->whereBetween('population', [1000000, 10000000]);
                    break;
                case 'high':
                    $query->where('population', '>', 10000000);
                    break;
            }
        }

        // Log-Ausgabe für die generierte SQL-Query
        //\Log::info('Generierte SQL:', ['query' => $query->toSql()]);

        return view('livewire.backend.country-manager.country-manager-component', [
            'countries' => $query->orderBy('title')->paginate(10),
            'continents' => WwdeContinent::all(),
        ])->layout('backend.layouts.livewiere-main');
    }




    public function fetchPanoramaImages()
{
    $keyword = $this->panoramaSearchKeyword ?: $this->title;
    $response = Http::get("https://pixabay.com/api/", [
        'key' => env('PIXABAY_API_KEY'),
        'q' => $keyword,
        'image_type' => 'photo',
        'orientation' => 'horizontal',
        'category' => 'nature',
        'per_page' => 10,
    ]);

    if ($response->ok() && isset($response['hits'])) {
        $this->panoramaPixabayImages = collect($response['hits'])->take(10);
    } else {
        $this->dispatch('error', 'Keine Panorama Bilder auf Pixabay gefunden.');
    }
}

public function fetchHeaderImages()
{
    $keyword = $this->headerSearchKeyword ?: $this->title;
    $response = Http::get("https://pixabay.com/api/", [
        'key' => env('PIXABAY_API_KEY'),
        'q' => $keyword,
        'image_type' => 'photo',
        'orientation' => 'horizontal',
        'category' => 'nature',
        'per_page' => 10,
    ]);

    if ($response->ok() && isset($response['hits'])) {
        $this->headerPixabayImages = collect($response['hits'])->take(10);
    } else {
        $this->dispatch('error', 'Keine Header Bilder auf Pixabay gefunden.');
    }
}


public function selectPanoramaImage($index)
{
    if (isset($this->panoramaPixabayImages[$index])) {
        $imageData = $this->panoramaPixabayImages[$index];
        $path = "uploads/images/countries/" . uniqid() . ".jpg";
        Storage::disk('public')->put($path, file_get_contents($imageData['largeImageURL']));
        $this->panorama_image_path = $path;
    }
}

public function selectHeaderImage($index)
{
    if (isset($this->headerPixabayImages[$index])) {
        $imageData = $this->headerPixabayImages[$index];
        $path = "uploads/images/countries/" . uniqid() . ".jpg";
        Storage::disk('public')->put($path, file_get_contents($imageData['largeImageURL']));
        $this->header_image_path = $path;
    }
}



    public function deleteHeaderImage()
    {
        if ($this->header_image_path && Storage::exists($this->header_image_path)) {
            Storage::delete($this->header_image_path);
        }

        $this->header_image_path = null;

        // Falls eine ID existiert, aktualisiere das Land in der Datenbank
        if ($this->countryId) {
            WwdeCountry::where('id', $this->countryId)
                ->update(['header_image_path' => null]);
        }
    }

    public function deletePanoramaImage()
    {
        if ($this->panorama_image_path && Storage::exists($this->panorama_image_path)) {
            Storage::delete($this->panorama_image_path);
        }

        $this->panorama_image_path = null;

        if ($this->countryId) {
            WwdeCountry::where('id', $this->countryId)
                ->update(['panorama_image_path' => null]);
        }
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Setzt die Paginierung zurück, damit die erste Seite angezeigt wird.
    }


}
