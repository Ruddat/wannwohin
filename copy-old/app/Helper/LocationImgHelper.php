<?php

namespace App\Helper;

use App\Models\Location;

class LocationImgHelper
{
    public static function getKlimatabelleImg(Location $location): string
    {
        $location->loadMissing(['continent', 'country']);
        return "https://www.klimatabelle.de/img/klima/{$location->continent->alias}/{$location->country->alias}/klimatabelle-{$location->alias}.webp";
    }

    public static function getKlimatabelleLocalImg(Location $location): string
    {
        $location->loadMissing(['continent', 'country']);
        return asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/klimatabelle-{$location->alias}.jpg");
    }

    public static function getKlimaImg(Location $location): string
    {
        $location->loadMissing(['continent', 'country']);
        return asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/klima-{$location->alias}.jpg");
    }

    public static function getCountryImg(Location $location): string
    {
        $location->loadMissing(['country']);
        return asset("img/flags_small/{$location->country->alias}.jpg");
    }

    public static function getContinentImg(Location $location)
    {
        $location->loadMissing(['continent']);
        return asset("img/location_main_img/{$location->continent->alias}.png");
    }

    public static function get_average($country_bsp, $my_bsp = 45091) {
        $country_bsp = $country_bsp == 0 ? 0.1 : $country_bsp;

        $average_price = $country_bsp / $my_bsp;
        $average_price = number_format($average_price, 2, '.', '') * 100;

        if ($average_price > 200) { $average_price = 200; }

        switch ($average_price) {
            case ($average_price > 66 && $average_price < 133):
                $average_text = "DURCHSCHNITTLICH";
                break;
            case ($average_price > 132 && $average_price < 201):
                $average_text = "HOCH";
                break;
            default:
                $average_text = "NIEDRIG";
        }

        return $average_text;
    }

    public static function checkIfWebpSupported(){
        if( strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false ) {
            return 'webp';
        }else{
            return 'jpg';
        }
        }
}
