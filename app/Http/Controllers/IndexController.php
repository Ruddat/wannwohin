<?php

namespace App\Http\Controllers;

use App\Repositories\LocationRepository;
use App\Services\WeatherService;
use App\Models\HeaderContent;
use Illuminate\Support\Facades\Storage;

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

        // Einen zufälligen oder den neuesten HeaderContent abrufen
        $headerContent = HeaderContent::inRandomOrder()->first(); // Zufällig
        // Alternativ: $headerContent = HeaderContent::latest()->first(); // Neuester

        // Überprüfen, ob HeaderContent verfügbar ist
        if (!$headerContent) {
            return view('pages.main.index', [
                'top_ten' => $topTenWithWeather,
                'total_locations' => $totalLocations,
                'panorama_location_picture' => null,
                'main_location_picture' => null,
                'panorama_location_text' => 'Default Text',
            ]);
        }

        // Bildpfade anpassen
        $bgImgPath = $headerContent->bg_img ? Storage::url($headerContent->bg_img) : null;
        $mainImgPath = $headerContent->main_img ? Storage::url($headerContent->main_img) : null;

        return view('pages.main.index', [
            'top_ten' => $topTenWithWeather,
            'total_locations' => $totalLocations,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
            'panorama_location_text' => $headerContent->main_text ?? null,
        ]);
    }
}
