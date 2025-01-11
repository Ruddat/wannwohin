<?php

namespace App\Models;

use App\Models\WwdeLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
