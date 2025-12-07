<?php

namespace App\Console\Commands;

use App\Models\WwdeLocation;
use Illuminate\Console\Command;

class GenerateLocationSlugs extends Command
{
    protected $signature = 'locations:generate-slugs';
    protected $description = 'Generate full_slug for all locations';

    public function handle()
    {
        WwdeLocation::with('country.continent')->chunk(500, function ($locations) {
            foreach ($locations as $l) {
                if ($l->country && $l->country->continent) {
                    $l->full_slug = strtolower(
                        $l->country->continent->alias . '/' .
                        $l->country->alias . '/' .
                        $l->alias
                    );
                    $l->save();
                }
            }
        });

        $this->info('Alle full_slugs erfolgreich generiert!');
    }
}
