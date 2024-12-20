<?php

namespace App\Repositories;

use DB;
use Illuminate\Http\Request;

class Continent extends \App\Models\Continent
{

    function getContinentByAlias($continent_alias){
        return \App\Models\Continent::where('alias', $continent_alias)->first();
    }

}
