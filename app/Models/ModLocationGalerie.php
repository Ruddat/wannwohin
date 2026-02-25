<?php

namespace App\Models;

use App\Models\WwdeLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ModLocationGalerie extends Model
{
    /** @use HasFactory<\Database\Factories\ModLocationGalerieFactory> */
    use HasFactory;

    protected $table = 'mod_location_galeries';

    protected $fillable = [
        'location_id',
        'location_name',
        'image_path',
        'image_caption',
        'activity',
        'description',
        'image_hash',
        'image_type',
        'is_primary',

    ];

    public function location()
    {
        return $this->belongsTo(WwdeLocation::class, 'location_id');
    }

protected static function booted()
{
    static::saved(function ($model) {
        Cache::forget("gallery_{$model->location_id}");
    });

    static::deleted(function ($model) {
        Cache::forget("gallery_{$model->location_id}");
    });
}


}
