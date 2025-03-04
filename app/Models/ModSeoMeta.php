<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModSeoMeta extends Model
{
    use HasFactory;

    protected $table = 'mod_seo_metas';

    protected $fillable = [
        'model_type',
        'model_id',
        'title',
        'description',
        'canonical',
        'image',
        'extra_meta',
        'keywords',
        'prevent_override',
    ];

    protected $casts = [
        'extra_meta' => 'array', // JSON-Daten automatisch als Array behandeln
        'keywords' => 'array',   // Keywords als Array casten (falls als JSON gespeichert)
        'prevent_override' => 'boolean', // Boolean casten
    ];

    public function seoable()
    {
        return $this->morphTo();
    }
}
