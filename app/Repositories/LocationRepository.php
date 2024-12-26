<?php

namespace App\Repositories;

use App\Models\WwdeLocation;

class LocationRepository
{
    public function getTopTenLocations($status = 'active')
    {
        return WwdeLocation::with('country')
            ->where('finished', 1)
            ->where('status', $status) // Status-Abfrage hinzufügen
            ->get()
            ->sortByDesc(function ($location) {
                return $location->country->popularity ?? 0;
            })
            ->take(10);
    }

    public function getTotalFinishedLocations($status = 'active')
    {
        return WwdeLocation::where('finished', 1)
            ->where('status', $status) // Status-Abfrage hinzufügen
            ->count();
    }

    public function getLocationsByStatus($status)
    {
        return WwdeLocation::where('status', $status)->get();
    }
}
