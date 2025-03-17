<?php

namespace App\Http\Controllers\Frontend\ContinentCountryTable;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Services\SeoService;
use App\Models\WwdeContinent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ContinentRepository;

class ContinentController extends Controller
{
    protected $repository;
    protected $seoService;

    private const CACHE_TTL_SHORT = 15 * 60;  // 15 Minuten
    private const CACHE_TTL_LONG = 60 * 60;   // 1 Stunde

    public function __construct(ContinentRepository $repository, SeoService $seoService)
    {
        $this->repository = $repository;
        $this->seoService = $seoService;
    }

    public function showCountries(string $continentAlias): \Illuminate\View\View
    {
        $continent = $this->fetchContinent($continentAlias);
        $countries = $this->fetchCountries($continent->id);
        $images = $this->getContinentImages($continent);

        $this->storeHeaderData($continent, $continent, $images);

        $seo = $this->seoService->getSeoData($continent);

        return view('frondend.continent_and_countries.index', [
            'continent' => $continent,
            'countries' => $countries,
            'panorama_location_picture' => $images['bgImgPath'],
            'main_location_picture' => $images['mainImgPath'],
            'panorama_location_text' => $continent->continent_header_text ?? null,
            'seo' => $seo,
        ]);
    }

    public function showLocations(string $continentAlias, string $countryAlias): \Illuminate\View\View
    {
        $continent = $this->fetchContinent($continentAlias);
        $country = $this->fetchCountry($countryAlias);
        $locations = $this->fetchLocations($country->id);
        $images = $this->getCountryImages($country, $continent);

        $this->storeHeaderData($country, $continent, $images);

        $seo = $this->seoService->getSeoData($country);

        return view('frondend.continent_and_countries.locations', [
            'continent' => $continent,
            'country' => $country,
            'locations' => $locations,
            'panorama_location_picture' => $images['bgImgPath'],
            'main_location_picture' => $images['mainImgPath'],
            'panorama_location_text' => $country->country_header_text ?? $continent->continent_header_text ?? null,
            'seo' => $seo,
        ])->with('h1', "Reiseziele in {$country->title} 2025: {$continent->title}");
    }

    private function fetchContinent(string $alias): WwdeContinent
    {
        try {
            return Cache::remember("continent_{$alias}", self::CACHE_TTL_SHORT, fn() =>
                WwdeContinent::where('alias', $alias)->firstOrFail()
            );
        } catch (\Exception $e) {
            abort(404, 'Kontinent nicht gefunden');
        }
    }

    private function fetchCountries(int $continentId): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Cache::remember("countries_{$continentId}", self::CACHE_TTL_SHORT, fn() =>
                WwdeCountry::where('continent_id', $continentId)
                    ->where('status', 'active')
                    ->whereHas('locations', function ($query) {
                        $query->where('status', 'active')
                              ->where('finished', '1');
                    })
                    ->orderBy('title')
                    ->get()
            );
        } catch (\Exception $e) {
            abort(500, 'Fehler beim Laden der Länder');
        }
    }

    private function fetchCountry(string $alias): WwdeCountry
    {
        try {
            return Cache::remember("country_{$alias}", self::CACHE_TTL_SHORT, fn() =>
                WwdeCountry::where('alias', $alias)
                    ->with('travelWarning')
                    ->firstOrFail()
            );
        } catch (\Exception $e) {
            abort(404, 'Land nicht gefunden');
        }
    }

    private function fetchLocations(int $countryId): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Cache::remember("locations_{$countryId}", self::CACHE_TTL_SHORT, fn() =>
                WwdeLocation::where('country_id', $countryId)
                    ->where('status', 'active')
                    ->where('finished', '1')
                    ->orderBy('title')
                    ->get()
            );
        } catch (\Exception $e) {
            abort(500, 'Fehler beim Laden der Reiseziele');
        }
    }

    private function getContinentImages(WwdeContinent $continent): array
    {
        $cacheKey = "continent_images_{$continent->alias}";
        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($continent) {
            $images = $this->repository->getAndStoreContinentImages($continent);

            if (!$images['bgImgPath'] || !$images['mainImgPath']) {
                $headerContent = Cache::remember('header_content_random', self::CACHE_TTL_LONG, fn() =>
                    \App\Models\HeaderContent::inRandomOrder()->first()
                );
                $images['bgImgPath'] = $images['bgImgPath'] ?? ($headerContent?->bg_img ? Storage::url($headerContent->bg_img) : null);
                $images['mainImgPath'] = $images['mainImgPath'] ?? ($headerContent?->main_img ? Storage::url($headerContent->main_img) : null);
            }

            return $images;
        });
    }

    private function getCountryImages(WwdeCountry $country, WwdeContinent $continent): array
    {
        $cacheKey = "country_images_{$country->alias}";
        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($country, $continent) {
            $images = [
                'bgImgPath' => null,
                'mainImgPath' => null,
            ];

            if ($country->panorama_image_path && Storage::exists($country->panorama_image_path)) {
                $images['bgImgPath'] = Storage::url($country->panorama_image_path);
            }
            if ($country->header_image_path && Storage::exists($country->header_image_path)) {
                $images['mainImgPath'] = Storage::url($country->header_image_path);
            }

            if (!$images['bgImgPath'] || !$images['mainImgPath']) {
                $continentImages = $this->getContinentImages($continent);
                $images['bgImgPath'] = $images['bgImgPath'] ?? $continentImages['bgImgPath'];
                $images['mainImgPath'] = $images['mainImgPath'] ?? $continentImages['mainImgPath'];
            }

            if (!$images['bgImgPath'] || !$images['mainImgPath']) {
                $headerContent = Cache::remember('header_content_random', self::CACHE_TTL_LONG, fn() =>
                    \App\Models\HeaderContent::inRandomOrder()->first()
                );
                $images['bgImgPath'] = $images['bgImgPath'] ?? ($headerContent?->bg_img ? Storage::url($headerContent->bg_img) : null);
                $images['mainImgPath'] = $images['mainImgPath'] ?? ($headerContent?->main_img ? Storage::url($headerContent->main_img) : null);
            }

            return $images;
        });
    }

    private function storeHeaderData(object $entity, WwdeContinent $continent, array $images, array $additionalData = []): void
    {
        session([
            'headerData' => array_merge([
                'bgImgPath' => $images['bgImgPath'] ?? null,
                'mainImgPath' => $images['mainImgPath'] ?? null,
                'title' => $this->cleanEditorContent(
                    !empty($entity->country_headert_titel) ? $entity->country_headert_titel :
                    (!empty($entity->continent_headert_titel) ? $entity->continent_headert_titel : $entity->title)
                ),
                'title_text' => $this->cleanEditorContent($entity->country_header_text)
                    ?? $this->cleanEditorContent($continent->continent_header_text),
            ], $additionalData)
        ]);
    }

    private function cleanEditorContent(?string $content): ?string
    {
        if (is_null($content)) {
            return null;
        }

        $cleaned = trim(strip_tags($content, '<img><a>'));

        $emptyPatterns = [
            '/^<p>\s*<br\s*\/?>\s*<\/p>$/i',
            '/^<p>\s* \s*<\/p>$/i',
            '/^\s*$/',
        ];

        foreach ($emptyPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return null;
            }
        }

        return empty($cleaned) ? null : $content;
    }
}
