<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Repositories\Location;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * @var Location
     */
    private $locationRep;
    /**
     * @var string
     */
    private $panorama_location_picture_url;
    /**
     * @var string
     */
    private $main_location_picture_url;
    private string $panorama_text_and_style;

    public function __construct()
    {
        $this->locationRep = new Location();
    }

    public function __invoke(Request $request)
    {
       $sort_by_criteria = $this->sort_by_criteria();
        $sonnenstunden_where = $wassertemperatur_where =  '';
        $sonnenstunden = explode('_',$request->sonnenstunden);
        if($sonnenstunden[0] =="less"){
            $sonnenstunden_where = ["<=",  $sonnenstunden[1]];
        }elseif ($sonnenstunden[0] =="more"){
            $sonnenstunden_where = [">",  $sonnenstunden[1]];
        }
        $wassertemperatur = explode('_',$request->wassertemperatur);
        if($wassertemperatur[0] =="less"){
            $wassertemperatur_where = ["<=",$wassertemperatur[1]];
        }elseif ($wassertemperatur[0] =="more"){
            $wassertemperatur_where = [">",$wassertemperatur[1]];
        }

        $items_per_page = $request->items_per_page ?? config('custom.global.items_per_page');
        $sort_criteria = $this->searchResultSorting($request->sort_by, $request->sort_direction);
        $Special_location_wishes = $request->input('spezielle');
        $continent = $request->input('continent') ?? null;
        $price = $request->input('price') ?? null;
        $sonnenstunden = $request->input('sonnenstunden') ?? null;
        $wassertemperatur = $request->input('wassertemperatur') ?? null;
        $month_id = (int)$request->input('urlaub') ?? config('app.global.default_urlaub_month');

        $locations = $this->locationRep->searchLocation($Special_location_wishes, $month_id, $continent,$price,$sonnenstunden, $wassertemperatur ,$sonnenstunden_where, $wassertemperatur_where,$items_per_page,$sort_criteria);
        $month = $request->input('urlaub') ?? config('app.global.default_urlaub_month');
        return view('pages.search_result', [
                'locations' => $locations,
                'items_per_page' => $items_per_page,
                'month'     => $month,
                'sort_by_criteria'     => $sort_by_criteria,
                'total_locations' => $this->searchResultCount($request)
        ]);
    }

    public function detailsSearchResult(Request $request)
    {
     $search_criteria = $this->detailsSearchResultQuery($request);
     $sort_by_criteria = $this->sort_by_criteria();
     $items_per_page = $request->items_per_page ?? config('custom.global.items_per_page');
     $sort_criteria = $this->searchResultSorting($request->sort_by, $request->sort_direction);
     $locations = $this->locationRep->detailsSearchLocation($search_criteria, $items_per_page,$sort_criteria);
     $month = $search_criteria['month_id'];
        return view('pages.search_result', [
                'locations' => $locations,
                'items_per_page' => $items_per_page,
                'month'     => $month,
                'sort_by_criteria'     => $sort_by_criteria,
                'total_locations' => $this->detailsSearchResultCount($request)
        ]);
    }

    public function detailsSearchResultQuery(Request $request)
    {
        $where = '1=1';
        $search['r_continents'] = ($request->continents!=null) ? $request->continents : null ; //locations.continent_id
        $search['country'] = ($request->country!=null) ? $request->country : null ; //locations.continent_id
        $search['month_id'] = ($request->month!=null) ? (int)$request->input('month') : config('custom.global.default_urlaub_month'); //month_id temp
        $search['range_flight'] = (int)$request->input('range_flight') != 0 ? (int)$request->input('range_flight') : null; // Preis pro Person > locations.range_flight
        $search['currency'] = $request->input('currency'); // Währung  > countries.currency_code
        $search['language'] = $request->input('language'); // Sprache  > countries.official_language
        $search['visum'] = ($request->input('visum')!=null) ? $request->input('visum') : null; // visum  > countries.country_visum_needed
        $search['preis_tendenz'] = ($request->input('preis_tendenz')!=null) ? ($request->input('preis_tendenz')) : null; // Preisendenz  > ????
        $search['climate_zone'] = ($request->input('climate_zone')!=null) ? ($request->input('climate_zone')) : null; // climate_zone  > ????
        $search['flight_duration'] = ($request->input('flight_duration')!=null) ? ($request->input('flight_duration')) : null; // Flugstunden  > ????
        $search['distance_to_destination'] = ($request->input('distance_to_destination')!=null) ? ($request->input('distance_to_destination')) : null; // Entfernung zum Reiseziel  > ????
        $search['stop_over'] = ($request->input('stop_over')!=null) ? ($request->input('stop_over')) : null; // Umsteigen  > ????
        $search['r_activities'] = ($request->activities!=null) ? $request->activities : null ; //activities.continent_id
        $search['daily_temp_min'] = ($request->input('daily_temp_min')!=null) ? ($request->input('daily_temp_min')) : config('custom.details_search_options.daily_temp.min');
        $search['daily_temp_max'] = ($request->input('daily_temp_max')!=null) ? ($request->input('daily_temp_max')) : config('custom.details_search_options.daily_temp.max');
        $search['night_temp_min'] = ($request->input('night_temp_min')!=null) ? ($request->input('night_temp_min')) : config('custom.details_search_options.night_temp.min');
        $search['night_temp_max'] = ($request->input('night_temp_max')!=null) ? ($request->input('night_temp_max')) : config('custom.details_search_options.night_temp.max');
        $search['water_temp_min'] = ($request->input('water_temp_min')!=null) ? ($request->input('water_temp_min')) : config('custom.details_search_options.water_temp.min');
        $search['water_temp_max'] = ($request->input('water_temp_max')!=null) ? ($request->input('water_temp_max')) : config('custom.details_search_options.water_temp.max');
        $search['sunshine_per_day_min'] = ($request->input('sunshine_per_day_min')!=null) ? ($request->input('sunshine_per_day_min')) : config('custom.details_search_options.sunshine_per_day.min');
        $search['sunshine_per_day_max'] = ($request->input('sunshine_per_day_max')!=null) ? ($request->input('sunshine_per_day_max')) : config('custom.details_search_options.sunshine_per_day.max');
        $search['rainy_days_min'] = ($request->input('rainy_days_min')!=null) ? ($request->input('rainy_days_min')) : config('custom.details_search_options.rainy_days.min');
        $search['rainy_days_max'] = ($request->input('rainy_days_max')!=null) ? ($request->input('rainy_days_max')) : config('custom.details_search_options.rainy_days.max');
        $search['humidity_min'] = ($request->input('humidity_min')!=null) ? ($request->input('humidity_min')) : config('custom.details_search_options.humidity.min');
        $search['humidity_max'] = ($request->input('humidity_max')!=null) ? ($request->input('humidity_max')) : config('custom.details_search_options.humidity.max');
        return $search;
    }

    public function detailsSearchResultCount(Request $request)
    {
        $search = $this->detailsSearchResultQuery($request);
        return $this->locationRep->detailsSearchLocationCount($search);
    }
    function searchByType(Request $request, $urlaub_type= null, $month_id=null){

        $Special_location_wishes = $this->getSpecialLocationWishes($urlaub_type);
        $sort_by_criteria = $this->sort_by_criteria();
        $items_per_page = config('custom.global.items_per_page');
        $sort_by = $request->input('sort_by')? $request->input('sort_by') : config('app.global.sort_by');
        $sort_direction = $request->input('sort_direction')? $request->input('sort_direction') : config('app.global.sort_direction');
        $sort_criteria = $this->searchResultSorting($sort_by ,$sort_direction );
//        $locations = $this->locationRep->searchByType($Special_location_wishes, $items_per_page, $sort_criteria);
        $month = $month_id !=null ? $month_id : config('app.global.default_urlaub_month');
        $locations = $this->locationRep->searchLocation($Special_location_wishes, $month, null,null,null, null ,null, null,$items_per_page,$sort_criteria);

        return view('pages.search_result', [
            'locations' => $locations,
            'items_per_page' => $items_per_page,
            'month'     => $month,
            'panorama_location_picture' => $this->panorama_location_picture_url,
            'main_location_picture' => $this->main_location_picture_url,
            'panorama_location_text' => $this->panorama_text_and_style,
            'sort_by_criteria'     => $sort_by_criteria,
            'total_locations' => $this->locationRep->searchLocationCount($Special_location_wishes, $month)
        ]);
    }
    function getSpecialLocationWishes($urlaub_type){
        switch ($urlaub_type){
            case 'strand-reise':
                $type_fied = 'list_beach';
                $this->panorama_location_picture_url ="img/subpages/strandurlaub_b.webp";
                $this->main_location_picture_url = "img/subpages/strandurlaub_s.webp";
                $this->panorama_text_and_style =  '<div class="txt1"><span>Meer</span><span>Wasser and</span><span>Strand</span></div>';
                break;
            case 'natur-reise':
                $type_fied = 'list_nature';
                $this->panorama_location_picture_url ="img/subpages/naturreise_b.webp";
                $this->main_location_picture_url = "img/subpages/naturreise_s.webp";
                $this->panorama_text_and_style =  '<div class="txt2"><span>Natürlich Urlaub</span><span>aber nur in</span><span>der Natur!</span></div>';
                break;
            case 'staedte-reise':
                $type_fied = 'list_citytravel';
                $this->panorama_location_picture_url ="img/subpages/staedtereise_b.webp";
                $this->main_location_picture_url = "img/subpages/staedtereise_s.webp";
                $this->panorama_text_and_style =  '<div class="txt1"><span>Kein Stillstand</span><span>Hauptsache Stadt</span><span></span></div>';
                break;
            case 'kultur-reise':
                $type_fied = 'list_culture';
                $this->panorama_location_picture_url ="img/subpages/kulturreise_b.webp";
                $this->main_location_picture_url = "img/subpages/kulturreise_s.webp";
                $this->panorama_text_and_style =  '<div class="txt2"><span>Culture Beat - </span><span>Kultur und Geschichte</span><span></span></div>';
                break;
            case 'insel-reise':
                $type_fied = 'list_island';
                $this->panorama_location_picture_url ="img/subpages/inselurlaub_b.webp";
                $this->main_location_picture_url = "img/subpages/inselurlaub_s.webp";
                $this->panorama_text_and_style =  '<div class="txt1"><span>Was würdest Du mit </span><span>auf eine einsame Insel</span><span>nehmen?</span></div>';
                break;
            case 'wintersport-reise':
                $type_fied = 'list_wintersport';
                $this->panorama_location_picture_url ="img/subpages/winterurlaub_b.webp";
                $this->main_location_picture_url = "img/subpages/winterurlaub_s.webp";
                $this->panorama_text_and_style =  '<div class="txt2"><span>Ich stehe auf </span><span>coolen Urlaub</span><span></span></div>';
                break;
            case 'sport-reise':
                $type_fied = 'list_sports';
                $this->panorama_location_picture_url ="img/subpages/sporturlaub_b.webp";
                $this->main_location_picture_url = "img/subpages/sporturlaub_s.webp";
                $this->panorama_text_and_style =  '<div class="txt2"><span>Zeit für mich - </span><span>mit Sport vor Ort</span><span></span></div>';
                break;
            default:
                $type_fied = null;
                $this->panorama_location_picture_url =null;
                $this->main_location_picture_url = null;
        }
        return $type_fied;
    }
    function searchResultCount(Request $request)
    {
        $sonnenstunden_where = $wassertemperatur_where =  '';
        $sonnenstunden = explode('_',$request->sonnenstunden);
        if($sonnenstunden[0] =="less"){
            $sonnenstunden_where = ["<=",  $sonnenstunden[1]];
        }elseif ($sonnenstunden[0] =="more"){
            $sonnenstunden_where = [">",  $sonnenstunden[1]];
        }
        $wassertemperatur = explode('_',$request->wassertemperatur);
        if($wassertemperatur[0] =="less"){
            $wassertemperatur_where = ["<=",$wassertemperatur[1]];
        }elseif ($wassertemperatur[0] =="more"){
            $wassertemperatur_where = [">",$wassertemperatur[1]];
        }
        $Special_location_wishes = $request->input('spezielle');
        $continent = $request->input('continent') ?? null;
        $price = $request->input('price') ?? null;
        $sonnenstunden = $request->input('sonnenstunden') ?? null;
        $wassertemperatur = $request->input('wassertemperatur') ?? null;
        $month_id = (int)$request->input('urlaub') ?? config('app.global.default_urlaub_month');

        return $this->locationRep->searchLocationCount($Special_location_wishes, $month_id, $continent,$price,$sonnenstunden, $wassertemperatur, $sonnenstunden_where, $wassertemperatur_where);
    }

    function findSpecial(Request $request, $special){
        $sort_by_criteria = $this->sort_by_criteria();
        $items_per_page = $request->items_per_page ?? config('custom.global.items_per_page');
        $sort_criteria = $this->searchResultSorting($request->sort_by, $request->sort_direction);
        $locations = $this->locationRep->searchSpecialLocation($special, $items_per_page, $sort_criteria);
        $month = $request->input('urlaub') ?? config('app.global.default_urlaub_month');
        return view('pages.search_result', [
            'locations' => $locations,
            'items_per_page' => $items_per_page,
            'month'     => $month,
            'sort_by_criteria'     => $sort_by_criteria,
        ]);
    }

    private function searchResultSorting($sort_by, $sort_direction='asc'){
        $sort_direction = ($sort_direction == 'desc') ? 'DESC' : 'ASC';
        $sort_by_feld_name =(isset($this->sort_by_criteria()[$sort_by]))? $this->sort_by_criteria()[$sort_by]['db_field'] : 'locations.title';
        return ['sort_by' => $sort_by_feld_name, 'sort_direction' => $sort_direction ];
    }

    private function sort_by_criteria()
    {
        return [
            'price' => ['title' => 'Preis', 'db_field' => 'locations.Price_Flight'],
            'location' => ['title' => 'Reiseziel', 'db_field' => 'locations.title'],
            'temperature' => ['title' => 'Tagestemperatur', 'db_field' => 'climates.daily_temperature'],
            'continents' => ['title' => 'Kontinent', 'db_field' => 'continents.title'],
            'countries' => ['title' => 'Land', 'db_field' => 'countries.title'],
            'flight_hours' => ['title' => 'Flugdauer', 'db_field' => 'locations.flight_hours'],
        ];
    }
}
