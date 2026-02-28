<?php

namespace App\Services\Tags;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagResolver
{
    protected array $cache = [];

    public function resolve(string $rawCategory): ?int
    {
        $slug = $this->normalizeToSlug($rawCategory);

        if (!$slug) {
            return null;
        }

        // In-Memory Cache (Performance)
        if (array_key_exists($slug, $this->cache)) {
            return $this->cache[$slug];
        }

        // 1️⃣ Canonical Match (über slug, NICHT title)
        $tag = DB::table('wwde_tags')
            ->where('slug', $slug)
            ->first();

        if ($tag) {
            return $this->cache[$slug] = $tag->id;
        }

        // 2️⃣ Alias Match
        $alias = DB::table('wwde_tag_aliases')
            ->where('alias_slug', $slug)
            ->first();

        if ($alias) {
            return $this->cache[$slug] = $alias->tag_id;
        }

        // 3️⃣ Optional: Singular/Plural Fallback
        $pluralSlug = Str::slug(Str::plural(Str::title($rawCategory)));

        if ($pluralSlug !== $slug) {
            $tag = DB::table('wwde_tags')
                ->where('slug', $pluralSlug)
                ->first();

            if ($tag) {
                return $this->cache[$slug] = $tag->id;
            }
        }

        return $this->cache[$slug] = null;
    }

    protected function normalizeToSlug(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        // Normalisierung
        $value = strtolower($value);

        // Vereinheitlichung häufiger Varianten
        $value = str_replace([' und ', ' u. '], ' & ', $value);
        $value = str_replace(['&amp;'], '&', $value);

        return Str::slug($value);
    }
}
