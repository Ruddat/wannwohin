<?php

namespace App\Models;

use App\Models\WwdeLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwdeLocationImages extends Model
{
    use HasFactory;

    protected $table = 'wwde_location_images';

    protected $fillable = [
        'location_id',
        'location_name', // Feld hinzugefügt
        'image_path',
        'image_caption',
        'image_type',
        'is_primary',
        'activity',
    ];

    // Beziehung zur Location
    public function location()
    {
        return $this->belongsTo(WwdeLocation::class);
    }
}
