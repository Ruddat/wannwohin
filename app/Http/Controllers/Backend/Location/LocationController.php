<?php

namespace App\Http\Controllers\Backend\Location;

use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Models\WwdeContinent;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = WwdeLocation::all();
        return view('backend.admin.location.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $continents = WwdeContinent::all();
        $countries = WwdeCountry::all();

        return view('backend.admin.location.create', compact('continents', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateLocation($request);

        WwdeLocation::create($validated);

        return redirect()->route('location-manager.locations.index')->with('success', 'Location created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WwdeLocation $location)
    {
        $continents = WwdeContinent::all();
        $countries = WwdeCountry::where('continent_id', $location->continent_id)->get();

        return view('backend.admin.location.edit', compact('location', 'continents', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WwdeLocation $location)
    {
        $validated = $this->validateLocation($request);

        $location->update($validated);

        return redirect()->route('location-manager.locations.index')->with('success', 'Location updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WwdeLocation $location)
    {
        $location->delete();

        return redirect()->route('location-manager.locations.index')->with('success', 'Location deleted successfully!');
    }

    /**
     * Validate location data.
     */
    private function validateLocation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:50',
            'alias' => 'nullable|string|max:50',
            'iata_code' => 'nullable|string|max:5',
            'flight_hours' => 'nullable|numeric',
            'stop_over' => 'nullable|integer',
            'dist_from_FRA' => 'nullable|integer',
            'dist_type' => 'nullable|string|max:50',
            'lat' => 'nullable|string|max:50',
            'lon' => 'nullable|string|max:50',
            'bundesstaat_long' => 'nullable|string|max:50',
            'bundesstaat_short' => 'nullable|string|max:50',
            'list_beach' => 'nullable|boolean',
            'list_citytravel' => 'nullable|boolean',
            'list_sports' => 'nullable|boolean',
            'list_island' => 'nullable|boolean',
            'list_culture' => 'nullable|boolean',
            'list_nature' => 'nullable|boolean',
            'best_traveltime' => 'nullable|string|max:28',
            'text_pic1' => 'nullable|string|max:300',
            'text_pic2' => 'nullable|string|max:300',
            'text_pic3' => 'nullable|string|max:300',
            'text_headline' => 'nullable|string|max:255',
            'text_short' => 'nullable|string|max:1000',
            'text_location_climate' => 'nullable|string',
            'text_what_to_do' => 'nullable|string',
            'text_best_traveltime' => 'nullable|string',
            'text_sports' => 'nullable|string',
            'text_amusement_parks' => 'nullable|string',
            'price_flight' => 'nullable|integer',
            'range_flight' => 'nullable|integer',
            'price_hotel' => 'nullable|integer',
            'range_hotel' => 'nullable|integer',
            'price_rental' => 'nullable|integer',
            'range_rental' => 'nullable|integer',
            'price_travel' => 'nullable|integer',
            'range_travel' => 'nullable|integer',
            'finished' => 'nullable|boolean',
            'time_zone' => 'nullable|string|max:255',
            'lat_new' => 'nullable|string|max:255',
            'lon_new' => 'nullable|string|max:255',
        ]);
    }
}
