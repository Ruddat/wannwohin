<?php

namespace App\Http\Controllers\Frontend\ContinentCountryTable;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\HeaderContent;
use App\Models\WwdeContinent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ContinentRepository;

/**
 * Handles displaying continents and countries along with their respective locations.
 */
class ContinentController extends Controller
{
    /**
     * Show a list of countries within a given continent.
     */
    public function showCountries($continentAlias, ContinentRepository $repository)
    {
        // Kontinent abrufen oder Fehlerseite anzeigen
        $continent = WwdeContinent::where('alias', $continentAlias)->firstOrFail();

        // Länder des Kontinents abrufen (nur aktive)
        $countries = WwdeCountry::where('continent_id', $continent->id)
            ->where('status', 'active')
            ->orderBy('title')
            ->get();

        // Bilder abrufen (mit Fallback-Mechanismus)
        $images = $this->getContinentImages($continent, $repository);

        // Header-Daten in der Session speichern
        session()->put('headerData', [
            'bgImgPath' => $images['bgImgPath'],
            'mainImgPath' => $images['mainImgPath'],
            'headerContent' => [
                'main_text' => $continent->continent_header_text ?? 'Standardtext',
            ],
        ]);

        // Ansicht rendern
        return view('frondend.continent_and_countries.index', [
            'continent' => $continent,
            'countries' => $countries,
            'panorama_location_picture' => $images['bgImgPath'],
            'main_location_picture' => $images['mainImgPath'],
            'panorama_location_text' => $continent->continent_header_text ?? null,
        ]);
    }

    /**
     * Show locations within a country of a given continent.
     */
    public function showLocations($continentAlias, $countryAlias, ContinentRepository $repository)
    {
        // Kontinent & Land abrufen
        $continent = WwdeContinent::where('alias', $continentAlias)->firstOrFail();
        $country = WwdeCountry::where('alias', $countryAlias)->with('travelWarning')->firstOrFail();

        // Locations des Landes abrufen (nur aktive & fertige)
        $locations = WwdeLocation::where('country_id', $country->id)
            ->where('status', 'active')
            ->where('finished', '1')
            ->orderBy('title')
            ->get();

        // Bilder abrufen (mit Fallback-Mechanismus)
        $images = $this->getContinentImages($continent, $repository);

        // Header-Daten in der Session speichern
        session()->put('headerData', [
            'bgImgPath' => $images['bgImgPath'],
            'mainImgPath' => $images['mainImgPath'],
            'headerContent' => [
                'main_text' => $continent->continent_header_text ?? 'Standardtext',
            ],
        ]);

        // Ansicht rendern
        return view('frondend.continent_and_countries.locations', [
            'continent' => $continent,
            'country' => $country,
            'locations' => $locations,
            'panorama_location_picture' => $images['bgImgPath'],
            'main_location_picture' => $images['mainImgPath'],
            'panorama_location_text' => $continent->continent_header_text ?? null,
        ]);
    }

    /**
     * Holt Bilder für den Kontinent mit Fallback-Logik.
     */
    private function getContinentImages(WwdeContinent $continent, ContinentRepository $repository)
    {
        // Bilder über Repository abrufen (optimierte Methode)
        $images = $repository->getAndStoreContinentImages($continent);

        // Falls Bilder fehlen, aus der HeaderContent-Tabelle holen (Cache für Performance)
        if (!$images['bgImgPath'] || !$images['mainImgPath']) {
            $headerContent = Cache::remember('header_content_random', 3600, function () {
                return HeaderContent::inRandomOrder()->first();
            });

            $images['bgImgPath'] = $images['bgImgPath'] ?? ($headerContent->bg_img ? Storage::url($headerContent->bg_img) : null);
            $images['mainImgPath'] = $images['mainImgPath'] ?? ($headerContent->main_img ? Storage::url($headerContent->main_img) : null);
        }

        return $images;
    }
}
