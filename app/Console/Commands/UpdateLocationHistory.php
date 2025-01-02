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
            DB::table('stat_location_search_histories')->updateOrInsert(
                [
                    'location_id' => $location->location_id,
                    'month' => now()->startOfMonth()->toDateString()
                ],
                [
                    'search_count' => DB::raw('search_count + ' . $location->search_count),
                    'updated_at' => now()
                ]
            );
        }

        // Löschen der alten Einträge
        DB::table('stat_top_ten_locations')->where('created_at', '<', now()->subWeeks(4))->delete();

        $this->info('Location history updated and old entries cleaned up.');
    }
}
