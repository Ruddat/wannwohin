<?php

namespace App\Livewire\Backend\StuffUpdater;

use Livewire\Component;
use App\Models\WwdeCountry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CountryImageUpdater extends Component
{
    public $countriesWithoutImages = [];
    public $totalCountries = 0;
    public $currentIndex = 0;
    public $progress = 0;
    public $statusMessage = '';
    public $currentCountry; // Neu: Speichert das aktuelle Land

    public function mount()
    {
        $this->countriesWithoutImages = WwdeCountry::whereNull('image1_path')->get();
        $this->totalCountries = $this->countriesWithoutImages->count();
        $this->statusMessage = "Found {$this->totalCountries} countries without images.";

        // Falls es Länder gibt, das erste direkt setzen
        if ($this->totalCountries > 0) {
            $this->currentCountry = $this->countriesWithoutImages[0];
        }
    }

    public function updateImages()
    {
        if ($this->currentIndex >= $this->totalCountries) {
            $this->statusMessage = 'All images have been updated.';
            return;
        }

        $this->currentCountry = $this->countriesWithoutImages[$this->currentIndex]; // Aktualisiert das aktuelle Land
        $this->statusMessage = "Updating image for: {$this->currentCountry->title}";

        // Abruf des Bildes
        $status = '';
        $imagePath = $this->getCityImage($this->currentCountry->title, 1, $status);

        if ($status === 'inactive' || !$imagePath) {
            $this->statusMessage = "Failed to update image for: {$this->currentCountry->title}";
        } else {
            $this->currentCountry->update(['image1_path' => str_replace('storage/', '', $imagePath)]);
            $this->statusMessage = "Image updated for: {$this->currentCountry->title}";
        }

        $this->currentIndex++;
        $this->progress = round(($this->currentIndex / $this->totalCountries) * 100);
    }

    private function getCityImage($city, $index, &$status)
    {
        $apiKey = config('services.pixabay.api_key', env('PIXABAY_API_KEY'));

        if (empty($apiKey)) {
            $status = 'inactive';
            return null;
        }

        $url = 'https://pixabay.com/api/';
        $response = Http::get($url, [
            'key' => $apiKey,
            'q' => $city,
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'safesearch' => 'true',
            'per_page' => 8,
        ]);

        if ($response->successful() && isset($response['hits'][0])) {
            $imageUrl = $response['hits'][0]['webformatURL'];
            $safeCityName = $this->sanitizeCityName($city);
            $directory = "uploads/images/locations/{$safeCityName}/";
            $fileName = "country_image_{$index}.jpg";

            // Verwende die Disk 'public' ohne das Präfix 'public/' im Pfad
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Lade das Bild herunter
            $imageContents = Http::get($imageUrl)->body();
            Storage::disk('public')->put("{$directory}{$fileName}", $imageContents);

            $status = 'active';
            // Rückgabe des Pfads, der über die URL erreichbar ist
            return "storage/{$directory}{$fileName}";
        }

        $status = 'inactive';
        return null;
    }


    private function sanitizeCityName($city)
    {
        $city = iconv('UTF-8', 'ASCII//TRANSLIT', $city);
        $city = preg_replace('/[^A-Za-z0-9_\-]/', '', $city);
        $city = str_replace(' ', '_', $city);
        return $city;
    }



    public function render()
    {
        return view('livewire.backend.stuff-updater.country-image-updater', [
            'currentCountry' => $this->currentCountry, // Neu: Übergeben an das Blade
        ])->layout('raadmin.layout.master');
    }
}
