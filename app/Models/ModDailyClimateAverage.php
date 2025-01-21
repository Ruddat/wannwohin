<?php

namespace App\Models;

use App\Models\WwdeLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModDailyClimateAverage extends Model
{
    /** @use HasFactory<\Database\Factories\ModDailyClimateAverageFactory> */
    use HasFactory;


    protected $fillable = [
        'location_id',
        'date',
        'avg_daily_temperature',
        'avg_night_temperature',
        'avg_sunshine_per_day',
        'avg_humidity',
        'total_rainy_days',
        'avg_water_temperature',
    ];

    public function location()
    {
        return $this->belongsTo(WwdeLocation::class, 'location_id');
    }


}
