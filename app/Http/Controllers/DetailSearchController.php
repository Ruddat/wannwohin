<?php

namespace App\Http\Controllers;
use App\Models\Continent;
use App\Models\Country;
use App\Repositories\SelectHelper;
use App\Repositories\Country as CountryRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Search\SearchController;



class DetailSearchController extends Controller
{
    public function index(Request $request)
    {
        $currencies = (new SelectHelper)->getCurrencies();
//        $languages = (new SelectHelper)->getLanguages();
        $languages = config('custom.details_search_options.languages');
//        $flightDuration = (new SelectHelper)->getFlightDuration();
        $flightDuration = config('custom.details_search_options.flight_duration');
//        $Destinations = config('custom.details_search_options.distance_to_destination.values');
        $Destinations = config('custom.details_search_options.distance_to_destination');

        $continents = Continent::all();
//        $countries = Country::all()->sortBy("title");
        $countries = (new CountryRepo)->getActiveCountries();
//        $preistendenzs  = \App\Models\Range::where('Type', 'Flight')->orderBy('sort')->get();
        $preistendenzs  = config('custom.details_search_options.preis_tendenz.values');
        $climate_lnam  = config('custom.details_search_options.climate_lnam.values');
        $activities  = config('custom.activities');
        $total_locations =(new SearchController)->detailsSearchResultCount($request);
        return view('pages.detailSearch.index',[
            'continents' =>$continents,
            'countries' =>$countries,
            'currencies' => $currencies,
            'languages' => $languages,
            'flightDuration' => $flightDuration,
            'Destinations' => $Destinations,
            'preistendenzs' => $preistendenzs,
            'climate_lnam' => $climate_lnam,
            'total_locations' => $total_locations,
            'activities'=> $activities
        ]);
    }

    public function result(){


    }
}
