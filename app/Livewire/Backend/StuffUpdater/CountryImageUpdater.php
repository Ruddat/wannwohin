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

    public function mount()
    {
        $this->countriesWithoutImages = WwdeCountry::whereNull('image1_path')->get();
        $this->totalCountries = $this->countriesWithoutImages->count();
        $this->statusMessage = "Found {$this->totalCountries} countries without images.";
    }

    public function updateImages1()
    {

    }


    public function updateImages()
    {
        if ($this->currentIndex >= $this->totalCountries) {
            $this->statusMessage = 'All images have been updated.';
            return;
        }

        $country = $this->countriesWithoutImages[$this->currentIndex];
        $this->statusMessage = "Updating image for: {$country->title}";

        // Abruf des Bildes
        $status = '';
        $imagePath = $this->getCityImage($country->title, 1, $status);

        if ($status === 'inactive' || !$imagePath) {
            $this->statusMessage = "Failed to update image for: {$country->title}";
        } else {
            $country->update(['image1_path' => str_replace('storage/', '', $imagePath)]); // Nur relativen Pfad speichern
            $this->statusMessage = "Image updated for: {$country->title}";
        }

        $this->currentIndex++;
        $this->progress = round(($this->currentIndex / $this->totalCountries) * 100);
    }

    public function render()
    {
        return view('livewire.backend.stuff-updater.country-image-updater');
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

            if (!Storage::exists("public/{$directory}")) {
                Storage::makeDirectory("public/{$directory}");
            }

            $imageContents = Http::get($imageUrl)->body();
            Storage::put("public/{$directory}{$fileName}", $imageContents);

            $status = 'active';
            return "storage/{$directory}{$fileName}"; // Relativer Pfad
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
}
