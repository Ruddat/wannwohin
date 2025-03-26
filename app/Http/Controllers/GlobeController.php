<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobeController extends Controller
{
    public function index()
    {
        $locations = DB::table('wwde_locations')
            ->where('status', 'active')
            ->whereNotNull('lat')
            ->whereNotNull('lon')
            ->select(
                'title as name',
                'lat as latitude',
                'lon as longitude',
                'text_pic1 as image_url',
                'text_short as description'
            )
            ->get();

        // Debugging: Logge die Daten
        \Log::info('Locations geladen:', $locations->toArray());

        return response()->json($locations);
    }

    public function show()
    {
        return view('globe');
    }
}
