<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageSearchService
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
     * Search for images using Pixabay API.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchImages(string $query, int $limit = 30): array
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
                'per_page' => min($limit, 200), // Pixabay allows max 200 per page
            ]);

            if ($response->successful()) {
                $images = $response->json()['hits'] ?? [];
                foreach ($images as $image) {
                    $results[] = [
                        'preview_url' => $image['previewURL'],
                        'full_url' => $image['largeImageURL'],
                        'tags' => $image['tags'] ?? '',
                        'id' => $image['id'], // Unique ID from Pixabay
                    ];
                }
            } else {
                Log::error("Failed to fetch images from Pixabay API: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Error fetching images from Pixabay API: {$e->getMessage()}");
        }

        return array_slice($results, 0, $limit);
    }
}
