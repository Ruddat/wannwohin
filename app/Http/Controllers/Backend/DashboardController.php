<?php

namespace App\Http\Controllers\Backend;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Models\AmusementParks;
use App\Models\ModLocationGalerie;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLocations = WwdeLocation::count();
        $totalCountries = WwdeCountry::count();
        $totalParks = AmusementParks::count();
        $totalImages = ModLocationGalerie::count();

    // Top-10 Location Statistiken
    $topLocations = DB::table('stat_top_ten_locations')
        ->join('wwde_locations', 'stat_top_ten_locations.location_id', '=', 'wwde_locations.id')
        ->select('wwde_locations.title', 'stat_top_ten_locations.search_count', 'wwde_locations.lat', 'wwde_locations.lon')
        ->orderBy('stat_top_ten_locations.search_count', 'desc')
        ->limit(10)
        ->get();

    // Monatliche Zusammenfassung
    $trafficSummary = DB::table('stat_location_search_histories')
        ->select(DB::raw("DATE_FORMAT(month, '%Y-%m') as month"), DB::raw('SUM(search_count) as total_searches'))
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();


    return view('backend.admin.dashboard.index', compact(
        'totalLocations',
        'totalCountries',
        'totalParks',
        'totalImages',
        'topLocations',
        'trafficSummary',
    ));

    }
}
