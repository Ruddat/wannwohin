<?php

namespace App\Repositories;

interface LocationRepositoryInterface
{
    public function getTopTenLocations($status = 'active');

    public function getTotalFinishedLocations($status = 'active');

    public function getLocationsByStatus($status);

    public function getLocationsByTypeAndMonth($urlaubType, $monthName);
}
