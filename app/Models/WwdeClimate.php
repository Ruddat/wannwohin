<?php

namespace App\Models;

use App\Models\WwdeLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwdeClimate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wwde_climates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = [];
    

    // Beziehung zur Location
    public function location()
    {
        return $this->belongsTo(WwdeLocation::class, 'location_id', 'id');
    }

}
