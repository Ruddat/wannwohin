<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Models\WwdeLocation;
use Illuminate\Console\Command;

class FixLocationAliases extends Command
{
    protected $signature = 'locations:fix-aliases
                            {--dry-run : Zeigt nur an, was geändert werden würde}';

    protected $description = 'Bereinigt doppelte oder fehlende Aliases in wwde_locations und macht sie eindeutig.';

    public function handle()
    {
        $this->info('🚀 Starte Alias-Bereinigung...');

        $locations = WwdeLocation::orderBy('id')->get();
        $seen = [];
        $changes = [];

        foreach ($locations as $loc) {

            // 1) Normalize alias (slug)
            $base = $loc->alias ? Str::slug($loc->alias) : Str::slug($loc->title);

            // 2) If slug becomes empty (rare case), fallback:
            if ($base === '') {
                $base = 'location-' . $loc->id;
            }

            // 3) Build unique alias
            if (!isset($seen[$base])) {
                $seen[$base] = 1;
                $newAlias = $base;
            } else {
                $seen[$base]++;
                $newAlias = $base . '-' . $seen[$base];
            }

            // If no change → skip
            if ($newAlias === $loc->alias) {
                continue;
            }

            $changes[] = [
                'id'       => $loc->id,
                'title'    => $loc->title,
                'old'      => $loc->alias,
                'new'      => $newAlias,
            ];

            if (! $this->option('dry-run')) {
                $loc->alias = $newAlias;
                $loc->save();
            }
        }

        if (empty($changes)) {
            $this->info("💚 Keine Änderungen notwendig. Alle Aliases sind bereits eindeutig.");
            return Command::SUCCESS;
        }

        $this->table(
            ['ID', 'Title', 'Alter Alias', 'Neuer Alias'],
            $changes
        );

        if ($this->option('dry-run')) {
            $this->warn("⚠️ DRY-RUN aktiviert – es wurde nichts gespeichert.");
        } else {
            $this->info("✅ Aliases erfolgreich bereinigt und gespeichert.");
        }

        return Command::SUCCESS;
    }
}
