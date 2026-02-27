<?php

namespace App\Console\Commands\Fix;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RebuildLocationTags extends Command
{
    protected $signature = 'locations:rebuild-tags {--dry-run}';
    protected $description = 'Rebuild wwde_tags and wwde_location_tag from mod_location_filters';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Collecting distinct filter categories...');

        $filters = DB::table('mod_location_filters')
            ->where('is_active', 1)
            ->whereNotNull('category')
            ->select('text_type', 'category')
            ->distinct()
            ->get();

        $this->info('Found '.$filters->count().' unique tag combinations.');

        if (!$dryRun) {
            DB::table('wwde_tags')->truncate();
        }

        foreach ($filters as $filter) {

            $group = Str::slug($filter->text_type);
            $title = trim($filter->category);
            $slug  = Str::slug($title);

            if ($dryRun) {
                $this->line("Would create: {$group} / {$slug}");
                continue;
            }

            DB::table('wwde_tags')->insertOrIgnore([
                'group'      => $group,
                'slug'       => $slug,
                'title'      => $title,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('Tags rebuilt.');

        // -------------------------
        // Pivot neu aufbauen
        // -------------------------

        if (!$dryRun) {
            DB::table('wwde_location_tag')->truncate();
        }

        $this->info('Rebuilding pivot table...');

        $relations = DB::table('mod_location_filters as f')
            ->join('wwde_tags as t', function ($join) {
                $join->on(DB::raw('LOWER(TRIM(f.category))'), '=', DB::raw('LOWER(t.title)'));
            })
            ->where('f.is_active', 1)
            ->select('f.location_id', 't.id as tag_id')
            ->distinct()
            ->get();

        $this->info('Found '.$relations->count().' pivot relations.');

        if ($dryRun) {
            $this->warn('Dry run finished.');
            return;
        }

        foreach ($relations as $row) {
            DB::table('wwde_location_tag')->insertOrIgnore([
                'location_id' => $row->location_id,
                'tag_id'      => $row->tag_id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->info('Pivot rebuilt successfully.');
    }
}
