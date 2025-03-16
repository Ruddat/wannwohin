<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AmusementParks extends Model
{

    use HasFactory;

    protected $fillable = [
        'name', 'country', 'location', 'latitude', 'longitude',
        'open_from', 'closed_from', 'external_id',
        'url', 'description', 'opening_hours' // Neue Felder hinzufügen
    ];

    protected $casts = [
        'opening_hours' => 'array', // JSON wird automatisch als Array geparst
    ];

}
