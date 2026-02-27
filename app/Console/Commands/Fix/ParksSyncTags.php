<?php

namespace App\Console\Commands\Fix;

use App\Models\AmusementParks;
use App\Models\WwdeTag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ParksSyncTags extends Command
{
    protected $signature = 'parks:sync-tags {--dry}';
    protected $description = 'Synchronisiert Park-Tags automatisch anhand einfacher Klassifizierung';

    public function handle()
    {
        $this->info('Starte Park-Tag-Synchronisierung...');

        $parks = AmusementParks::with('tags')->orderBy('id')->get();

        if ($parks->isEmpty()) {
            $this->warn('Keine Parks gefunden.');
            return Command::SUCCESS;
        }

        $parkTags = WwdeTag::where('group', 'parks')
            ->pluck('id', 'slug');

        $updated = 0;

        foreach ($parks as $park) {

            $slugs = $this->classifyPark($park);

            $tagIds = collect($slugs)
                ->map(fn($slug) => $parkTags[$slug] ?? null)
                ->filter()
                ->values()
                ->toArray();

            if (empty($tagIds)) {
                $this->warn("⚠ Kein Tag gefunden für {$park->name}");
                continue;
            }

            if (!$this->option('dry')) {
                $park->tags()->sync($tagIds);
            }

            $this->line("✔ {$park->name} → " . implode(',', $slugs));

            $updated++;
        }

        $this->info("------------------------------------------------");
        $this->info("Fertig. {$updated} Parks synchronisiert.");

        return Command::SUCCESS;
    }

    /**
     * Deterministische Park-Klassifizierung
     */
    private function classifyPark(AmusementParks $park): array
    {
        $name  = Str::lower($park->name);
        $group = Str::lower($park->group_name ?? '');

        $tags = [];

        // ------------------------
        // Zoo / Tierpark
        // ------------------------
        if (
            str_contains($name, 'zoo') ||
            str_contains($name, 'safari') ||
            str_contains($name, 'wildlife') ||
            str_contains($name, 'bird')
        ) {
            $tags[] = 'zoo';
        }

        // ------------------------
        // Wasserpark
        // ------------------------
        if (
            str_contains($name, 'water') ||
            str_contains($name, 'aqua') ||
            str_contains($group, 'water') ||
            str_contains($name, 'ocean')
        ) {
            $tags[] = 'wasserpark';
        }

        // ------------------------
        // Miniaturpark
        // ------------------------
        if (str_contains($name, 'miniatur')) {
            $tags[] = 'miniaturpark';
        }

        // ------------------------
        // Abenteuerpark
        // ------------------------
        if (
            str_contains($name, 'adventure') ||
            str_contains($name, 'abenteuer')
        ) {
            $tags[] = 'abenteuerpark';
        }

        // ------------------------
        // Familienpark
        // ------------------------
        if (
            str_contains($name, 'family') ||
            str_contains($name, 'famil')
        ) {
            $tags[] = 'familienpark';
        }

        // ------------------------
        // Fallback
        // ------------------------
        if (empty($tags)) {
            $tags[] = 'vergnuegungspark';
        }

        return array_unique($tags);
    }
}
