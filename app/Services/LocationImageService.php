<?php

namespace App\Services;

use App\Models\ModLocationGalerie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LocationImageService
{
    protected $pixabayApiKey;

    public function __construct()
    {
        $this->pixabayApiKey = config('services.pixabay.api_key', env('PIXABAY_API_KEY'));

        if (empty($this->pixabayApiKey)) {
            throw new \Exception('Pixabay API key is missing.');
        }
    }



    /**
     * Suche nach Bildern bei Pixabay.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchImages(string $query, int $limit = 5): array
    {
        $url = 'https://pixabay.com/api/';
        $results = [];

        try {
            $response = Http::get($url, [
                'key' => $this->pixabayApiKey,
                'q' => $query,
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'safesearch' => 'true',
                'per_page' => $limit,
            ]);

            if ($response->successful()) {
                $images = $response->json()['hits'] ?? [];
                foreach ($images as $image) {
                    $results[] = [
                        'preview_url' => $image['previewURL'],
                        'full_url' => $image['largeImageURL'],
                        'tags' => $image['tags'] ?? '',
                        'id' => $image['id'],
                    ];
                }
            } else {
                Log::error("Pixabay API Fehler: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Pixabay API Exception: {$e->getMessage()}");
        }

        return $results;
    }

    /**
     * Fetch images for a location based on activities, save them with descriptions, and return gallery paths.
     *
     * @param int $locationId
     * @param string $city
     * @param array $activities
     * @param int $limit
     * @return array
     */
    public function fetchImagesByActivities(int $locationId, string $city, array $activities, int $limit = 12): array
    {
        $url = 'https://pixabay.com/api/';
        $galleryPaths = [];

        // Ersetze Leerzeichen in den Ordnernamen durch Unterstriche und eliminiere Sonderzeichen
        $safeCityName = str_replace(' ', '_', $this->sanitizeString($city));
        $directoryPath = "uploads/images/locations/{$safeCityName}";

        // Ensure the directory exists
        Storage::disk('public')->makeDirectory($directoryPath);

        // Get existing image hashes and activities for the location
        $existingImages = ModLocationGalerie::where('location_id', $locationId)
            ->pluck('activity', 'image_hash')
            ->toArray();

        foreach ($activities as $activity) {
            // Skip if images already exist for this activity
            if (in_array($activity, $existingImages)) {
                continue;
            }

            try {
                $response = Http::get($url, [
                    'key' => $this->pixabayApiKey,
                    'q' => "{$city} {$activity}",
                    'image_type' => 'photo',
                    'orientation' => 'horizontal',
                    'safesearch' => 'true',
                    'per_page' => 12,
                ]);

                if ($response->successful()) {
                    $images = $response->json()['hits'] ?? [];

                    foreach ($images as $image) {
                        if (count($galleryPaths) >= $limit) {
                            break 2; // Stop when the limit is reached
                        }

                        $imageUrl = $image['webformatURL'];
                        $description = $image['tags'] ?? 'No description available';
                        $imageHash = md5($imageUrl);

                        // Skip if the image hash already exists
                        if (array_key_exists($imageHash, $existingImages)) {
                            continue;
                        }

                        // Ersetze Leerzeichen durch Unterstriche in den Dateinamen und eliminiere Sonderzeichen
                        // Ursprüngliche Dateierweiterung extrahieren
                        $fileExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);

                        // Bereinige den Dateinamen, aber behalte die Erweiterung
                        $fileName = "{$directoryPath}/" . str_replace(' ', '_', $this->sanitizeString(pathinfo($imageUrl, PATHINFO_FILENAME))) . ".{$fileExtension}";

                        try {
                            // Download and save the image
                            $imageContent = Http::get($imageUrl)->body();
                            Storage::disk('public')->put($fileName, $imageContent);

                            // Save the relative path in the database
                            ModLocationGalerie::create([
                                'location_id' => $locationId,
                                'location_name' => $city,
                                'image_path' => $fileName,
                                'image_hash' => $imageHash,
                                'activity' => $activity, // Save the activity
                                'description' => $description,
                            ]);

                            $galleryPaths[] = [
                                'url' => Storage::url($fileName),
                                'description' => $description,
                            ];

                            // Add the new hash to the list of existing hashes
                            $existingImages[$imageHash] = $activity;
                        } catch (\Exception $e) {
                            Log::error("Error saving image for {$city}: {$e->getMessage()}");
                        }
                    }
                } else {
                    Log::error("Failed to fetch images for {$activity} from Pixabay API: {$response->body()}");
                }
            } catch (\Exception $e) {
                Log::error("Error fetching images for {$activity} from Pixabay API: {$e->getMessage()}");
            }
        }

        return $galleryPaths;
    }




    /**
     * Get gallery for a specific location based on activities with descriptions.
     *
     * @param int $locationId
     * @param string $city
     * @param array $activities
     * @return array
     */
    public function getGalleryByActivities(int $locationId, string $city, array $activities): array
    {
        $gallery = ModLocationGalerie::where('location_id', $locationId)->get();

        if ($gallery->isEmpty()) {
            // Fetch and save images if not already in the database
            return $this->fetchImagesByActivities($locationId, $city, $activities);
        }

        return $gallery->map(function ($image) {
            return [
                'url' => Storage::url($image->image_path),
                'description' => $image->description ?? 'Keine Beschreibung verfügbar',
                'activity' => $image->activity ?? 'Allgemein',
                'image_caption' => $image->image_caption ?? 'Kein Titel verfügbar',
            ];
        })->toArray();
    }


    /**
    * Sanitize a string by removing special characters and accents.
    *
    * @param string $string
    * @return string
    */
    protected function sanitizeString(string $string): string
    {
        // Entferne Akzente und Sonderzeichen
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        // Entferne alles außer Buchstaben, Zahlen, Unterstrichen und Leerzeichen
        $string = preg_replace('/[^A-Za-z0-9 _-]/', '', $string);
        // Trim und zurückgeben
        return trim($string);
    }
}
