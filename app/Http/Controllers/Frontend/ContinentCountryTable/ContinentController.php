<?php

namespace App\Http\Controllers\Frontend\ContinentCountryTable;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\HeaderContent;
use App\Models\WwdeContinent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ContinentController extends Controller
{
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
