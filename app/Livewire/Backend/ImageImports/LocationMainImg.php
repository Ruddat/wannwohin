<?php

namespace App\Livewire\Backend\ImageImports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LocationMainImg extends Component
{
    public $message = '';

    public function importLocationImages()
    {
        $directory = public_path('img/location_main_img');
        $continentDirs = File::directories($directory);

        foreach ($continentDirs as $continentDir) {
            $continent = basename($continentDir);
            $countryDirs = File::directories($continentDir);

            foreach ($countryDirs as $countryDir) {
                $country = basename($countryDir);
                $locationDirs = File::directories($countryDir);

                foreach ($locationDirs as $locationDir) {
                    $location = basename($locationDir);
                    $imagePath = $locationDir . '/urlaubsfotos';

                    if (!File::exists($imagePath)) {
                        $this->message .= "Übersprungen: Keine Urlaubsfotos für {$location}<br>";
                        continue;
                    }

                    // Prüfe, ob die Location existiert
                    $locationRecord = DB::table('wwde_locations')
                        ->where('title', $location)
                        ->orWhere('alias', Str::slug($location))
                        ->first();

                    if (!$locationRecord) {
                        $this->message .= "Übersprungen: Location {$location} existiert nicht in der Datenbank<br>";
                        continue;
                    }

                    // Bilderpfade speichern
                    $images = collect(File::files($imagePath));

                    // Gruppiere Dateien nach Basename ohne Extension
                    $groupedImages = $images->groupBy(function ($file) {
                        return pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    });

                    foreach ($groupedImages as $basename => $files) {
                        // Bevorzuge .webp über andere Formate
                        $preferredFile = $files->first(function ($file) {
                            return $file->getExtension() === 'webp';
                        }) ?? $files->first();

                        if (!$preferredFile) {
                            continue;
                        }

                        $fileName = $preferredFile->getFilename();
                        $relativePath = "img/location_main_img/{$continent}/{$country}/{$location}/urlaubsfotos/{$fileName}";
                        $imageHash = md5_file($preferredFile->getPathname());

                        // Überprüfe, ob der Hash bereits existiert
                        $existingImage = DB::table('mod_location_galeries')
                            ->where('image_hash', $imageHash)
                            ->exists();

                        if ($existingImage) {
                            $this->message .= "Übersprungen: {$fileName} (bereits in der Datenbank)<br>";
                            continue;
                        }

                        // Bildpfad in der Datenbank speichern
                        DB::table('mod_location_galeries')->insert([
                            'location_id' => $locationRecord->id,
                            'location_name' => $location,
                            'image_path' => $relativePath,
                            'image_caption' => null,
                            'image_hash' => $imageHash,
                            'image_type' => 'gallery',
                            'is_primary' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $this->message .= "Importiert: {$fileName} für Location {$location}<br>";
                    }
                }
            }
        }

        $this->dispatch('importCompleted');
    }


    public function render()
    {
        return view('livewire.backend.image-imports.location-main-img');
    }
}
