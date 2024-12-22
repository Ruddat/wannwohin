<?php

namespace App\Http\Controllers;

use App\Repositories\LocationRepository;
use App\Services\WeatherService;
use App\Models\HeaderContent; // Import für Header-Inhalte

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
        // Top 10 Locations abrufen
        $topTenLocations = $this->locationRepository->getTopTenLocations();
        $topTenWithWeather = $this->weatherService->addWeatherToLocations($topTenLocations);

        // Anzahl der Locations
        $totalLocations = $this->locationRepository->getTotalFinishedLocations();

        // Header-Inhalte abrufen
        $headerContent = HeaderContent::first(); // Den ersten Eintrag aus der Tabelle 'header_contents' holen

//dd($headerContent);


return view('pages.main.index', [
    'top_ten' => $topTenWithWeather,
    'total_locations' => $totalLocations,
    'panorama_location_picture' => $headerContent->bg_img ?? null,
    'main_location_picture' => $headerContent->main_img ?? null,
    'panorama_location_text' => $headerContent->main_text ?? null,
]);
    }
}
