<?php

namespace App\Http\Controllers;

use App\Models\Continent;
use  App\Repositories\Location;
use App\Library\WeatherApiClientLibrary;

class IndexController extends Controller
{
    public function __invoke()
    {
     $location = new Location();
     $top_ten =$this->addWeatherToTopTen($location->topTenLocations());
     $total_locations = Location::where('finished', 1 )->count();
     return view('pages.main.index',['top_tep'=>$top_ten, 'total_locations' =>$total_locations] );
    }

    function addWeatherToTopTen($top_ten){

        $weatherLib = new WeatherApiClientLibrary();
        foreach ($top_ten as &$location){
            $weather_from_api = $weatherLib->GetCurrentWeatherByTimeZone($location->lat_new, $location->lon_new);
            $location->current_temp_from_api = $weather_from_api['current_tmp'];
            $location->current_weather_from_api = $weather_from_api['weather'];
        }

        return $top_ten;
    }
}
