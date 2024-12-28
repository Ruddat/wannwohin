<?php

namespace App\Http\Controllers;

use App\Repositories\LocationRepository;
use App\Services\WeatherService;
use App\Models\HeaderContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        // Ladezeit-Start
        $startTime = microtime(true);

        // Top 10 Locations abrufen und im Cache speichern (für 30 Minuten)
        $topTenLocations = Cache::remember('top_ten_locations', 30 * 60, function () {
            return $this->locationRepository->getTopTenLocations()
                ->load(['country', 'country.continent']); // Eager Loading direkt hier
        });

        // Wetterdaten hinzufügen und im Cache speichern (für 30 Minuten)
        $topTenWithWeather = Cache::remember('top_ten_weather', 30 * 60, function () use ($topTenLocations) {
            return $this->weatherService->addWeatherToLocations($topTenLocations);
        });

        // Anzahl der Locations cachen (für 60 Minuten)
        $totalLocations = Cache::remember('total_finished_locations', 60 * 60, function () {
            return $this->locationRepository->getTotalFinishedLocations();
        });

        // Einen zufälligen oder den neuesten HeaderContent cachen (für 60 Minuten)
        $headerContent = Cache::remember('header_content_random', 60 * 60, function () {
            return HeaderContent::inRandomOrder()->first();
        });

        // HeaderContent validieren
        if (!$headerContent) {
            return view('pages.main.index', [
                'top_ten' => $topTenWithWeather,
                'total_locations' => $totalLocations,
                'panorama_location_picture' => null,
                'main_location_picture' => null,
                'panorama_location_text' => 'Default Text',
            ]);
        }

        // Bildpfade
        $bgImgPath = $headerContent->bg_img ? Storage::url($headerContent->bg_img) : null;
        $mainImgPath = $headerContent->main_img ? Storage::url($headerContent->main_img) : null;

        // Ladezeit-Ende
        $endTime = microtime(true);
        Log::info('IndexController Ladezeit: ' . ($endTime - $startTime) . ' Sekunden');

        return view('pages.main.index', [
            'top_ten' => $topTenWithWeather,
            'total_locations' => $totalLocations,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
            'panorama_location_text' => $headerContent->main_text ?? null,
        ]);
    }
}
