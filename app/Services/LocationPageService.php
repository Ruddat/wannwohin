<?php

namespace App\Services;
use App\Models\WwdeLocation;

use App\Services\SeoService;

use Illuminate\Support\Facades\Cache;
use App\Services\LocationDetails\ParkService;
use App\Services\LocationDetails\GalleryService;
use App\Services\LocationDetails\TimezoneService;
use App\Services\LocationDetails\PriceTrendService;
use App\Services\LocationDetails\InspirationService;




class LocationPageService
{
public function get(string $slug): array
{
    $cacheKey = "location_page_$slug";

    // Wenn kein Klima vorhanden → kein Cache nutzen
    $location = WwdeLocation::where('full_slug', $slug)->first();
    $climateService = app(\App\Services\LocationDetails\ClimateDataService::class);

    if ($location && $climateService->needsImport($location)) {
        // Kein Cache → sofort importieren
        return $this->build($slug);
    }

//dd( $climateService->needsImport($location) );
    return Cache::remember($cacheKey, 60 * 30, fn() => $this->build($slug));
}




protected function build(string $slug): array
{
    $location = WwdeLocation::where('full_slug', $slug)
        ->with(['electric', 'country', 'country.continent'])
        ->firstOrFail();

    $climateService = app(\App\Services\LocationDetails\ClimateDataService::class);

    // Automatischer Import wenn noch nichts vorhanden ist
    if ($climateService->needsImport($location)) {
        $climateService->import($location);
    }

    return [
        'location'     => $location,
        'weather'      => app(\App\Services\LocationDetails\WeatherDataService::class)->get($location),
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
