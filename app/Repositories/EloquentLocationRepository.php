<?php

namespace App\Repositories;

use App\Models\Location;
use App\Repositories\LocationRepository;
use App\Repositories\EloquentLocationRepository;

class EloquentLocationRepository implements LocationRepository
{
    public function getTopTenLocations()
    {
        return Location::query()
            ->orderBy('search_count', 'desc')
            ->take(10)
            ->get();
    }

    public function getTotalFinishedLocations()
    {
        return Location::whereNotNull('finished_at')->count();
    }
}
