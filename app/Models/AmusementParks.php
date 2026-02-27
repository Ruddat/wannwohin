<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AmusementParks extends Model
{

    use HasFactory;

    protected $fillable = [
        'name', 'type', 'country', 'location', 'latitude', 'longitude',
        'status', 'open_from', 'closed_from', 'continent', 'timezone', 'group_name', 'group_id', 'url', 'description',
        'opening_hours', 'video_url', 'logo_url', 'has_video', 'embed_code', 'slug', 'external_id', 'affiliate_enabled' // Neue Felder hinzufügen
    ];

    protected $casts = [
        'opening_hours' => 'array', // JSON wird automatisch als Array geparst
        'affiliate_enabled' => 'boolean', // Cast zu boolean
        'has_video' => 'boolean', // Cast zu boolean
        'open_from' => 'date',
        'closed_from' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'external_id' => 'string',
        ];


public function tags()
{
    return $this->belongsToMany(
        \App\Models\WwdeTag::class,
        'park_tag',
        'park_id',
        'tag_id'
    );
}


protected static function booted()
{
    static::creating(function ($park) {
        $park->slug = static::generateUniqueSlug($park);
    });

    static::updating(function ($park) {
        if ($park->isDirty('name') || $park->isDirty('country')) {
            $park->slug = static::generateUniqueSlug($park);
        }
    });
}

public static function generateUniqueSlug($park)
{
    $baseSlug = Str::slug($park->name);

    $slug = $baseSlug;
    $counter = 1;

    while (
        static::where('slug', $slug)
            ->when($park->id, fn ($q) => $q->where('id', '!=', $park->id))
            ->exists()
    ) {
        $slug = $baseSlug . '-' . Str::slug($park->country);
        if ($counter > 1) {
            $slug .= '-' . $counter;
        }
        $counter++;
    }

    return $slug;
}


}
