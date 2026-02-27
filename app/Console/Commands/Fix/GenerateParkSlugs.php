<?php

namespace App\Console\Commands\Fix;

use App\Models\AmusementParks;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateParkSlugs extends Command
{
    protected $signature = 'parks:generate-slugs {--force}';
    protected $description = 'Generate missing slugs for amusement_parks';

    public function handle()
    {
        $parks = AmusementParks::orderBy('id')->get();

        if ($parks->isEmpty()) {
            $this->info('Keine Parks gefunden.');
            return Command::SUCCESS;
        }

        $slugMap = [];
        $updated = 0;

        foreach ($parks as $park) {

            if (!$park->name) {
                $this->warn("Park ID {$park->id} hat keinen Namen.");
                continue;
            }

            $baseSlug = Str::slug($park->name);

            if (!$baseSlug) {
                $this->warn("Slug konnte nicht generiert werden für {$park->name}");
                continue;
            }

            if (!isset($slugMap[$baseSlug])) {
                $slugMap[$baseSlug] = 0;
            }

            $slugMap[$baseSlug]++;

            $slug = $baseSlug;

            if ($slugMap[$baseSlug] > 1) {
                $slug .= '-' . $slugMap[$baseSlug];
            }

            if ($park->slug !== $slug || $this->option('force')) {
                $park->slug = $slug;
                $park->saveQuietly();
                $updated++;
                $this->line("✔ {$park->name} → {$slug}");
            }
        }

        $this->info("------------------------------------------------");
        $this->info("Fertig. {$updated} Slugs aktualisiert.");

        return Command::SUCCESS;
    }
}
