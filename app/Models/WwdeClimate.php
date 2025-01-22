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
        return $this->belongsTo(WwdeLocation::class, 'location_id');
    }


    public static function predictFutureMonths($locationId, $baseMonthCount = 12)
    {
        // Hole alle vorhandenen Monate für die Location
        $existingData = self::where('location_id', $locationId)
            ->orderBy('month_id')
            ->get();

        if ($existingData->isEmpty()) {
            return null; // Keine Daten verfügbar
        }

        $futureData = [];
        $months = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        for ($i = 0; $i < $baseMonthCount; $i++) {
            $futureMonthIndex = ($existingData->first()->month_id + $i) % 12;
            $futureMonth = $months[$futureMonthIndex];

            if ($existingData->count() === 1) {
                // Nur ein Monat vorhanden: Nutze diesen als Basis
                $baseData = $existingData->first();
                $futureData[] = [
                    'month' => $futureMonth,
                    'daily_temperature' => $baseData->daily_temperature + rand(-2, 2),
                    'night_temperature' => $baseData->night_temperature + rand(-2, 2),
                    'sunshine_per_day' => max(0, $baseData->sunshine_per_day + rand(-1, 1)),
                    'humidity' => max(0, min(100, $baseData->humidity + rand(-5, 5))),
                    'rainy_days' => max(0, $baseData->rainy_days + rand(-1, 1)),
                ];
            } else {
                // Mehrere Monate vorhanden: Berechne Durchschnittswerte
                $avgDailyTemp = $existingData->avg('daily_temperature');
                $avgNightTemp = $existingData->avg('night_temperature');
                $avgSunshine = $existingData->avg('sunshine_per_day');
                $avgHumidity = $existingData->avg('humidity');
                $avgRainyDays = $existingData->avg('rainy_days');

                $futureData[] = [
                    'month' => $futureMonth,
                    'daily_temperature' => $avgDailyTemp + rand(-2, 2),
                    'night_temperature' => $avgNightTemp + rand(-2, 2),
                    'sunshine_per_day' => max(0, $avgSunshine + rand(-1, 1)),
                    'humidity' => max(0, min(100, $avgHumidity + rand(-5, 5))),
                    'rainy_days' => max(0, $avgRainyDays + rand(-1, 1)),
                ];
            }
        }

        return $futureData;
    }

}
