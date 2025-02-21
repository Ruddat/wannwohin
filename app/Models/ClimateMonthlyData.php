<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClimateMonthlyData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'climate_monthly_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location_id',
        'month',
        'month_name',
        'year',
        'temperature_avg',
        'temperature_max',
        'temperature_min',
        'precipitation',
        'snowfall',
        'sunshine_hours',
        'wind_direction',
        'wind_speed',
        'peak_wind_gust',
        'sea_level_pressure',
        'created_at',
        'updated_at',
    ];

    /**
     * Define the relationship to the location.
     */
    public function location()
    {
        return $this->belongsTo(WwdeLocation::class, 'location_id');
    }
}
