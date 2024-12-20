<?php

namespace App\Library;

//use Http;
use Illuminate\Support\Facades\Http;

class WeatherApiClientLibrary {

        public $api_main_url ;
        public $api_key ;

    public function __construct()
    {
        $this->api_main_url = config('custom.weather.apiurl');
        $this->api_key = config('custom.weather.appid');

    }

    //Gets the current time of a time zone.
    public function GetCurrentWeatherByTimeZone($lat, $lon){
//        https://api.openweathermap.org/data/2.5/weather?lat={lat}&lon={lon}&appid={API%20key}
        if ($lat!='' && $lon!='' && $lat!=null) {
            $response = Http::get($this->api_main_url . 'weather?lat=' . $lat . '&lon=' . $lon . '&appid=' . $this->api_key.'&units=metric&lang=de');
//           dd('https://api.openweathermap.org/data/2.5/weather?lat=' . $lat . '&lon=' . $lon . '&appid='.$this->api_key);
            return [
                'current_tmp' => round(floatval($response->json('main.temp', '')) ),
                'weather' => $response->json('weather.0.description', ''),
                'humidity' => $response->json('main.humidity', ''),
                'wind_speed' => $response->json('wind.speed', '')
            ];
        }else{
            return ['current_tmp' => '--', 'weather' => '--'];
        }

    }

}

