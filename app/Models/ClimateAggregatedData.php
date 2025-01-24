<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClimateAggregatedData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'climate_aggregated_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location_id',
        'month',
        'avg_daily_temp',
        'min_temp',
        'max_temp',
        'avg_humidity',
        'sunshine_hours',
        'total_rainy_days',
    ];

    /**
     * Define the relationship to the location.
     */
    public function location()
    {
        return $this->belongsTo(WwdeLocation::class);
    }
}
