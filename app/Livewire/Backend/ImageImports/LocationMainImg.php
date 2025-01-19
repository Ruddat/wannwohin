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
     * Importiert Urlaubsfotos in die Galerie-Tabelle
     */
    public function importLocationGalleryImages()
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

                    // Bilderpfade sammeln
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

/**
 * Importiert die Hauptbilder (text_pic1, text_pic2, text_pic3)
 */
public function importMainLocationImages()
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

                // Im aktuellen Verzeichnis nach den gewünschten Bildern suchen
                $imagePath = $locationDir;

                if (!File::exists($imagePath)) {
                    $this->message .= "Übersprungen: Keine Bilder für {$location}<br>";
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

                // Bilderpfade sammeln: Nur spezifische Dateien mit Präfixen "urlaub-", "beste-reisezeit-", "reise-"
                $images = collect(File::files($imagePath))->filter(function ($file) {
                    $fileName = basename($file->getFilename());
                    $extension = strtolower($file->getExtension());
                    return Str::startsWith($fileName, ['urlaub-', 'beste-reisezeit-', 'reise-']) && in_array($extension, ['webp', 'jpg']);
                });

                // Priorisiere WEBP und entferne doppelte Basenames
                $uniqueImages = $images->groupBy(function ($file) {
                    return pathinfo($file->getFilename(), PATHINFO_FILENAME); // Basename ohne Extension
                })->map(function ($files) {
                    // Bevorzuge WEBP über JPG
                    return $files->firstWhere('extension', 'webp') ?? $files->first();
                });

                // Nach Präfix priorisieren: urlaub-, beste-reisezeit-, reise-
                $sortedImages = $uniqueImages->sortBy(function ($file) {
                    $fileName = basename($file->getFilename());

                    if (Str::startsWith($fileName, 'reise-')) {
                        return 1;
                    } elseif (Str::startsWith($fileName, 'beste-reisezeit-')) {
                        return 2;
                    } elseif (Str::startsWith($fileName, 'urlaub-')) {
                        return 3;
                    }
                    return 99;
                })->take(3);

                $textPics = []; // Speichert die drei Bilder für text_pic1, text_pic2, text_pic3
                $storagePath = "uploads/images/locations/{$location}";

                if (!Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->makeDirectory($storagePath); // Verzeichnis erstellen
                }

                $index = 1;

                foreach ($sortedImages as $image) {
                    $newFileName = "city_image_{$index}." . $image->getExtension();
                    $destinationPath = "{$storagePath}/{$newFileName}";

                    // Bild kopieren und überschreiben
                    Storage::disk('public')->put($destinationPath, File::get($image));

                    // Speichere den relativen Pfad für die textPics-Liste
                    $textPics[] = url("storage/{$destinationPath}");

                    $index++;
                }

                // Maximal 3 Bilder eintragen und Felder in der Tabelle aktualisieren
                DB::table('wwde_locations')
                    ->where('id', $locationRecord->id)
                    ->update([
                        'text_pic1' => $textPics[0] ?? null,
                        'text_pic2' => $textPics[1] ?? null,
                        'text_pic3' => $textPics[2] ?? null,
                        'updated_at' => now(),
                    ]);

                $this->message .= "Hauptbilder für Location {$location} erfolgreich kopiert und Felder aktualisiert<br>";
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
