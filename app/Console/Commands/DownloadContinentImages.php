<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadContinentImages extends Command
{
    protected $signature = 'locations:download-continent-images';
    protected $description = 'Download and save images for continents from Pixabay';

    public function handle()
    {
        $this->info('Starting to download images for continents...');

        $continents = DB::table('wwde_continents')->get();

        foreach ($continents as $continent) {
            // Skip if custom images are set
            if (property_exists($continent, 'custom_images') && $continent->custom_images) {
                $this->info("Skipping {$continent->title} as it has custom images.");
                continue;
            }

            // Download images from Pixabay
            $images = $this->fetchImagesFromPixabay($continent->title);

            if (!$images) {
                $this->error("No images found for {$continent->title}.");
                continue;
            }

            // Save images to the filesystem
            $imagePaths = [];
            foreach ($images as $index => $imageUrl) {
                $indexIncremented = $index + 1; // Index inkrementieren
                $fileName = "{$continent->alias}_image{$indexIncremented}.jpg"; // Dateiname erstellen
                $filePath = "uploads/images/continents/{$fileName}";

                if ($this->saveImage($imageUrl, $filePath)) {
                    $imagePaths[] = Storage::url($filePath); // Öffentlicher URL-Pfad
                } else {
                    $this->error("Failed to save image {$indexIncremented} for {$continent->title}.");
                }
            }

            // Update database with image paths
            if (!empty($imagePaths)) {
                DB::table('wwde_continents')->where('id', $continent->id)->update([
                    'image1_path' => $imagePaths[0] ?? null,
                    'image2_path' => $imagePaths[1] ?? null,
                    'image3_path' => $imagePaths[2] ?? null,
                ]);
            } else {
                $this->error("No images were saved for {$continent->title}.");
            }

            $this->info("Images for {$continent->title} have been updated.");
        }

        $this->info('All continent images processed successfully.');
    }

    private function fetchImagesFromPixabay(string $query)
    {
        $apiKey = config('services.pixabay.api_key');

        if (!$apiKey) {
            $this->error('Pixabay API key is missing.');
            return null;
        }

        $response = Http::get('https://pixabay.com/api/', [
            'key' => $apiKey,
            'q' => $query,
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'safesearch' => 'true',
            'per_page' => 3,
        ]);

        if ($response->failed()) {
            $this->error('Failed to fetch images from Pixabay API: ' . $response->body());
            return null;
        }

        $data = $response->json();

        return collect($data['hits'] ?? [])->pluck('webformatURL')->take(3)->toArray();
    }

    private function saveImage(string $url, string $path): bool
    {
        try {
            $imageContent = Http::get($url)->body();
            Storage::disk('public')->makeDirectory(dirname($path)); // Verzeichnis erstellen, falls es nicht existiert
            Storage::disk('public')->put($path, $imageContent); // Datei speichern
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to save image: ' . $e->getMessage());
            return false;
        }
    }
}
