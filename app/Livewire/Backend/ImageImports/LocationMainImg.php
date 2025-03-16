<?php

namespace App\Livewire\Backend\ImageImports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationMainImg extends Component
{
    public $message = '';

    /**
     * Bereinigt Dateinamen im Verzeichnis, indem (1) entfernt wird
     */
    private function cleanFilenames($directory)
    {
        $files = File::allFiles($directory);
        foreach ($files as $file) {
            $originalName = $file->getFilename();
            $extension = $file->getExtension(); // z.B. "webp" oder "jpg"
            $basename = pathinfo($originalName, PATHINFO_FILENAME); // Name ohne Endung
            $cleanedBasename = preg_replace('/\(\d+\)$/', '', trim($basename)); // Entferne (1), (2), etc. und trimme Leerzeichen

            if ($basename === $cleanedBasename) {
                continue;
            }

            $cleanedName = $cleanedBasename . '.' . $extension; // z.B. "michael-huber-LbuJLJxz1Oc-unsplash.webp"
            $newPath = $file->getPath() . '/' . $cleanedName;

            if (!File::exists($newPath)) {
                File::move($file->getPathname(), $newPath);
                $this->message .= "Umbenannt: {$originalName} -> {$cleanedName}<br>";
            } else {
                File::delete($file->getPathname());
                $this->message .= "Gelöscht: {$originalName} (Duplikat von {$cleanedName})<br>";
            }
        }
    }

    /**
     * Bereinigt Datenbankeinträge mit (1) im image_path
     */
    private function cleanDatabase()
    {
        $galleryImages = DB::table('mod_location_galeries')
            ->where('image_path', 'like', '%(1)%')
            ->get();

        foreach ($galleryImages as $image) {
            $cleanedPath = preg_replace('/\(\d+\)/', '', $image->image_path);
            $fileExists = File::exists(public_path($cleanedPath));

            if ($fileExists) {
                DB::table('mod_location_galeries')
                    ->where('id', $image->id)
                    ->update([
                        'image_path' => $cleanedPath,
                        'updated_at' => now(),
                    ]);
                $this->message .= "DB aktualisiert: {$image->image_path} -> {$cleanedPath}<br>";
            } else {
                DB::table('mod_location_galeries')
                    ->where('id', $image->id)
                    ->delete();
                $this->message .= "DB bereinigt: {$image->image_path} entfernt<br>";
            }
        }

        $locations = DB::table('wwde_locations')
            ->where(function ($query) {
                $query->where('text_pic1', 'like', '%(1)%')
                      ->orWhere('text_pic2', 'like', '%(1)%')
                      ->orWhere('text_pic3', 'like', '%(1)%');
            })
            ->get();

        foreach ($locations as $location) {
            $updates = [];
            foreach (['text_pic1', 'text_pic2', 'text_pic3'] as $field) {
                if ($location->$field && strpos($location->$field, '(1)') !== false) {
                    $cleanedPath = preg_replace('/\(\d+\)/', '', $location->$field);
                    $updates[$field] = $cleanedPath;
                }
            }
            if (!empty($updates)) {
                DB::table('wwde_locations')
                    ->where('id', $location->id)
                    ->update(array_merge($updates, ['updated_at' => now()]));
                $this->message .= "DB aktualisiert für Location ID {$location->id}<br>";
            }
        }
    }

    /**
     * Importiert Urlaubsfotos in die Galerie-Tabelle
     */
    public function importLocationGalleryImages()
    {
        $directory = public_path('img/location_main_img');
        $this->cleanFilenames($directory);
        $this->cleanDatabase();
        $continentDirs = File::directories($directory);

        DB::transaction(function () use ($continentDirs) {
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

                        $locationRecord = DB::table('wwde_locations')
                            ->where('title', $location)
                            ->orWhere('alias', Str::slug($location))
                            ->first();

                        if (!$locationRecord) {
                            $this->message .= "Übersprungen: Location {$location} existiert nicht in der Datenbank<br>";
                            continue;
                        }

                        $images = collect(File::files($imagePath));
                        $groupedImages = $images->groupBy(function ($file) {
                            $basename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                            return preg_replace('/\(\d+\)/', '', $basename);
                        });

                        foreach ($groupedImages as $basename => $files) {
                            $preferredFile = $files->first(function ($file) {
                                return $file->getExtension() === 'webp';
                            }) ?? $files->first();

                            if (!$preferredFile) {
                                continue;
                            }

                            $fileName = $preferredFile->getFilename();
                            $relativePath = "img/location_main_img/{$continent}/{$country}/{$location}/urlaubsfotos/{$fileName}";
                            $imageHash = md5_file($preferredFile->getPathname());

                            DB::table('mod_location_galeries')->updateOrInsert(
                                ['image_hash' => $imageHash],
                                [
                                    'location_id' => $locationRecord->id,
                                    'location_name' => $location,
                                    'image_path' => $relativePath,
                                    'image_caption' => null,
                                    'image_hash' => $imageHash,
                                    'image_type' => 'gallery',
                                    'is_primary' => 0,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );
                            $this->message .= "Importiert/Aktualisiert: {$fileName} für Location {$location}<br>";
                        }
                    }
                }
            }
        });

        $this->dispatch('importCompleted');
    }

    /**
     * Importiert die Hauptbilder (text_pic1, text_pic2, text_pic3)
     */
    public function importMainLocationImages()
    {
        $directory = public_path('img/location_main_img');
        $this->cleanFilenames($directory);
        $this->cleanDatabase();
        $continentDirs = File::directories($directory);

        DB::transaction(function () use ($continentDirs) {
            foreach ($continentDirs as $continentDir) {
                $continent = basename($continentDir);
                $countryDirs = File::directories($continentDir);

                foreach ($countryDirs as $countryDir) {
                    $country = basename($countryDir);
                    $locationDirs = File::directories($countryDir);

                    foreach ($locationDirs as $locationDir) {
                        $location = basename($locationDir);
                        $imagePath = $locationDir;

                        if (!File::exists($imagePath)) {
                            $this->message .= "Übersprungen: Keine Bilder für {$location}<br>";
                            continue;
                        }

                        $locationRecord = DB::table('wwde_locations')
                            ->where('title', $location)
                            ->orWhere('alias', Str::slug($location))
                            ->first();

                        if (!$locationRecord) {
                            $this->message .= "Übersprungen: Location {$location} existiert nicht in der Datenbank<br>";
                            continue;
                        }

                        $images = collect(File::files($imagePath))->filter(function ($file) {
                            $fileName = basename($file->getFilename());
                            $extension = strtolower($file->getExtension());
                            return Str::startsWith($fileName, ['urlaub-', 'beste-reisezeit-', 'reise-']) && in_array($extension, ['webp', 'jpg']);
                        });

                        $uniqueImages = $images->groupBy(function ($file) {
                            $basename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                            return preg_replace('/\(\d+\)/', '', $basename);
                        })->map(function ($files) {
                            return $files->firstWhere('extension', 'webp') ?? $files->first();
                        });

                        $sortedImages = $uniqueImages->sortBy(function ($file) {
                            $fileName = basename($file->getFilename());
                            if (Str::startsWith($fileName, 'reise-')) return 1;
                            elseif (Str::startsWith($fileName, 'beste-reisezeit-')) return 2;
                            elseif (Str::startsWith($fileName, 'urlaub-')) return 3;
                            return 99;
                        })->take(3);

                        $textPics = [];
                        $storagePath = "uploads/images/locations/{$location}";

                        if (!Storage::disk('public')->exists($storagePath)) {
                            Storage::disk('public')->makeDirectory($storagePath);
                        }

                        $index = 1;
                        foreach ($sortedImages as $image) {
                            $newFileName = "city_image_{$index}." . $image->getExtension();
                            $destinationPath = "{$storagePath}/{$newFileName}";
                            Storage::disk('public')->put($destinationPath, File::get($image));
                            $textPics[] = url("storage/{$destinationPath}");
                            $index++;
                        }

                        DB::table('wwde_locations')
                            ->where('id', $locationRecord->id)
                            ->update([
                                'text_pic1' => $textPics[0] ?? null,
                                'text_pic2' => $textPics[1] ?? null,
                                'text_pic3' => $textPics[2] ?? null,
                                'updated_at' => now(),
                            ]);

                        $this->message .= "Hauptbilder für Location {$location} erfolgreich aktualisiert<br>";
                    }
                }
            }
        });

        $this->dispatch('importCompleted');
    }

    public function render()
    {
        return view('livewire.backend.image-imports.location-main-img');
    }
}
