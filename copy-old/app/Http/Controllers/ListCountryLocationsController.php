<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Library\TimezoneApiClientLibrary;
use App\Library\W;
use DateTime;
use DateTimeZone;
use File;
use Illuminate\Http\Request;
use App\Models\Location;
use function view;

class ListCountryLocationsController extends Controller
{
    private $locationRep;
    /**
     * @var \App\Repositories\Location
     */
    private $countryRep;
    /**
     * @var \App\Repositories\Location
     */
    private $continentRep;

    public function __construct()
    {
        $this->locationRep = new \App\Repositories\Location();
        $this->countryRep = new \App\Repositories\Country();
        $this->continentRep = new \App\Repositories\Continent();
    }

    public function locations(Request $request, $continent,$country)
    {
//        get parameter from url
//        get location id by parameters alias
//        get location data
        $locations = $this->locationRep->getLocationsByContinentAndCountry($continent,$country);
        $country = $this->countryRep->getCountryByAlias($country);
//        dd($locations);
//        $panorama_location_picture_url = "img/location_main_img/".$continent."/".$country."/reise-".$location->alias.".webp";
//        $main_location_picture_url = "img/location_main_img/".$continent."/".$country."/urlaub-".$location->alias.".webp";
//        $location_time = $this->calculateCurrentLocationTime($location->time_zone);
        return view('pages.locations.index', [
            'locations' => $locations,
            'country' => $country,
            'continent_alias' => $continent,
//             'country' => $country,
//            'location_time' => $location_time,
//            'panorama_location_picture' => $panorama_location_picture_url,
//            'main_location_picture' => $main_location_picture_url,
            'total_locations' =>Location::where('finished', 1)->count()
        ]);
    }
}
