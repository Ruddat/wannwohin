<?php

namespace App\Models;

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
    protected $fillable = [
        'location_id',
        'month_id',
        'month',
        'daily_temperature',
        'night_temperature',
        'sunshine_per_day',
        'humidity',
        'rainy_days',
        'water_temperature',
    ];

        /**
     * Relationship with Location.
     */
    public function location()
    {
        return $this->belongsTo(WwdeLocation::class);
    }
    
}
