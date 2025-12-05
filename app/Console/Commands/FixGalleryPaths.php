<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Models\ModLocationGalerie;
use Illuminate\Support\Facades\Storage;

class FixGalleryPaths extends Command
{
    protected $signature = 'locations:fix-gallery-paths';
    protected $description = 'Fixes gallery folder and file names by slugifying location names';

    public function handle()
    {
        $this->info("🚀 Fixing gallery paths...");

        $items = ModLocationGalerie::all();
        foreach ($items as $item) {

            $oldLocation = $item->location_name;
            $newLocation = Str::slug($oldLocation);

            // alter Ordner
            $oldFolder = "uploads/images/locations/" . $oldLocation;
            // neuer Ordner
            $newFolder = "uploads/images/locations/" . $newLocation;

            // fix Ordnername
            if (Storage::disk('public')->exists($oldFolder)) {
                Storage::disk('public')->move($oldFolder, $newFolder);
                $this->info("🟢 Folder renamed: $oldFolder → $newFolder");
            }

            // Bildpfad aktualisieren
            if ($item->image_path) {
                $filename = basename($item->image_path);
                $newPath = $newFolder . '/' . $filename;

                $item->image_path = $newPath;
                $item->save();

                $this->info("   ↳ Updated DB: " . $newPath);
            }
        }

        $this->info("✅ Done! All gallery paths converted to clean ASCII slugs.");
        return Command::SUCCESS;
    }
}
