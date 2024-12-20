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

class CountryController extends Controller
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

    public function getCountriesByContinent(Request $request, $continent_alias)
    {
        $continent = $this->continentRep->getContinentByAlias($continent_alias);
        if (!$continent) return redirect(route('home'));

        $countries = $this->countryRep->getCountriesByContinent($continent_alias);
        return view('pages.countries.index', [
            'countries' => $countries,
            'continent' => $continent,
            'continent_alias' => $continent_alias,
            'total_locations' =>Location::where('finished', 1)->count()
        ]);
    }
}
