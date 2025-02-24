<?php

namespace App\Http\Controllers\Frontend\ContinentCountryTable;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ContinentRepository;

class ContinentController extends Controller
{
    protected $repository;

    public function __construct(ContinentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function showCountries($continentAlias)
    {
        $continent = $this->fetchContinent($continentAlias);
        $countries = $this->fetchCountries($continent->id);
        $images = $this->getContinentImages($continent);

        $this->storeHeaderData($continent, $images);

        return view('frondend.continent_and_countries.index', [
            'continent' => $continent,
            'countries' => $countries,
            'panorama_location_picture' => $images['bgImgPath'],
            'main_location_picture' => $images['mainImgPath'],
            'panorama_location_text' => $continent->continent_header_text ?? null,
        ]);
    }

    public function showLocations($continentAlias, $countryAlias)
    {
        $continent = $this->fetchContinent($continentAlias);
        $country = $this->fetchCountry($countryAlias);
        $locations = $this->fetchLocations($country->id);
        $images = $this->getContinentImages($continent);

        $this->storeHeaderData($continent, $images);

        return view('frondend.continent_and_countries.locations', [
            'continent' => $continent,
            'country' => $country,
            'locations' => $locations,
            'panorama_location_picture' => $images['bgImgPath'],
            'main_location_picture' => $images['mainImgPath'],
            'panorama_location_text' => $continent->continent_header_text ?? null,
        ]);
    }

    private function fetchContinent($alias): WwdeContinent
    {
        return Cache::remember("continent_{$alias}", 15 * 60, fn() =>
            WwdeContinent::where('alias', $alias)->firstOrFail()
        );
    }

    private function fetchCountries($continentId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("countries_{$continentId}", 15 * 60, fn() =>
            WwdeCountry::where('continent_id', $continentId)
                ->where('status', 'active')
                ->orderBy('title')
                ->get()
        );
    }

    private function fetchCountry($alias): WwdeCountry
    {
        return Cache::remember("country_{$alias}", 15 * 60, fn() =>
            WwdeCountry::where('alias', $alias)->with('travelWarning')->firstOrFail()
        );
    }

    private function fetchLocations($countryId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("locations_{$countryId}", 15 * 60, fn() =>
            WwdeLocation::where('country_id', $countryId)
                ->where('status', 'active')
                ->where('finished', '1')
                ->orderBy('title')
                ->get()
        );
    }

    private function getContinentImages(WwdeContinent $continent): array
    {
        $cacheKey = "continent_images_{$continent->alias}";
        return Cache::remember($cacheKey, 60 * 60, function () use ($continent) {
            $images = $this->repository->getAndStoreContinentImages($continent);

            if (!$images['bgImgPath'] || !$images['mainImgPath']) {
                $headerContent = Cache::remember('header_content_random', 60 * 60, fn() =>
                    \App\Models\HeaderContent::inRandomOrder()->first()
                );
                $images['bgImgPath'] = $images['bgImgPath'] ?? ($headerContent->bg_img ? Storage::url($headerContent->bg_img) : null);
                $images['mainImgPath'] = $images['mainImgPath'] ?? ($headerContent->main_img ? Storage::url($headerContent->main_img) : null);
            }

            return $images;
        });
    }

    private function storeHeaderData(WwdeContinent $continent, array $images): void
    {
        session([
            'headerData' => [
                'bgImgPath' => $images['bgImgPath'],
                'mainImgPath' => $images['mainImgPath'],
                'title' => $continent->title,
                'title_text' => $continent->continent_header_text,
            ]
        ]);
    }
}
