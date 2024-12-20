<?php

namespace App\Repositories;

use App\Models\WwdeLocation;


class LocationRepository
{
    public function getTopTenLocations()
    {
        return WwdeLocation::with('country')
            ->where('finished', 1)
            ->get()
            ->sortByDesc(function ($location) {
                return $location->country->popularity ?? 0;
            })
            ->take(10);
    }

    public function getTotalFinishedLocations()
    {
        return WwdeLocation::where('finished', 1)->count();
    }
}
