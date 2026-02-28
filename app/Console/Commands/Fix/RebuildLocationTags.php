<?php

namespace App\Console\Commands\Fix;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Tags\TagNormalizer;

class RebuildLocationTags extends Command
{
    protected $signature = 'locations:rebuild-tags {--dry-run}';
    protected $description = 'Rebuild dynamic tag system from mod_location_filters';

    public function handle(TagNormalizer $normalizer)
    {
        $dryRun = $this->option('dry-run');

        $filters = DB::table('mod_location_filters')
            ->where('is_active', 1)
            ->whereNotNull('category')
            ->get();

        if (!$dryRun) {
            DB::table('wwde_location_tag')->delete();
        }

        $inserted = 0;

        foreach ($filters as $filter) {

            $normalized = $normalizer->normalize($filter->category);

            $tag = DB::table('wwde_tags')
                ->where('normalized', $normalized)
                ->first();

            if (!$tag && !$dryRun) {

                $tagId = DB::table('wwde_tags')->insertGetId([
                    'group'      => $filter->text_type,
                    'title'      => trim($filter->category),
                    'slug'       => Str::slug($filter->category),
                    'normalized' => $normalized,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } else {
                $tagId = $tag->id ?? null;
            }

            if ($dryRun || !$tagId) {
                continue;
            }

            DB::table('wwde_location_tag')->insertOrIgnore([
                'location_id' => $filter->location_id,
                'tag_id'      => $tagId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $inserted++;
        }

        $this->info("Inserted {$inserted} pivot relations.");
    }
}
