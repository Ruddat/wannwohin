<?php

namespace App\Services;

use App\Models\LocationGallery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        // Ersetze Leerzeichen in den Ordnernamen durch Unterstriche
        $safeCityName = str_replace(' ', '_', $city);
        $directoryPath = "uploads/images/locations/{$safeCityName}";

        // Ensure the directory exists
        Storage::disk('public')->makeDirectory($directoryPath);

        // Get existing image hashes across all records to avoid duplicates globally
        $existingImageHashes = LocationGallery::pluck('image_hash')->toArray();

        foreach ($activities as $activity) {
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
                        $description = $image['tags'] ?? 'No description available'; // Fetch description
                        $imageHash = md5($imageUrl); // Generate a unique hash for the image URL

                        // Skip if the image hash already exists
                        if (in_array($imageHash, $existingImageHashes)) {
                            continue;
                        }

                        // Ersetze Leerzeichen durch Unterstriche in den Dateinamen
                        $fileName = "{$directoryPath}/" . str_replace(' ', '_', basename($imageUrl));

                        try {
                            // Download and save the image
                            $imageContent = Http::get($imageUrl)->body();
                            Storage::disk('public')->put($fileName, $imageContent);

                            // Save the relative path in the database
                            LocationGallery::create([
                                'location_id' => $locationId,
                                'location_name' => $city,
                                'image_path' => $fileName,
                                'image_hash' => $imageHash, // Save image hash
                                'description' => $description, // Save description
                            ]);

                            $galleryPaths[] = [
                                'url' => Storage::url($fileName),
                                'description' => $description,
                            ];

                            // Add the new hash to the list of existing hashes
                            $existingImageHashes[] = $imageHash;
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
        $gallery = LocationGallery::where('location_id', $locationId)->get();

        if ($gallery->isEmpty()) {
            // Fetch and save images if not already in database
            return $this->fetchImagesByActivities($locationId, $city, $activities);
        }

        return $gallery->map(fn($image) => [
            'url' => Storage::url($image->image_path),
            'description' => $image->description,
        ])->toArray();
    }
}
