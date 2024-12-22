<?php

namespace App\Http\Controllers;

use App\Repositories\LocationRepository;
use App\Services\WeatherService;
use App\Models\HeaderContent;

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
        $headerContents = HeaderContent::all();

        // Überprüfen, ob Header-Inhalte verfügbar sind
        if ($headerContents->isEmpty()) {
            return view('pages.main.index', [
                'top_ten' => $topTenWithWeather,
                'total_locations' => $totalLocations,
                'panorama_location_picture' => null,
                'main_location_picture' => null,
                'panorama_location_text' => 'Default Text',
            ]);
        }

        // Auswahl eines Header-Inhalts basierend auf der aktuellen Zeit
        $currentMinute = now()->minute;
        $headerIndex = intdiv($currentMinute, 15) % $headerContents->count(); // Index berechnen
        $headerContent = $headerContents->get($headerIndex);

        return view('pages.main.index', [
            'top_ten' => $topTenWithWeather,
            'total_locations' => $totalLocations,
            'panorama_location_picture' => $headerContent->bg_img ?? null,
            'main_location_picture' => $headerContent->main_img ?? null,
            'panorama_location_text' => $headerContent->main_text ?? null,
        ]);
    }
}
