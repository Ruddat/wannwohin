<?php

namespace App\Http\Controllers\Frontend\ContinentCountryTable;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\HeaderContent;
use App\Models\WwdeContinent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Handles displaying continents and countries along with their respective locations.
 *
 * This controller provides endpoints to fetch and display countries for a given continent
 * and locations for a specific country within a continent. It also includes logic for
 * handling fallback images and caching.
 */
class ContinentController extends Controller
{
    /**
     * Show a list of countries within a given continent.
     *
     * @param string $continentAlias The alias of the continent (e.g., "europe").
     * @return \Illuminate\View\View
     *
     * This function retrieves a continent using its alias and fetches all the countries
     * associated with it. If the continent does not have defined images, it uses fallback
     * images stored in the `HeaderContent` table.
     *
     * The resulting data is passed to the `frondend.continent_and_countries.index` view.
     */

    public function showCountries($continentAlias)
    {
        // Finde den Kontinent basierend auf dem Alias
        $continent = WwdeContinent::where('alias', $continentAlias)->firstOrFail();

        // Länder des Kontinents abrufen
        $countries = WwdeCountry::where('continent_id', $continent->id)->get();

        // Prüfe, ob der Kontinent benutzerdefinierte Bilder hat
        $bgImgPath = $continent->image1_path ?? null;
        $mainImgPath = $continent->image2_path ?? null;

        // Falls keine Bilder im Kontinent definiert sind, verwende HeaderContent
        if (!$bgImgPath || !$mainImgPath) {
            $headerContent = Cache::remember('header_content_random', 60 * 60, function () {
                return HeaderContent::inRandomOrder()->first();
            });

            $bgImgPath = $bgImgPath ?? ($headerContent->bg_img ? Storage::url($headerContent->bg_img) : null);
            $mainImgPath = $mainImgPath ?? ($headerContent->main_img ? Storage::url($headerContent->main_img) : null);
        }

        // Ansicht rendern
        return view('frondend.continent_and_countries.index', [
            'continent' => $continent,
            'countries' => $countries,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
            'panorama_location_text' => $continent->continent_text ?? null,
        ]);
    }


    public function showLocations($continentAlias, $countryAlias)
    {
        // Finde den Kontinent basierend auf dem Alias
        $continent = WwdeContinent::where('alias', $continentAlias)->firstOrFail();

        // Finde das Land basierend auf dem Alias
        $country = WwdeCountry::where('alias', $countryAlias)->firstOrFail();


//dd($country);

        // Locations des Landes abrufen, die aktiv und nicht fertiggestellt sind
        $locations = WwdeLocation::where('country_id', $country->id)
        ->where('status', 'active') // Filter für aktive Locations
        ->where('finished', '1') // Filter für nicht abgeschlossene Locations
        ->get();

//dd($locations);

        // Bilder und Texte des Kontinents
        $bgImgPath = $continent->image1_path ?? null;
        $mainImgPath = $continent->image2_path ?? null;

        // Falls keine Bilder definiert sind, verwende Fallback-Bilder
        if (!$bgImgPath || !$mainImgPath) {
            $headerContent = Cache::remember('header_content_random', 60 * 60, function () {
                return HeaderContent::inRandomOrder()->first();
            });

            $bgImgPath = $bgImgPath ?? ($headerContent->bg_img ? Storage::url($headerContent->bg_img) : null);
            $mainImgPath = $mainImgPath ?? ($headerContent->main_img ? Storage::url($headerContent->main_img) : null);
        }

        // Ansicht rendern
        return view('frondend.continent_and_countries.locations', [
            'continent' => $continent,
            'country' => $country,
            'locations' => $locations,
            'panorama_location_picture' => $bgImgPath,
            'main_location_picture' => $mainImgPath,
            'panorama_location_text' => $continent->continent_text ?? null,
        ]);
    }



}
