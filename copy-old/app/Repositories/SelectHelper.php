<?php

namespace App\Repositories;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SelectHelper extends Model
{
    public function getCurrencies()
    {
        $currencies =  DB::table('countries')
            ->select('countries.currency_code', 'countries.currency_name')
           //->orderby('currency_code')
//            ->orderByRaw('count(currency_name) DESC')
            ->leftJoin('locations','countries.id' , '=', 'locations.country_id')
            ->where('locations.finished', 1)
            ->orderBy('countries.currency_name')
            ->groupBy('countries.currency_name')
            ->groupBy('countries.currency_code')
            //->unique('currency_code')
//            ->pluck('currency_name');
            ->get();

        return $currencies;
    /*    return cache()->rememberForever('currencies', function () use($currencies){
            return $currencies;
        });*/
    }

    public function getLanguages()
    {
        $languages =  DB::table('countries')
            //->select('currency_code')
//            ->orderByRaw('count(official_language) ASC')
            ->orderBy('countries.official_language')
            ->groupBy('official_language')
            ->leftJoin('locations','countries.id' , '=', 'locations.country_id')
            ->where('locations.finished', 1)
            ->pluck('official_language');
       return $languages;
        /* return cache()->rememberForever('languages', function () use($languages){
            return $languages;
        });*/
    }

    public function getFlightDuration()
    {
        $flightDuration =  DB::table('locations')
            //->select('currency_code')
            ->orderByRaw('count(range_flight) DESC')
            ->groupBy('range_flight')
            ->pluck('range_flight');
       return $flightDuration;
        /* return cache()->rememberForever('languages', function () use($languages){
            return $languages;
        });*/
    }
    public function getDestinations()
    {
        $Destinations =  DB::table('locations')
            //->select('currency_code')
            ->orderByRaw('count(dist_from_FRA) DESC')
            ->groupBy('dist_from_FRA')
            ->pluck('dist_from_FRA');
       return $Destinations;
        /* return cache()->rememberForever('languages', function () use($languages){
            return $languages;
        });*/
    }
}
