<?php

namespace App\Repositories;

use DB;
use Illuminate\Http\Request;

class Location extends \App\Models\Location
{
    function getLocationById($continent,$country,$location) {
        return \App\Models\Location::where('alias', $location)
            ->whereHas('continent', function ($q) use ($continent) {
                $q->where('alias', $continent);
            })
            ->whereHas('country', function ($q) use ($country) {
                $q->where('alias', $country);
            })
            ->with([
            'continent',
            'country',
            'electric',
        ])->firstOrFail();
    }

    function getLocationsByContinentAndCountry($continent,$country){
        return \App\Models\Location::
            whereHas('continent', function ($q) use ($continent) {
                $q->where('alias', $continent);
            })
            ->whereHas('country', function ($q) use ($country) {
                $q->where('alias', $country);
            })->where('finished', 1)
//            ->with([
//                'continent',
//                'country',
//                'electric',
//            ])
            ->get();


    }

    function searchLocation($Special_location_wishes, $month_id, $continent=null,$price=null,$sonnenstunden=null, $wassertemperatur=null ,$sonnenstunden_where=null, $wassertemperatur_where=null,$items_per_page,$sort_criteria)
    {
       //Elequent dose not support order By relation ship by default there are nummbers of way do achive that some are wrong some are gut some relaise better performacne
        // get() with sortBy (relation.fieldName) with take (number) aber in this case u can not user pagination for example
        //better way is to to order by sub query
        $locations = DB::table('locations')
        ->select(
            'locations.title',
            'locations.alias',
            'locations.flight_hours',
            'locations.price_flight',
            'locations.best_traveltime_json',
            'locations.list_beach',
            'locations.list_citytravel',
            'locations.list_sports',
            'locations.list_island',
            'locations.list_culture',
            'locations.list_nature',
            'locations.list_watersport',
            'locations.list_wintersport',
            'locations.list_mountainsport',
            'continents.alias as continent_alias',
            'countries.alias as country_alias',
            'countries.title as country_title',
            'continents.title as continent_title',
            'climates.daily_temperature as climates_daily_temperature',
            'climates.month as climates_month',
            'climates.water_temperature as climates_water_temperature',
            'climates.humidity as climates_humidity',
            'climates.sunshine_per_day as climates_sunshine_per_day',
            'climates.rainy_days as climates_rainy_days',
        )
            ->leftJoin('continents','locations.continent_id', '=','continents.id')
            ->leftJoin('countries','locations.country_id', '=','countries.id')
            ->leftJoin('climates', function($join) use ($month_id)
            {
                $join->on('locations.id', '=', 'climates.location_id');
                $join->on('climates.month_id','=', DB::raw($month_id));
            })
            ->where(function($query) use ($continent,$price,$sonnenstunden, $wassertemperatur,$Special_location_wishes, $sonnenstunden_where, $wassertemperatur_where) {
                $query->when($continent, function ($query, $value) {
                    return $query->where('continents.id', $value);
                });
                $query->when($price, function ($query, $value) {
                    return $query->where('locations.range_flight','<=', $value);
                });
                $query->when($sonnenstunden, function  ($query, $value) use ($sonnenstunden_where) {
                   return $query->where(\DB::raw('ROUND(sunshine_per_day)'),$sonnenstunden_where[0], $sonnenstunden_where[1]);
                });
                $query->when($wassertemperatur, function  ($query, $value) use ($wassertemperatur_where) {
                   return $query->where(\DB::raw('ROUND(water_temperature)'),$wassertemperatur_where[0], $wassertemperatur_where[1]);
                });
                $query->when($Special_location_wishes, function  ($query, $value)  {
                    $q = $query->where('locations.'.$value, 1);
                    if( $value =='list_wintersport'){
                        $q->where('climates.night_temperature','<', 1);
                    }
                    return $q;
                });

            })
            ->where('locations.finished', 1)
            ->orderBy($sort_criteria['sort_by'], $sort_criteria['sort_direction'])

            ->paginate($items_per_page);
        return $locations;
/*

        $locations = \App\Models\Location::with(['continent', 'country', 'climate' => function($q) use ($month){
            $q->where('climates.month_id', '=', $month);
        }])
            ->when($request->input('continent'), function ($query, $continent) {
                return $query->where('continent_id', $continent);
            })
            ->when($request->input('price'), function ($query, $value) {
                return $query->where('Range_Flight', $value);
            })
            ->whereJsonContains('best_traveltime_json', $month)
            ->whereHas('climate', function ($q) use ($request, $month, $sonnenstunden_where, $wassertemperatur_where) {
                $q
                    ->where('month_id', $month)
                    ->when($request->input('sonnenstunden'), function  ($q, $v) use ($sonnenstunden_where) {
                        $q->where(\DB::raw('ROUND(sunshine_per_day)'),$sonnenstunden_where[0], $sonnenstunden_where[1]);
                    })
                    ->when($request->input('wassertemperatur'), function ($q, $v) use ($wassertemperatur_where){
                        $q->where(\DB::raw('ROUND(water_temperature)'),$wassertemperatur_where[0],$wassertemperatur_where[1]);
                    })
                ;
            })
            ->when($request->input('spezielle'), function ($query, $value) {
                $query->where($value, true);
            })
//            ->orderBy($sort_criteria['sort_by'], $sort_criteria['sort_direction'])
            ->orderBy($this->orderByInstance($sort_criteria['sort_by'], $sort_criteria['sort_direction']))
            ->paginate($items_per_page);
            // dd($request->par_page);*/
        return $locations;
    }

    function detailsSearchLocationQuery($search)
    {
       //Elequent dose not support order By relation ship by default there are nummbers of way do achive that some are wrong some are gut some relaise better performacne
        // get() with sortBy (relation.fieldName) with take (number) aber in this case u can not user pagination for example
        //better way is to to order by sub query
        $locations = DB::table('locations')
        ->select(
            'locations.title',
            'locations.alias',
            'locations.flight_hours',
            'locations.price_flight',
            'locations.best_traveltime_json',
            'locations.list_beach',
            'locations.list_citytravel',
            'locations.list_sports',
            'locations.list_island',
            'locations.list_culture',
            'locations.list_nature',
            'locations.list_watersport',
            'locations.list_wintersport',
            'locations.list_mountainsport',
            'continents.alias as continent_alias',
            'countries.alias as country_alias',
            'countries.title as country_title',
            'continents.title as continent_title',
            'climates.daily_temperature as climates_daily_temperature',
            'climates.night_temperature as climates_night_temperature',
            'climates.month as climates_month',
            'climates.water_temperature as climates_water_temperature',
            'climates.humidity as climates_humidity',
            'climates.sunshine_per_day as climates_sunshine_per_day',
            'climates.rainy_days as climates_rainy_days',
        )
            ->leftJoin('continents','locations.continent_id', '=','continents.id')
            ->leftJoin('countries','locations.country_id', '=','countries.id')
            ->leftJoin('climates', function($join) use ($search)
            {
                $join->on('locations.id', '=', 'climates.location_id');
                $join->on('climates.month_id','=', DB::raw($search['month_id']));
            })
            ->where(function($query) use ($search) {
                $query->when($search['r_continents'], function ($query, $value) {
                    return $query->whereIn('continents.id', $value);
                });

                $query->when($search['country'], function ($query, $value) {
                    return $query->where('locations.country_id', $value);
                });
                $query->when($search['range_flight'], function ($query, $value) {
                    return $query->where('locations.range_flight','<=', $value);
                });
                $query->when($search['currency'], function ($query, $value) {
                    return $query->where('countries.currency_code','=', $value);
                });

                $query->when($search['language'], function ($query, $value) {
                    return $query->where('countries.official_language','LIKE', '%'.$value.'%');
                });

                $query->when($search['visum'], function ($query, $value) {
                    switch ($value) {
                        case 'yes':
                            return $query->where('countries.country_visum_needed','=', 1);
                        case 'no':
                            return $query->where('countries.country_visum_needed','=', 0);
                    }
                });

                $query->when($search['preis_tendenz'], function ($query, $value) {
                    $my_bsp = config('custom.global.my_bsp');
                    switch ($value) {
                        case 'middle':
                            return $query->where('countries.price_tendency', 'Mittel');
                            break;
                        case 'high':
                            return $query->where('countries.price_tendency', 'Hoch');
                            break;
                        default :
                            return $query->where('countries.price_tendency', 'Niedrig');
                    }

                });

                //climate_zone
                $query->when($search['climate_zone'], function ($query, $value) {
                    return $query->where('locations.climate_lnam', $value);
//                    switch ($value) {
//                        case 'middle':
//                            return $query->where('locations.climate_lnam', 'Mittel');
//                            break;
//                        case 'high':
//                            return $query->where('locations.climate_lnam', 'Hoch');
//                            break;
//                        default :
//                            return $query->where('locations.climate_lnam', 'Niedrig');
//                    }

                });

                $query->when($search['flight_duration'], function ($query, $value) {
                    if (config('custom.details_search_options.flight_duration.'.(int)$value)== null)
                        return null;
                    $duration = config('custom.details_search_options.flight_duration.'.(int)$value.'.value');
                    $operator =    config('custom.details_search_options.flight_duration.'.(int)$value.'.operator');
                    return $query->where('locations.flight_hours',$operator, $duration);
                });

                $query->when($search['distance_to_destination'], function ($query, $value) {
                    if (config('custom.details_search_options.distance_to_destination.'.(int)$value)== null)
                        return null;
                    $destination = config('custom.details_search_options.distance_to_destination.'.(int)$value.'.value');
                    $operator =    config('custom.details_search_options.distance_to_destination.'.(int)$value.'.operator');
                    return $query->where('locations.dist_from_FRA',$operator, $destination);
                });

                $query->when($search['stop_over'], function ($query, $value) {
                    switch ($value) {
                        case 'no':
                            return $query->where('locations.stop_over','>=', 1);
                        case 'yes':
                            return $query->where('locations.stop_over','=', 0);
                    }
                });

                if($search['r_activities']!=null and count($search['r_activities']) > 0) {
                    foreach ($search['r_activities'] as $activity) {
                        $query->where('locations.' . $activity, 1);
                        if ($activity == 'list_wintersport') {
                            $query->where('climates.night_temperature', '<', 1);
                        }
                    }
                }
                $query->whereBetween('climates.daily_temperature', [$search['daily_temp_min'], $search['daily_temp_max']]);
                $query->whereBetween('climates.water_temperature', [$search['water_temp_min'], $search['water_temp_max']]);
                $query->whereBetween('climates.sunshine_per_day', [$search['sunshine_per_day_min'], $search['sunshine_per_day_max']]);
                $query->whereBetween('climates.rainy_days', [$search['rainy_days_min'], $search['rainy_days_max']]);
                $query->whereBetween('climates.humidity', [$search['humidity_min'], $search['humidity_max']]);
                $query->whereBetween('climates.night_temperature', [$search['night_temp_min'], $search['night_temp_max']]);
            })
            ->where('locations.finished', 1);
        return $locations;
/*

        $locations = \App\Models\Location::with(['continent', 'country', 'climate' => function($q) use ($month){
            $q->where('climates.month_id', '=', $month);
        }])
            ->when($request->input('continent'), function ($query, $continent) {
                return $query->where('continent_id', $continent);
            })
            ->when($request->input('price'), function ($query, $value) {
                return $query->where('Range_Flight', $value);
            })
            ->whereJsonContains('best_traveltime_json', $month)
            ->whereHas('climate', function ($q) use ($request, $month, $sonnenstunden_where, $wassertemperatur_where) {
                $q
                    ->where('month_id', $month)
                    ->when($request->input('sonnenstunden'), function  ($q, $v) use ($sonnenstunden_where) {
                        $q->where(\DB::raw('ROUND(sunshine_per_day)'),$sonnenstunden_where[0], $sonnenstunden_where[1]);
                    })
                    ->when($request->input('wassertemperatur'), function ($q, $v) use ($wassertemperatur_where){
                        $q->where(\DB::raw('ROUND(water_temperature)'),$wassertemperatur_where[0],$wassertemperatur_where[1]);
                    })
                ;
            })
            ->when($request->input('spezielle'), function ($query, $value) {
                $query->where($value, true);
            })
//            ->orderBy($sort_criteria['sort_by'], $sort_criteria['sort_direction'])
            ->orderBy($this->orderByInstance($sort_criteria['sort_by'], $sort_criteria['sort_direction']))
            ->paginate($items_per_page);
            // dd($request->par_page);*/
        return $locations;
    }

    function detailsSearchLocation($search, $items_per_page,$sort_criteria)
    {
        $locations = $this->detailsSearchLocationQuery($search);
        return $locations->orderBy($sort_criteria['sort_by'], $sort_criteria['sort_direction'])
                ->paginate($items_per_page);
    }

    function detailsSearchLocationCount($search)
    {
        $locations_count = $this->detailsSearchLocationQuery($search);
        return $locations_count->count();
    }

    function searchLocationCount($Special_location_wishes, $month_id, $continent=null,$price=null,$sonnenstunden=null, $wassertemperatur=null ,$sonnenstunden_where=null, $wassertemperatur_where=null)
    {
        return DB::table('locations')
            ->leftJoin('continents','locations.continent_id', '=','continents.id')
            ->leftJoin('countries','locations.country_id', '=','countries.id')
            ->leftJoin('climates', function($join) use ($month_id)
            {
                $join->on('locations.id', '=', 'climates.location_id');
                $join->on('climates.month_id','=', DB::raw($month_id));
            })
            ->where(function($query) use ($continent, $price,$wassertemperatur ,$sonnenstunden, $Special_location_wishes, $sonnenstunden_where, $wassertemperatur_where) {
                $query->when($continent, function ($query, $value) {
                    return $query->where('continents.id', $value);
                });
                $query->when($price, function ($query, $value) {
                    return $query->where('locations.range_flight','<=', $value);
                });
                $query->when($sonnenstunden, function  ($query, $value) use ($sonnenstunden_where) {
                   return $query->where(\DB::raw('ROUND(sunshine_per_day)'),$sonnenstunden_where[0], $sonnenstunden_where[1]);
                });
                $query->when($wassertemperatur, function  ($query, $value) use ($wassertemperatur_where) {
                   return $query->where(\DB::raw('ROUND(water_temperature)'),$wassertemperatur_where[0], $wassertemperatur_where[1]);
                });
                $query->when($Special_location_wishes , function  ($query, $value)  {
                    $q = $query->where('locations.'.$value, 1);
                    if( $value =='list_wintersport'){
                        $q->where('climates.night_temperature','<', 1);
                    }
                    return $q;
                });
/*                if($request->spezielle !='')
                    $query->where('locations.'.$request->spezielle,1);*/
            })->where('locations.finished', 1)->count();
/*

        $locations = \App\Models\Location::with(['continent', 'country', 'climate' => function($q) use ($month){
            $q->where('climates.month_id', '=', $month);
        }])
            ->when($request->input('continent'), function ($query, $continent) {
                return $query->where('continent_id', $continent);
            })
            ->when($request->input('price'), function ($query, $value) {
                return $query->where('Range_Flight', $value);
            })
            ->whereJsonContains('best_traveltime_json', $month)
            ->whereHas('climate', function ($q) use ($request, $month, $sonnenstunden_where, $wassertemperatur_where) {
                $q
                    ->where('month_id', $month)
                    ->when($request->input('sonnenstunden'), function  ($q, $v) use ($sonnenstunden_where) {
                        $q->where(\DB::raw('ROUND(sunshine_per_day)'),$sonnenstunden_where[0], $sonnenstunden_where[1]);
                    })
                    ->when($request->input('wassertemperatur'), function ($q, $v) use ($wassertemperatur_where){
                        $q->where(\DB::raw('ROUND(water_temperature)'),$wassertemperatur_where[0],$wassertemperatur_where[1]);
                    })
                ;
            })
            ->when($request->input('spezielle'), function ($query, $value) {
                $query->where($value, true);
            })
//            ->orderBy($sort_criteria['sort_by'], $sort_criteria['sort_direction'])
            ->orderBy($this->orderByInstance($sort_criteria['sort_by'], $sort_criteria['sort_direction']))
            ->paginate($items_per_page);
            // dd($request->par_page);*/
        return $locations;
    }

    function topTenLocations(){
        return DB::table('location_statistic')
           ->select('locations.title', 'continents.alias as continent_alias', 'countries.alias as country_alias', 'locations.alias as location_alias', 'climates.daily_temperature' , 'locations.lat_new', 'locations.lon_new' )
            ->leftJoin('locations','location_statistic.id' , '=', 'locations.id')
            ->leftJoin('climates','location_statistic.id' , '=', 'climates.location_id')
            ->leftJoin('countries','locations.country_id' , '=', 'countries.id')
            ->leftJoin('continents','locations.continent_id' , '=', 'continents.id')
            ->where('climates.month_id' , '=' , now()->month)
            ->limit(10)->orderBy('location_statistic.clicks',  'desc')->get();
    }

    function updateLocationClick($id){
//        return DB::table('location_statistic')
//           ->where('location_id', $id)
//            ->update([
//               'clicks'=> DB::raw('clicks+1')
//           ]);

//        return DB::table('location_statistic')->where('id', $id)->update(['clicks' => \DB::raw('clicks + 1')]);
//        return DB::table('location_statistic')->where('id', $id)->update(['clicks' => \DB::raw('clicks + 1')]);
        return DB::table('location_statistic')->updateOrInsert(
            ['id' => $id],
            ['clicks' => \DB::raw( 'clicks + 1')]
        );

    }

    public function searchSpecialLocation($special, $items_per_page, array $sort_criteria)
    {
        //Elequent dose not support order By relation ship by default there are nummbers of way do achive that some are wrong some are gut some relaise better performacne
        // get() with sortBy (relation.fieldName) with take (number) aber in this case u can not user pagination for example
        //better way is to to order by sub query
        $month_id = config('app.global.default_urlaub_month');
        $locations = DB::table('locations')
            ->select(
                'locations.title',
                'locations.alias',
                'locations.flight_hours',
                'locations.price_flight',
                'locations.best_traveltime_json',
                'locations.list_beach',
                'locations.list_citytravel',
                'locations.list_sports',
                'locations.list_island',
                'locations.list_culture',
                'locations.list_nature',
                'locations.list_watersport',
                'locations.list_wintersport',
                'locations.list_mountainsport',
                'continents.alias as continent_alias',
                'countries.alias as country_alias',
                'countries.title as country_title',
                'continents.title as continent_title',
                'climates.daily_temperature as climates_daily_temperature',
                'climates.month as climates_month',
                'climates.water_temperature as climates_water_temperature',
                'climates.humidity as climates_humidity',
                'climates.sunshine_per_day as climates_sunshine_per_day',
                'climates.rainy_days as climates_rainy_days',
            )
            ->leftJoin('continents','locations.continent_id', '=','continents.id')
            ->leftJoin('countries','locations.country_id', '=','countries.id')
            ->leftJoin('climates', function($join) use ($month_id)
            {
                $join->on('locations.id', '=', 'climates.location_id');
                $join->on('climates.month_id','=', DB::raw($month_id));
            })
            ->where(function($query) use ($request) {
                $query->when($request->input('spezielle'), function  ($query, $value)  {
                    return $query->where('locations.'.$value, 1);
                });
            })
            ->orderBy($sort_criteria['sort_by'], $sort_criteria['sort_direction'])

            ->paginate($items_per_page);
        return $locations;
    }

    public function searchByType($type_fied, $items_per_page, array $sort_criteria)
    {
        //Elequent dose not support order By relation ship by default there are nummbers of way do achive that some are wrong some are gut some relaise better performacne
        // get() with sortBy (relation.fieldName) with take (number) aber in this case u can not user pagination for example
        //better way is to to order by sub query
        $month_id = config('app.global.default_urlaub_month');
        $locations = DB::table('locations')
            ->select(
                'locations.title',
                'locations.alias',
                'locations.flight_hours',
                'locations.price_flight',
                'locations.best_traveltime_json',
                'locations.list_beach',
                'locations.list_citytravel',
                'locations.list_sports',
                'locations.list_island',
                'locations.list_culture',
                'locations.list_nature',
                'locations.list_watersport',
                'locations.list_wintersport',
                'locations.list_mountainsport',
                'continents.alias as continent_alias',
                'countries.alias as country_alias',
                'countries.title as country_title',
                'continents.title as continent_title',
                'climates.daily_temperature as climates_daily_temperature',
                'climates.month as climates_month',
                'climates.water_temperature as climates_water_temperature',
                'climates.humidity as climates_humidity',
                'climates.sunshine_per_day as climates_sunshine_per_day',
                'climates.rainy_days as climates_rainy_days',
            )
            ->leftJoin('continents','locations.continent_id', '=','continents.id')
            ->leftJoin('countries','locations.country_id', '=','countries.id')
            ->leftJoin('climates', function($join) use ($month_id)
            {
                $join->on('locations.id', '=', 'climates.location_id');
                $join->on('climates.month_id','=', DB::raw($month_id));
            })
            ->where(function($query) use ($type_fied) {
                return $query->where('locations.'.$type_fied, 1);
            })
            ->orderBy($sort_criteria['sort_by'], $sort_criteria['sort_direction'])

            ->paginate($items_per_page);
        return $locations;
    }
}
