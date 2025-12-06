<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupGalleryMissingExtensions extends Command
{
    protected $signature = 'gallery:cleanup-missing-extensions';
    protected $description = 'Löscht alle Galerie-Bilder ohne Dateiendung aus DB & Storage';

    public function handle()
    {
        $this->info("🔍 Suche nach Bildern ohne Dateiendung...");

        $images = DB::table('mod_location_galeries')->get();
        $deletedCount = 0;

        foreach ($images as $img) {

            $path = $img->image_path ?? '';

            // Prüfen: keine Dateiendung = löschen
            if (!preg_match('/\.(jpg|jpeg|png|webp)$/i', $path)) {

                $this->warn("⛔ Ungültiger Eintrag gefunden: {$path}");

                // Datei im Storage löschen, falls sie existiert
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $this->info("   🗑 Datei gelöscht: {$path}");
                }

                // DB-Eintrag löschen
                DB::table('mod_location_galeries')->where('id', $img->id)->delete();
                $this->warn("   🗑 DB-Eintrag gelöscht (ID {$img->id})");

                $deletedCount++;
            }
        }

        $this->info("✅ Fertig! {$deletedCount} ungültige Einträge gelöscht.");

        return Command::SUCCESS;
    }
}