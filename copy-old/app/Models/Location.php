<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $primaryKey = "id";
    protected $casts = [
        'best_traveltime_json'  => 'json',
        'list_island'           => 'boolean',
        'list_citytravel'       => 'boolean',
        'list_swim'             => 'boolean',
        'list_surfing'          => 'boolean',
        'list_diving'           => 'boolean',
        'list_wintersport'      => 'boolean',
        'list_walking'          => 'boolean',
        'list_biking'           => 'boolean',
        'list_climbing'         => 'boolean',
    ];

    public function continent()
    {
        return $this->belongsTo(Continent::class, 'continent_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function electric()
    {
        return $this->belongsTo(Electric::class, 'country_id', 'id');
    }

    public function climateAverage()
    {
        return $this->hasOne(AvgLocation::class, 'location_id', 'id');
    }

    public function climate()
    {
        return $this->hasMany(Climate::class, 'location_id', 'id');
    }

    public function climateMonth($month)
    {
        return $this->climate()->where('month_id', $month)->first();
    }

}
