<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WwdeTag extends Model
{
    protected $fillable = ['group','slug','title'];

    public function locations()
    {
        return $this->belongsToMany(\App\Models\WwdeLocation::class, 'wwde_location_tag', 'tag_id', 'location_id')
            ->withTimestamps();
    }


public function parks()
{
    return $this->belongsToMany(
        AmusementParks::class,
        'park_tag',
        'tag_id',
        'park_id'
    );
}

}
