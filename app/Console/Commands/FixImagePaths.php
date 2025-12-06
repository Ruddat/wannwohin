<?php

namespace App\Console\Commands;

use App\Helpers\PathSanitizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FixImagePaths extends Command
{
    protected $signature = 'fix:images';
    protected $description = 'Repariert ALLE Galerie-Ordner, Slugs, Dateinamen und Datenbank-Pfade.';

    public function handle()
    {
        $baseDir = storage_path('app/public/uploads/images/locations');

        if (!File::exists($baseDir)) {
            $this->error("Ordner nicht gefunden: {$baseDir}");
            return;
        }

        $locations = File::directories($baseDir);

        foreach ($locations as $locPath) {
            $origName = basename($locPath);
            $slug = PathSanitizer::locationSlug($origName);

            $newPath = dirname($locPath) . '/' . $slug;

            // Ordner umbenennen
            if ($locPath !== $newPath) {
                File::move($locPath, $newPath);
                $this->info("Ordner umbenannt: {$origName} -> {$slug}");
            }

            // Dateinamen korrigieren
            foreach (File::files($newPath) as $file) {
                $cleanName = PathSanitizer::filename($file->getFilename());
                $target = $newPath . '/' . $cleanName;

                if ($file->getFilename() !== $cleanName) {
                    File::move($file->getPathname(), $target);
                    $this->info("Datei umbenannt: {$file->getFilename()} -> {$cleanName}");
                }
            }

            // DB updaten
            DB::table('mod_location_galeries')
                ->where('location_name', $origName)
                ->update([
                    'location_name' => $origName,
                    'image_path' => DB::raw("
                        REPLACE(image_path, '{$origName}', '{$slug}')
                    ")
                ]);

            $this->info("DB aktualisiert für {$origName}");
        }

        $this->info("✨ Fertig! Alle Pfade, Slugs und Dateien sind nun ASCII-SAFE.");
    }
}
