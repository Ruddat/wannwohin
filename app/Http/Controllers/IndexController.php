<?php

namespace App\Http\Controllers;

use App\Repositories\LocationRepository;
use App\Services\WeatherService;

class IndexController extends Controller
{
    protected $locationRepository;
    protected $weatherService;

    public function __construct(LocationRepository $locationRepository, WeatherService $weatherService)
    {
        $this->locationRepository = $locationRepository;
        $this->weatherService = $weatherService;
    }

    public function __invoke()
    {
        $topTenLocations = $this->locationRepository->getTopTenLocations();
        $topTenWithWeather = $this->weatherService->addWeatherToLocations($topTenLocations);
        $totalLocations = $this->locationRepository->getTotalFinishedLocations();

        return view('pages.main.index', [
            'top_ten' => $topTenWithWeather,
            'total_locations' => $totalLocations,
        ]);
    }
}
