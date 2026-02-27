<?php

namespace App\Console\Commands\Fix;

use App\Models\AmusementParks;
use App\Models\WwdeCountry;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncParkCountries extends Command
{
    protected $signature = 'parks:sync-country';
    protected $description = 'Sync amusement_parks.country_id with wwde_countries';

    public function handle()
    {
        $parks = AmusementParks::whereNull('country_id')->get();

        if ($parks->isEmpty()) {
            $this->info('Alle Parks haben bereits eine country_id.');
            return Command::SUCCESS;
        }

        $countries = WwdeCountry::all();

        // Manuelles Fallback-Mapping (wichtig für englische Imports)
        $manualMap = [
            'germany' => 'deutschland',
            'netherlands' => 'niederlande',
            'the netherlands' => 'niederlande',
            'holland' => 'niederlande',
            'austria' => 'österreich',
            'switzerland' => 'schweiz',
            'italy' => 'italien',
            'spain' => 'spanien',
            'france' => 'frankreich',
            'united kingdom' => 'vereinigtes königreich',
            'uk' => 'vereinigtes königreich',
            'usa' => 'vereinigte staaten',
            'united states' => 'vereinigte staaten',

            // USA
            'united states' => 'usa',
            'united states of america' => 'usa',
            'us' => 'usa',

            // Kanada
            'canada' => 'kanada',

            // Belgien
            'belgium' => 'belgien',

            // Dänemark
            'denmark' => 'daenemark',

            // Vereinigtes Königreich
            'england' => 'vereinigtes königreich',
            'united kingdom' => 'vereinigtes königreich',
            'uk' => 'vereinigtes königreich',

            // Niederlande
            'netherlands' => 'niederlande',
            'holland' => 'niederlande',

            // Spanien
            'spain' => 'spanien',

            // Frankreich
            'france' => 'frankreich',

            // Italien
            'italy' => 'italien',

            // USA
            'united states' => 'usa',
            'united states of america' => 'usa',
            'us' => 'usa',

            // UK
            'england' => 'vereinigtes königreich',
            'uk' => 'vereinigtes königreich',
            'united kingdom' => 'vereinigtes königreich',
            'great britain' => 'vereinigtes königreich',
            'gb' => 'vereinigtes königreich',
            'Grossbritannien' => 'vereinigtes königreich',
            'England' => 'vereinigtes königreich',
            'grossbritannien' => 'vereinigtes königreich',

            // Germany
            'de-de' => 'deutschland',
            'germany' => 'deutschland',

            // Korea
            'south korea' => 'suedkorea',
            'korea' => 'suedkorea',

            // Mexico
            'mexico' => 'mexiko',

            // Brazil
            'brazil' => 'brasilien',

            // Poland
            'poland' => 'polen',

            // Sweden
            'sweden' => 'schweden',

            // Denmark
            'denmark' => 'daenemark',

            // Belgium
            'belgium' => 'belgien',

            // Canada
            'canada' => 'kanada',

            // Netherlands
            'netherlands' => 'niederlande',
            'holland' => 'niederlande',

            // France
            'france' => 'frankreich',

            // Spain
            'spain' => 'spanien',

            // Italy
            'italy' => 'italien',

            // Hong Kong
            'hong kong' => 'hongkong',



        ];

        $updated = 0;
        $unmatched = [];

        foreach ($parks as $park) {

            if (!$park->country) {
                $this->warn("✖ {$park->name} hat kein country Feld");
                $unmatched[] = $park->name;
                continue;
            }

            $normalized = Str::of($park->country)
                ->lower()
                ->trim()
                ->replace(['.', ',', '-', '_'], '')
                ->value();

            // Manual Mapping anwenden
            if (isset($manualMap[$normalized])) {
                $normalized = $manualMap[$normalized];
            }

            $match = $countries->first(function ($c) use ($normalized) {

                $title = Str::of($c->title)->lower()->trim()->value();
                $alias = Str::of($c->alias)->lower()->trim()->value();
                $iso3  = Str::of($c->country_iso_3)->lower()->trim()->value();
                $iso2  = Str::of($c->country_code)->lower()->trim()->value();

                return
                    $title === $normalized ||
                    $alias === $normalized ||
                    $iso3 === $normalized ||
                    $iso2 === $normalized ||
                    str_contains($title, $normalized) ||
                    str_contains($normalized, $title);
            });

            if ($match) {
                $park->country_id = $match->id;
                $park->save();

                $updated++;
                $this->line("✔ {$park->name} → {$match->title}");
            } else {
                $this->warn("✖ Kein Match für {$park->name} ({$park->country})");
                $unmatched[] = $park->name;
            }
        }

        $this->info("------------------------------------------------");
        $this->info("Fertig. {$updated} Parks aktualisiert.");

        if (!empty($unmatched)) {
            $this->warn("Nicht gematcht:");
            foreach ($unmatched as $name) {
                $this->line("- {$name}");
            }
        }

        return Command::SUCCESS;
    }
}
