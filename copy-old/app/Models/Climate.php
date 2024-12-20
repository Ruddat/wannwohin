<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Climate extends Model
{
    use HasFactory;

    protected $table = 'climates';

    protected $guarded = array('id');
    // in use
    public static function getAllClimateData($location_id)
    {
        $averages = self::where('climates.location_id', $location_id)
            ->select(DB::raw('MAX(wwde_climates.daily_temperature) as daily_temp_max,
                                    MAX(wwde_climates.night_temperature) as night_temp_max,
                                    MAX(wwde_climates.water_temperature) as water_temp_max,
                                    AVG(wwde_climates.water_temperature) as water_temp_avg,
                                    AVG(wwde_climates.daily_temperature) as daily_temp_avg,
                                    AVG(wwde_climates.sunshine_per_day) as sun_shine_avg,
                                    MAX(wwde_climates.sunshine_per_day) as sun_shine_max,
                                    AVG(wwde_climates.rainy_days) as rainy_days_avg,
                                    Max(wwde_climates.rainy_days) as rainy_days_max,
                                    AVG(wwde_climates.night_temperature) as night_temp_avg'))
            ->first();


        $climate =  self::orderBy('month_id', 'asc')
            ->where('climates.location_id', $location_id)
            ->select(
                DB::raw("$averages->water_temp_avg as water_temperature_avg"),
                DB::raw("$averages->daily_temp_avg as daily_temperature_avg"),
                DB::raw("$averages->night_temp_avg as night_temperature_avg"),

                DB::raw("$averages->daily_temp_max as daily_temperature_max"),
                DB::raw("$averages->night_temp_max as night_temperature_max"),
                DB::raw("$averages->water_temp_max as water_temperature_max"),

                DB::raw("$averages->sun_shine_avg as sun_shine_avg"),
                DB::raw("$averages->sun_shine_max as sun_shine_max"),

                DB::raw("$averages->rainy_days_avg as rainy_days_avg"),
                DB::raw("$averages->rainy_days_max as rainy_days_max"),
                'climates.month_id',
                'climates.month',
                'climates.daily_temperature',
                'climates.night_temperature',
                'climates.sunshine_per_day',
                'climates.humidity',
                'climates.rainy_days',
                'climates.water_temperature')
            ->get();

        return cache()->rememberForever('climate.'.$location_id, function () use($climate){
            return $climate;
        });
    }
}
