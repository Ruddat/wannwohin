<?php

namespace App\Livewire\Backend\ImageImports;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Helpers\PathSanitizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
    ini_set('max_execution_time', 360);
    set_time_limit(360);

    $baseDir = public_path('img/location_main_img');
    $continents = File::directories($baseDir);

    DB::transaction(function () use ($continents) {

        foreach ($continents as $continentDir) {

            foreach (File::directories($continentDir) as $countryDir) {

                foreach (File::directories($countryDir) as $locationDir) {

                    $locationOriginal = basename($locationDir);
                    $locationSlug = PathSanitizer::locationSlug($locationOriginal);

                    // Zielordnername korrigieren
                    $targetLocationDir = dirname($locationDir) . '/' . $locationSlug;

                    if ($locationDir !== $targetLocationDir) {
                        File::move($locationDir, $targetLocationDir);
                        $this->message .= "Ordner umbenannt: {$locationOriginal} → {$locationSlug}<br>";
                    }

                    $locationDir = $targetLocationDir;

                    $galleryDir = $locationDir . '/urlaubsfotos';
                    if (!File::exists($galleryDir)) {
                        $this->message .= "Übersprungen: Keine Urlaubsfotos für {$locationOriginal}<br>";
                        continue;
                    }

                    // DB-Location finden
                    $locationRecord = DB::table('wwde_locations')
                        ->where('title', $locationOriginal)
                        ->orWhere('alias', $locationSlug)
                        ->first();

                    if (!$locationRecord) {
                        $this->message .= "Übersprungen: Keine DB-Location für {$locationOriginal}<br>";
                        continue;
                    }

                    $files = collect(File::files($galleryDir));

                    // Gruppierung nach Basename (ohne (1), (2)...)
                    $grouped = $files->groupBy(function ($file) {
                        $basename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        return preg_replace('/\(\d+\)$/', '', $basename);
                    });

                    // Speicherziel
                    $storagePath = "uploads/images/locations/{$locationSlug}";
                    Storage::disk('public')->makeDirectory($storagePath);

                    foreach ($grouped as $basename => $files) {

                        // WebP bevorzugen
                        $preferred = $files->firstWhere('extension', 'webp') ?? $files->first();
                        if (!$preferred) continue;

                        $fullPath = $preferred->getPathname();

                        /**
                         * 1. MIME-Type auslesen → richtige Endung bestimmen
                         */
                        $mime = mime_content_type($fullPath);

                        $ext = match ($mime) {
                            'image/jpeg', 'image/jpg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp',
                            default => 'jpg',
                        };

                        /**
                         * 2. Datei sicher benennen
                         */
                        $cleanBase = PathSanitizer::filename($basename);
                        $cleanName = "{$cleanBase}.{$ext}";

                        $cleanPath = "{$storagePath}/{$cleanName}";

                        /**
                         * 3. Datei speichern
                         */
                        Storage::disk('public')->put(
                            $cleanPath,
                            File::get($fullPath)
                        );

                        /**
                         * 4. Bildhash bestimmen
                         */
                        $imageHash = md5_file($fullPath);

                        /**
                         * 5. Datenbank speichern
                         */
                        DB::table('mod_location_galeries')->updateOrInsert(
                            ['image_hash' => $imageHash],
                            [
                                'location_id' => $locationRecord->id,
                                'location_name' => $locationOriginal,
                                'image_path' => $cleanPath,
                                'image_type' => 'gallery',
                                'is_primary' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                        $this->message .= "Galeriebild importiert: {$cleanName} ({$locationSlug})<br>";
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
    ini_set('max_execution_time', 360);
    set_time_limit(360);

    $baseDir = public_path('img/location_main_img');
    $continents = File::directories($baseDir);

    DB::transaction(function () use ($continents) {

        foreach ($continents as $continentDir) {
            $continent = basename($continentDir);

            foreach (File::directories($continentDir) as $countryDir) {
                $country = basename($countryDir);

                foreach (File::directories($countryDir) as $locationDir) {

                    $locationOriginal = basename($locationDir);
                    $locationSlug = PathSanitizer::locationSlug($locationOriginal);

                    $targetLocationDir = dirname($locationDir) . '/' . $locationSlug;

                    if ($locationDir !== $targetLocationDir) {
                        File::move($locationDir, $targetLocationDir);
                        $this->message .= "Ordner umbenannt: {$locationOriginal} → {$locationSlug}<br>";
                    }

                    $locationDir = $targetLocationDir;

                    // DB-Location holen
                    $locationRecord = DB::table('wwde_locations')
                        ->where('title', $locationOriginal)
                        ->orWhere('alias', $locationSlug)
                        ->first();

                    if (!$locationRecord) {
                        $this->message .= "Übersprungen: Keine DB-Location für {$locationOriginal}<br>";
                        continue;
                    }

                    // Alle Hauptbilder (urlaub-, beste-reisezeit-, reise-)
                    $files = collect(File::files($locationDir))->filter(function ($f) {
                        $name = strtolower($f->getFilename());
                        return (
                            str_starts_with($name, 'urlaub-') ||
                            str_starts_with($name, 'beste-reisezeit-') ||
                            str_starts_with($name, 'reise-')
                        ) && in_array($f->getExtension(), ['webp', 'jpg']);
                    });

                    if ($files->isEmpty()) continue;

                    // Unique-basename+WebP bevorzugen
                    $grouped = $files->groupBy(function ($f) {
                        $base = pathinfo($f->getFilename(), PATHINFO_FILENAME);
                        return preg_replace('/\(\d+\)$/', '', $base);
                    })->map(function ($files) {
                        return $files->firstWhere('extension', 'webp') ?? $files->first();
                    });

                    // Sortieren in deine Reihenfolge
                    $sorted = $grouped->sortBy(function ($f) {
                        $n = strtolower($f->getFilename());
                        if (str_starts_with($n, 'reise-')) return 1;
                        if (str_starts_with($n, 'beste-reisezeit-')) return 2;
                        if (str_starts_with($n, 'urlaub-')) return 3;
                        return 99;
                    })->take(3);

                    // Zielordner
                    $storagePath = "uploads/images/locations/{$locationSlug}";
                    Storage::disk('public')->makeDirectory($storagePath);

                    $textPics = [];
                    $i = 1;

                    foreach ($sorted as $file) {
                        $cleanName = "city_image_{$i}." . strtolower($file->getExtension());
                        $cleanPath = "{$storagePath}/{$cleanName}";

                        Storage::disk('public')->put($cleanPath, File::get($file));
                        $textPics[] = url("storage/{$cleanPath}");
                        $i++;
                    }

                    DB::table('wwde_locations')
                        ->where('id', $locationRecord->id)
                        ->update([
                            'text_pic1' => $textPics[0] ?? null,
                            'text_pic2' => $textPics[1] ?? null,
                            'text_pic3' => $textPics[2] ?? null,
                            'updated_at' => now(),
                        ]);

                    $this->message .= "Hauptbilder aktualisiert: {$locationOriginal}<br>";
                }
            }
        }
    });

    $this->dispatch('importCompleted');
}

    public function render()
    {
        return view('livewire.backend.image-imports.location-main-img')
        ->layout('raadmin.layout.master');
    }
}
