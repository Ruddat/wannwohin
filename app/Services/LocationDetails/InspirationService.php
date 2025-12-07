<?php

namespace App\Services\LocationDetails;


use App\Models\ModLocationFilter;
use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Cache;

class InspirationService
{
    public function get(WwdeLocation $location)
    {
        $cacheKey = "inspiration_{$location->id}";

        return Cache::remember($cacheKey, 60 * 60 * 6, function () use ($location) {
            return ModLocationFilter::where('location_id', $location->id)
                ->where('is_active', 1)
                ->get()
                ->groupBy('text_type')
                ->toArray();
        });
    }
}
