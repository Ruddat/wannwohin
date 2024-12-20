<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Library\TimeApiClientLibrary;
use App\Library\W;
use DateTime;
use DateTimeZone;
use File;
use Illuminate\Http\Request;
use App\Models\Location;
use function view;

class GetLocationController extends Controller
{
    private $locationRep;
    public function __construct()
    {
        $this->locationRep = new \App\Repositories\Location();
    }

    public function __invoke(Request $request, $continent,$country,$location)
    {
//        get parameter from url
//        get location id by parameters alias
//        get location data
        $location = $this->locationRep->getLocationById($continent,$country,$location);
        $lang_label = count(explode(',', $location->country->official_language)) > 1 ? 'Sprachen' : 'Sprache';
        $panorama_location_picture_url = "img/location_main_img/".$continent."/".$country."/reise-".$location->alias.".webp";
        $main_location_picture_url = "img/location_main_img/".$continent."/".$country."/urlaub-".$location->alias.".webp";
        $location_time = $this->calculateCurrentLocationTime($location->time_zone);
        return view('pages.location.index', [
            'location' => $location,
            'lang_label' => $lang_label,
             'country_alias' => $country,
            'location_time' => $location_time,
            'panorama_location_picture' => $panorama_location_picture_url,
            'main_location_picture' => $main_location_picture_url,
            'total_locations' =>Location::where('finished', 1)->count()
        ]);
    }

    private function calculateCurrentLocationTime($time_zone = 'Europe/Berlin')
    {
        $time_zone = 'Europe/Berlin';
                $location_time = (new TimeApiClientLibrary())->GetCurrentTimeByTimeZone($time_zone);

        //        $location_time = (new TimeApiClientLibrary())->GetCurrentTimeByTimeZone($time_zone);

        //        return ($location_time) ? $location_time['time'] : 'xxxxxx';
        $local_tz = new DateTimeZone('Europe/Berlin');
        $local = new DateTime('now', $local_tz);

        $location_tz = new DateTimeZone($time_zone);
        $location = new DateTime('now', $location_tz);

        $local_offset = $local->getOffset() / 3600;
        $location_offset = $location->getOffset() / 3600;

        $diff = $location_offset - $local_offset;
        return $diff ; //outputs 3
    }
}
