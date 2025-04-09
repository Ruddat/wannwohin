<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AmusementParks;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConvertParkLogos extends Command
{
    protected $signature = 'logos:convert-parks';
    protected $description = 'Konvertiert bereits hochgeladene Parklogos in WebP und bringt sie auf ein einheitliches Format';

    public function handle(): int
    {
        $parks = AmusementParks::whereNotNull('logo_url')
            ->where('logo_url', 'like', '%parklogo_%')
            ->get();

        $this->info("Konvertiere {$parks->count()} Parklogos...");

        $manager = new ImageManager(new Driver());

        foreach ($parks as $park) {
            $originalPath = public_path($park->logo_url);
            if (!file_exists($originalPath)) {
                $this->warn("❌ Datei nicht gefunden: {$park->logo_url}");
                continue;
            }

            try {
                $image = $manager->read($originalPath);

                // Zielgröße (z. B. 280x180) mit Seitenverhältnis
                $targetWidth = 280;
                $targetHeight = 180;

                $image->resize($targetWidth, $targetHeight, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $canvas = $manager->create($targetWidth, $targetHeight)->fill('rgba(255,255,255,0)');
                $canvas->place($image, 'center');

                $newFileName = 'img/parklogos/parklogo_' . Str::uuid() . '.webp';
                Storage::disk('public')->put($newFileName, (string) $canvas->toWebp(quality: 85));

                $park->logo_url = '/storage/' . $newFileName;
                $park->save();

                $this->info("✅ Konvertiert: {$park->name}");
            } catch (\Exception $e) {
                $this->error("⚠️ Fehler bei {$park->name}: " . $e->getMessage());
            }
        }

        $this->info("Alle Logos verarbeitet.");

        return self::SUCCESS;
    }
}
