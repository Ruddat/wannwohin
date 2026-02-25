<?php

namespace App\Services;
use App\Models\WwdeLocation;

use App\Services\SeoService;

use Illuminate\Support\Facades\Cache;
use App\Services\LocationDetails\ParkService;
use App\Services\LocationDetails\GalleryService;
use App\Services\LocationDetails\TimezoneService;
use App\Services\LocationDetails\PriceTrendService;
use App\Services\LocationDetails\ClimateDataService;
use App\Services\LocationDetails\InspirationService;
use App\Services\LocationDetails\WeatherDataService;




class LocationPageService
{
public function get(string $slug): array
{
    $location = WwdeLocation::where('full_slug', $slug)
        ->with(['electric', 'country', 'country.continent'])
        ->firstOrFail();

    $climateService = app(ClimateDataService::class);

    if ($climateService->needsImport($location)) {
        return $this->buildFromModel($location);
    }

    return Cache::remember(
        "location_page_$slug",
        60 * 30,
        fn() => $this->buildFromModel($location)
    );
}

protected function buildFromModel(WwdeLocation $location): array
{
    $location = WwdeLocation::where('full_slug', $location->full_slug)
        ->with(['electric', 'country', 'country.continent'])
        ->firstOrFail();

    $climateService = app(ClimateDataService::class);

    // Automatischer Import wenn noch nichts vorhanden ist
    if ($climateService->needsImport($location)) {
        $climateService->import($location);
    }

    return [
        'location'     => $location,
        'weather'      => app(WeatherDataService::class)->get($location),
        'climate'      => $climateService->get($location),
        'gallery'      => app(GalleryService::class)->get($location),
        'parks'        => app(ParkService::class)->get($location),
        'seo'          => app(SeoService::class)->getSeoData($location),
        'inspiration'  => app(InspirationService::class)->get($location),
        'price_trend'  => app(PriceTrendService::class)->get($location),
        'time_info'    => app(TimezoneService::class)->get($location),
    ];
}
}
