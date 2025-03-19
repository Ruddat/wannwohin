<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeLocation;
use Livewire\WithFileUploads;
use App\Models\ModLocationGalerie;
use App\Services\ImageSearchService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class LocationEditGalleryComponent extends Component
{
    use WithFileUploads;

    public $locationId;
    public $galleryImages = [];
    public $newImage;
    public $newImages = [];
    public $captions = [];
    public $query = '';
    public $searchResults = [];
    public $perPage = 32;

    protected $imageSearchService;

    public function boot(ImageSearchService $imageSearchService)
    {
        $this->imageSearchService = $imageSearchService;
    }

    public function mount($locationId)
    {
        $this->locationId = $locationId;
       // $this->authorize('update', WwdeLocation::findOrFail($this->locationId));
        $this->loadGallery();
    }

    public function loadGallery()
    {
        $this->galleryImages = ModLocationGalerie::where('location_id', $this->locationId)->get();

        foreach ($this->galleryImages as $image) {
            // Überprüfe, ob das Bild aus `storage` oder `public` kommt
            if (str_starts_with($image->image_path, 'uploads/')) {
                // Falls es ein hochgeladenes Bild ist, füge `storage/` hinzu
                $image->full_url = asset('storage/' . $image->image_path);
            } else {
                // Falls es ein importiertes Bild ist, nutze den direkten public-Pfad
                $image->full_url = asset($image->image_path);
            }

            // Initialisiere das captions-Array
            if (!isset($this->captions[$image->id])) {
                $this->captions[$image->id] = $image->image_caption ?? '';
            }
        }
    }



    public function searchImages()
    {
        $this->validate(['query' => 'required|string|max:255']);
        try {
            $this->searchResults = $this->imageSearchService->searchImages($this->query, $this->perPage);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Fehler bei der Bildersuche: ' . $e->getMessage());

        }
    }

    public function loadMore()
    {
        $this->perPage += 10;
        $this->searchImages();
    }

    public function uploadImage()
    {
        $this->validate(['newImage' => 'image|max:2048']);

        $location = WwdeLocation::findOrFail($this->locationId);
        $cityName = str_replace(' ', '_', $location->title);

        // Lade den Bildinhalt und generiere einen Hash
        $imageContent = $this->newImage->get();
        $imageHash = md5($imageContent); // Erstelle den Hash basierend auf dem Bildinhalt

        $fileName = "{$cityName}_{$imageHash}.jpg";
        $path = "uploads/images/locations/{$cityName}/{$fileName}";

        // Speichere das Bild
        Storage::disk('public')->put($path, $imageContent);

        // Speichere die Datenbankeinträge
        ModLocationGalerie::create([
            'location_id' => $this->locationId,
            'location_name' => $location->title,
            'image_path' => $path,
            'image_caption' => null,
            'image_hash' => $imageHash, // Speichere den Hash
            'image_type' => 'gallery',
            'is_primary' => 0,
        ]);

        $this->newImage = null;
        $this->loadGallery();
        $this->dispatch('show-toast', type: 'success', message: 'Bild erfolgreich hinzugefügt.');

    }

    public function selectImage($imageUrl, $description = null, $imageType = 'gallery')
    {
        try {
            $location = WwdeLocation::findOrFail($this->locationId);
            $cityName = str_replace(' ', '_', $location->title);

            $imageContent = file_get_contents($imageUrl);
            $imageHash = md5($imageUrl);
            $fileName = "{$cityName}_{$imageHash}.jpg";
            $path = "uploads/images/locations/{$cityName}/{$fileName}";

            // Bild speichern
            Storage::disk('public')->put($path, $imageContent);

            // Datenbankeintrag erstellen
            ModLocationGalerie::create([
                'location_id' => $this->locationId,
                'location_name' => $location->title,
                'image_path' => $path,
                'image_caption' => $description,
                'image_hash' => $imageHash, // Der Hash wird hier hinzugefügt
                'image_type' => $imageType,
                'is_primary' => 0,
            ]);

            $this->loadGallery();
            // Toast-Nachricht dispatchen
            $this->dispatch('show-toast', type: 'success', message: 'Bild erfolgreich hinzugefügt.');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Fehler beim Hinzufügen des Bildes: ' . $e->getMessage());
        }
    }

    public function uploadSingleImage($index)
    {
        $this->validate([
            "newImages.{$index}" => 'image|max:2048',
        ]);

        $image = $this->newImages[$index];
        $caption = $this->captions[$index] ?? null;

        $location = WwdeLocation::findOrFail($this->locationId);
        $cityName = str_replace(' ', '_', $location->title);

        $imageContent = $image->get();
        $imageHash = md5($imageContent);
        $fileName = "{$cityName}_{$imageHash}.jpg";
        $path = "uploads/images/locations/{$cityName}/{$fileName}";

        // Speichere das Bild
        Storage::disk('public')->put($path, $imageContent);

        // Datenbankeintrag erstellen
        ModLocationGalerie::create([
            'location_id' => $this->locationId,
            'location_name' => $location->title,
            'image_path' => $path,
            'image_caption' => $caption,
            'image_hash' => $imageHash,
            'image_type' => 'gallery',
            'is_primary' => 0,
        ]);

        // Bild aus der temporären Liste entfernen
        unset($this->newImages[$index]);
        unset($this->captions[$index]);

        $this->loadGallery();
        // Toast-Nachricht dispatchen
        $this->dispatch('show-toast', type: 'success', message: 'Bild erfolgreich hochgeladen.');
    }

    public function updateCaption($imageId, $caption)
    {
        $this->validate([
            "captions.$imageId" => 'nullable|string|max:255',
        ]);

        $image = ModLocationGalerie::findOrFail($imageId);
        if ($image) {
            $image->update(['image_caption' => $caption]);
            // Toast-Nachricht dispatchen
            $this->dispatch('show-toast', type: 'success', message: 'Bildunterschrift erfolgreich aktualisiert.');
        }
    }

    public function confirmDelete($imageId)
    {
        $this->dispatch('confirmDelete', $imageId);
    }

    public function deleteImage($imageId)
    {
        $image = ModLocationGalerie::findOrFail($imageId);

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();
        $this->loadGallery();
        $this->dispatch('show-toast', type: 'success', message: 'Bild erfolgreich gelöscht.');
    }


    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-gallery-component');
    }
}
