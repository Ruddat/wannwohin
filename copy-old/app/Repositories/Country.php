<?php

namespace App\Repositories;

use DB;
use Illuminate\Http\Request;

class Country extends \App\Models\Country
{
    function getCountryByAlias($country_alias)
    {
        return \App\Models\Country::where('alias', $country_alias)->first();
    }

    function getCountriesByContinent($continent){
        return \App\Models\Country::
        whereHas('continent', function ($q) use ($continent) {
            $q->where('alias', $continent);
        })->whereHas('location', function ($q) use ($continent) {
            $q->where('finished', 1);
        })->orderBy('title')
          ->get();
    }


    function getActiveCountries(){
        return \App\Models\Country::select('id', 'title')->
        whereHas('location', function ($q)  {
            $q->where('finished', 1);
        })
          ->orderBy('title')->get();
    }
}
