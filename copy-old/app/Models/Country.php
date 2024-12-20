<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $primaryKey = "id";
    protected $casts = [
        'country_visum_needed' => 'boolean'
    ];

    public function electric()
    {
        return $this->hasOne(Electric::class, 'country_id', 'country_id');
    }

    public function continent()
    {
        return $this->belongsTo(Continent::class, 'continent_id', 'id');
    }

    public function location()
    {
        return $this->hasMany(Location::class, 'country_id', 'id');
    }

}
