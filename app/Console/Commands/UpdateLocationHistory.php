<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateLocationHistory extends Command
{
    protected $signature = 'locations:update-history';
    protected $description = 'Update and archive top ten locations and clean up old entries';

    public function handle()
    {
        // Archivieren der Top-Ten-Daten
        $topLocations = DB::table('stat_top_ten_locations')->get();

        foreach ($topLocations as $location) {
            // Update oder Insert in stat_location_search_histories
            DB::table('stat_location_search_histories')->updateOrInsert(
                [
                    'location_id' => $location->location_id,
                    'month' => now()->format('Y-m'), // Monat im Format YYYY-MM
                ],
                [
                    'search_count' => DB::raw('search_count + ' . $location->search_count),
                    'updated_at' => now(),
                ]
            );
        }

        // Löschen aller älteren Einträge aus stat_top_ten_locations
        DB::table('stat_top_ten_locations')
            ->where('updated_at', '<', now()->subMonth()) // Älter als ein Monat
            ->delete();

        // Info ausgeben
        $this->info('Location history archived, and old entries removed.');
    }
}
