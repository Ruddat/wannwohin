<?php

namespace App\Http\Controllers;

use App\Library\TimezoneApiClientLibrary;
//use App\Library\W;
use App\Library\WeatherApiClientLibrary;
use App\Models\Location;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Models\Climate;
use function view;

class LocationController extends Controller
{
    private $locationRep;
    /**
     * @var false|string
     */
    private $_location_time;

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
        $this->updateLocationClick($location->id);
        $lang_label = count(explode(',', $location->country->official_language)) > 1 ? 'Sprachen' : 'Sprache';
        $panorama_location_picture_url = "img/location_main_img/".$continent."/".$country."/".$location->alias."/reise-".$location->alias.".webp";
        $main_location_picture_url = "img/location_main_img/".$continent."/".$country."/".$location->alias."/urlaub-".$location->alias.".webp";
        $panorama_text_and_style = $location->panorama_text_and_style;
        $location_time_diff = $this->calculateCurrentLocationTime($location->time_zone);
        $location_image_gallery=  $this->locationImageGallery($location->continent->alias, $country, $location->alias);
        $climate = Climate::getAllClimateData($location->id);;
        $weather_from_api = (new WeatherApiClientLibrary())->GetCurrentWeatherByTimeZone($location->lat_new, $location->lon_new);
        //        $location_time_diff = $this->getTimeZoneByPoint($location->lat_new, $location->lon_new );
//        dd($main_location_picture_url);
        return view('pages.location.index', [
            'location' => $location,
            'lang_label' => $lang_label,
             'country_alias' => $country,
            'location_time_diff' => $location_time_diff,
            'location_time' => $this->_location_time,
            'panorama_location_picture' => $panorama_location_picture_url,
            'location_image_gallery' => $location_image_gallery,
            'panorama_location_text' => $panorama_text_and_style,
            'main_location_picture' => $main_location_picture_url,
            'climates'=> $climate,
            'weather_from_api'=> $weather_from_api,
            'total_locations' =>Location::where('finished', 1)->count()
        ]);
    }
    private function locationImageGallery($continent, $country, $location_alias){
//
        $supported_img_extension = \App\Helper\LocationImgHelper::checkIfWebpSupported();
        $path = public_path("img/location_main_img/$continent/$country/$location_alias/urlaubsfotos");
        if (!file_exists($path))
            return false;
        $images = \File::allFiles($path);
        if ($images and count($images)>0){
            $image_with_full_path = array();
            foreach ($images as $image){
                if($image->getExtension() == $supported_img_extension) {
                    $image_file_name = $image->getFilename();
                    $image_with_full_path[] =asset("img/location_main_img/$continent/$country/$location_alias/urlaubsfotos/$image_file_name");
//                    $image_with_full_path[] = asset("img/location_main_img/europa/frankreich/paris/urlaubsfotos/$image_file_name");
                }
            }
        }
            return $image_with_full_path;
        return false;
    }
    private function calculateCurrentLocationTime($time_zone = 'Europe/Berlin')
    {
        if ($time_zone =='')
            return 'no tz';
                //$location_time = (new TimezoneApiClientLibrary())->GetCurrentTimeByTimeZone($time_zone);

        //        $location_time = (new TimeApiClientLibrary())->GetCurrentTimeByTimeZone($time_zone);

        //        return ($location_time) ? $location_time['time'] : 'xxxxxx';
        $local_tz = new DateTimeZone('Europe/Berlin');
        $local = new DateTime('now', $local_tz);

        $location_tz = new DateTimeZone($time_zone);
        $location = new DateTime('now', $location_tz);
        $this->_location_time = $location->format('d.m.Y H:i');

        $local_offset = $local->getOffset() / 3600;
        $location_offset = $location->getOffset() / 3600;

        $diff = $location_offset - $local_offset;
        return $diff ; //outputs 3
    }

    private function getTimeZoneByPoint($lat, $lon)
    {
        $time_zone = (new TimezoneApiClientLibrary())->getByPoint($lat, $lon);
    }

    private function updateLocationClick($id)
    {
        $this->locationRep->updateLocationClick($id);
    }
}
