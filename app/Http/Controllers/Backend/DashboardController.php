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
        $totalParks     = AmusementParks::count();
        $totalImages    = ModLocationGalerie::count();

        // ===============================
        // 📊 Last 7 vs 30 Days
        // ===============================
        $last7Days = DB::table('stat_location_search_histories')
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('search_count');

        $last30Days = DB::table('stat_location_search_histories')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('search_count');

        $growthPercent = $last30Days > 0
            ? round((($last7Days - ($last30Days / 4)) / ($last30Days / 4)) * 100, 1)
            : 0;

        // ===============================
        // 🔥 Top Locations Aggregated
        // ===============================
        $topLocations = DB::table('stat_location_search_histories')
            ->join('wwde_locations', 'stat_location_search_histories.location_id', '=', 'wwde_locations.id')
            ->select(
                'wwde_locations.id',
                'wwde_locations.title',
                'wwde_locations.slug',
                'wwde_locations.lat',
                'wwde_locations.lon',
                DB::raw('SUM(stat_location_search_histories.search_count) as search_count')
            )
            ->whereNotNull('wwde_locations.lat')
            ->whereNotNull('wwde_locations.lon')
            ->groupBy(
                'wwde_locations.id',
                'wwde_locations.title',
                'wwde_locations.slug',
                'wwde_locations.lat',
                'wwde_locations.lon'
            )
            ->orderByDesc('search_count')
            ->limit(10)
            ->get();

        // ===============================
        // 🌍 Heatmap Data
        // ===============================
        $heatmapData = $topLocations->map(function ($loc) {
            return [
                'lat'   => (float)$loc->lat,
                'lng'   => (float)$loc->lon,
                'value' => (int)$loc->search_count,
            ];
        });

        // ===============================
        // 📊 Monthly Traffic Summary
        // ===============================
        $trafficSummary = DB::table('stat_location_search_histories')
            ->select(
                'month',
                DB::raw('SUM(search_count) as total_searches')
            )
            ->whereNotNull('month')
            ->groupBy('month')
            ->orderByRaw("STR_TO_DATE(month, '%Y-%m') ASC")
            ->get();


        // ===============================
        // 🟢 Active vs Pending Ratio
        // ===============================
        $activeLocations  = WwdeLocation::where('status', 'active')->count();
        $pendingLocations = WwdeLocation::where('status', 'pending')->count();

        return view('raadmin.index', compact(
            'totalLocations',
            'totalCountries',
            'totalParks',
            'totalImages',
            'topLocations',
            'last7Days',
            'last30Days',
            'growthPercent',
            'heatmapData',
            'activeLocations',
            'pendingLocations',
            'trafficSummary'
        ));
    }
}
