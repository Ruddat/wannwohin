<?php

namespace App\Services\LocationDetails;

use App\Models\WwdeLocation;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class TimezoneService
{
    public function get(WwdeLocation $location)
    {
        $timezone = $location->time_zone ?: config('app.timezone', 'UTC');

        try {
            $locationTime = new DateTime('now', new DateTimeZone($timezone));
        } catch (\Exception $e) {
            Log::error("Invalid timezone for location {$location->id}: ".$e->getMessage());
            $locationTime = new DateTime('now');
        }

        return [
            'current_time' => $locationTime->format('Y-m-d H:i:s'),
            'offset'       => $locationTime->getOffset() / 3600,
        ];
    }
}
