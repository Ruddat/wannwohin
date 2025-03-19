<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Storage;
use App\Services\LocationImageService;

class LocationEditImagesComponent extends Component
{
    use WithFileUploads;

    public $locationId;
    public $textPic1, $textPic2, $textPic3;
    public $newImage1, $newImage2, $newImage3;
    public $query1 = '', $query2 = '', $query3 = '';
    public $searchResults1 = [], $searchResults2 = [], $searchResults3 = [];

    protected $imageService;

    public function boot(LocationImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function mount($locationId)
    {
        $this->locationId = $locationId;

        // Hole die Location aus der Datenbank
        $location = WwdeLocation::findOrFail($locationId);

        $this->textPic1 = $location->text_pic1;
        $this->textPic2 = $location->text_pic2;
        $this->textPic3 = $location->text_pic3;

        $locationName = $location->title; // Hole den Titel der Location

        // Standard-Suchbegriffe setzen mit Location-Name
        $this->query1 = "{$locationName} Natur";
        $this->query2 = "{$locationName} Stadt";
        $this->query3 = "{$locationName} Kultur";

        // Optional: Automatische Suche auslösen
        foreach ([1, 2, 3] as $index) {
            $this->searchImages($index);
        }
    }

    public function searchImages($index)
    {
        $query = $this->{"query{$index}"};

        $this->validate([
            "query{$index}" => 'required|string|max:255',
        ]);

        $results = $this->imageService->searchImages($query, 20);
        $this->{"searchResults{$index}"} = $results;
    }

    public function selectImage($index, $imageUrl)
    {
        $location = WwdeLocation::findOrFail($this->locationId);

        // Lösche das alte Bild, falls vorhanden
        $oldPath = $location->{"text_pic{$index}"};
        if ($oldPath && Storage::disk('public')->exists(str_replace(Storage::url(''), '', $oldPath))) {
            Storage::disk('public')->delete(str_replace(Storage::url(''), '', $oldPath));
        }

        // Speichere das neue Bild mit einem eindeutigen Namen
        $path = $this->saveImage($imageUrl, "city_image_{$index}");

        // Aktualisiere die Datenbank
        $location->update([
            "text_pic{$index}" => $path,
        ]);

        // Setze das neue Bild im Livewire-Status
        $this->{"textPic{$index}"} = $path;

        //session()->flash('status', "Bild {$index} erfolgreich geändert.");

        // Toast-Nachricht dispatchen
        $this->dispatch('show-toast', type: 'status', message: 'Bild ' . $index . ' erfolgreich geändert.');

    }

    public function uploadImage($index)
    {
        $this->validate([
            "newImage{$index}" => 'image|max:2048',
        ]);

        $location = WwdeLocation::findOrFail($this->locationId);
        $cityName = str_replace(' ', '_', $location->title);

        // Lösche das alte Bild, falls vorhanden
        $oldPath = $location->{"text_pic{$index}"};
        if ($oldPath && Storage::disk('public')->exists(str_replace(Storage::url(''), '', $oldPath))) {
            Storage::disk('public')->delete(str_replace(Storage::url(''), '', $oldPath));
        }

        // Eindeutiger Dateiname mit Zeitstempel
        $timestamp = now()->timestamp;
        $uniqueFileName = "city_image_{$index}_{$timestamp}.jpg";
        $path = $this->{"newImage{$index}"}->storeAs("uploads/images/locations/{$cityName}", $uniqueFileName, 'public');

        // Aktualisiere die Datenbank
        $location->update([
            "text_pic{$index}" => Storage::url($path),
        ]);

        // Setze das neue Bild im Livewire-Status
        $this->{"textPic{$index}"} = Storage::url($path);
        $this->{"newImage{$index}"} = null;

        // Toast-Nachricht dispatchen
        $this->dispatch('show-toast', type: 'status', message: 'Bild ' . $index . ' erfolgreich hochgeladen.');
    }


    private function saveImage($imageUrl, $fileName)
    {
        $location = WwdeLocation::findOrFail($this->locationId);
        $cityName = str_replace(' ', '_', $location->title);

        // Eindeutiger Dateiname basierend auf Zeitstempel und Hash
        $timestamp = now()->timestamp;
        $hash = substr(md5($imageUrl), 0, 8); // Kürzerer Hash
        $uniqueFileName = "{$fileName}_{$timestamp}_{$hash}.jpg";

        // Speicherpfad
        $path = "uploads/images/locations/{$cityName}/{$uniqueFileName}";

        // Bildinhalt abrufen und speichern
        $imageContent = file_get_contents($imageUrl);
        Storage::disk('public')->put($path, $imageContent);

        return Storage::url($path); // Rückgabe der vollständigen URL
    }



    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-images-component');
    }
}
