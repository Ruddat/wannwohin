<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AmusementParks extends Model
{

    use HasFactory;

    protected $fillable = [
        'name', 'type', 'country', 'location', 'latitude', 'longitude',
        'open_from', 'closed_from', 'external_id', 'continent', 'timezone', 'group_name', 'group_id', 'url', 'description', 'opening_hours', 'video_url', 'logo_url', 'has_video', 'embed_code' // Neue Felder hinzufügen
    ];

    protected $casts = [
        'opening_hours' => 'array', // JSON wird automatisch als Array geparst
    ];

}
