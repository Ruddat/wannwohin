<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WwdeLocation;
use Illuminate\Support\Str;

class FixLocationAliases extends Command
{
    protected $signature = 'locations:fix-aliases {--dry-run : Nur anzeigen, keine Änderungen speichern}';
    protected $description = 'Bereinigt ALLE Aliases und macht sie eindeutig – auch bei NULL, leeren Werten oder kaputten Slugs.';

    public function handle()
    {
        $this->info("🚀 Starte Alias-Bereinigung...");

        // 🔥 Ohne Einschränkungen ALLE Datensätze holen – inklusive SoftDeletes
        $locations = WwdeLocation::withoutGlobalScopes()->get();

        $seen = [];
        $changes = [];

        foreach ($locations as $loc) {

            // ----------------------------
            // 1) BASIS ALIAS BESTIMMEN
            // ----------------------------
            $alias = $loc->alias;

            // Unicode-Trash entfernen & normalisieren
            $alias = trim((string)$alias);

            // slugifizieren
            $alias = Str::slug($alias);

            // Wenn alias leer → Titel als Basis
            if ($alias === '') {
                $alias = Str::slug((string)$loc->title);
            }

            // Wenn immer noch leer → fallback
            if ($alias === '') {
                $alias = 'loc-' . $loc->id;
            }

            $base = $alias;

            // ----------------------------
            // 2) EINDEUTIGKEIT GARANTIEREN
            // ----------------------------
            $counter = 2;
            while (isset($seen[$alias])) {
                $alias = $base . '-' . $counter;
                $counter++;
            }

            $seen[$alias] = true;

            // ----------------------------
            // 3) Nur speichern, wenn sich etwas ändert
            // ----------------------------
            if ($alias !== $loc->alias) {

                $changes[] = [
                    'id' => $loc->id,
                    'old' => $loc->alias,
                    'new' => $alias
                ];

                if (!$this->option('dry-run')) {
                    $loc->alias = $alias;
                    $loc->save();
                }
            }
        }

        // ----------------------------
        // 4) Output
        // ----------------------------
        if (empty($changes)) {
            $this->info("💚 Keine Änderungen notwendig. Alle Aliases sind eindeutig.");
            return Command::SUCCESS;
        }

        $this->table(['ID', 'Alter Alias', 'Neuer Alias'], $changes);

        if ($this->option('dry-run')) {
            $this->warn("⚠️ DRY-RUN – es wurde nichts gespeichert.");
        } else {
            $this->info("✅ Aliases erfolgreich bereinigt und gespeichert.");
        }

        return Command::SUCCESS;
    }
}
