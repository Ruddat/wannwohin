<?php

namespace App\Http\Controllers\Frontend\DetailSearch;

use App\Models\WwdeRange;
use App\Models\WwdeClimate;
use App\Models\WwdeCountry;
use App\Models\ModLanguages;
use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WwdeContinent; // WwdeContinent importieren

class DetailSearchController extends Controller
{
    /**
     * Zeigt die Detail-Suche-Seite an.
     */
    public function index()
    {
        // Länder abrufen
        $countries = WwdeCountry::all();

        // Preisspannen abrufen
        $ranges = WwdeRange::where('Type', 'Flight')->orderBy('Sort')->get();

        // Sprachen
        $languages = ModLanguages::all();


        // Klimazonen aus Ländern extrahieren
        $climateZones = WwdeCountry::query()
            ->whereNotNull('climatezones_ids')
            ->distinct()
            ->pluck('climatezones_lnam', 'climatezones_ids')
            ->toArray();

        // Kontinente abrufen
        $continents = WwdeContinent::all();

        // Anzahl der Locations (Placeholder, bis eine Suche ausgeführt wird)
        $totalLocations = WwdeLocation::count();

        return view('frondend.detailSearch._index', [
            'countries' => $countries,
            'ranges' => $ranges,
            'climate_lnam' => $climateZones,
            'continents' => $continents, // Kontinente an die View übergeben
            'total_locations' => $totalLocations,
            'languages' => $languages,
        ]);
    }

    /**
     * Verarbeitet die Suchanfrage.
     */
    public function search(Request $request)
    {
        $query = WwdeLocation::query();

        // Monat filtern
        if ($request->filled('month')) {
            $query->where('best_traveltime', 'like', "%{$request->month}%");
        }

        // Preis filtern
        if ($request->filled('range_flight')) {
            $query->where('range_flight', $request->range_flight);
        }

        // Land filtern
        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        // Klimazone filtern
        if ($request->filled('climate_zone')) {
            $query->where('climatezones_ids', 'like', "%{$request->climate_zone}%");
        }

        // Ergebnisse abrufen
        $locations = $query->get();

        return view('pages.detailSearch.results', [
            'locations' => $locations,
        ]);
    }

        /**
     * Berechnet zukünftige Monate basierend auf vorhandenen Klimadaten.
     */
    public function predictFutureClimate($locationId)
    {
        // Hole vorhandene Monate für die Location
        $existingData = WwdeClimate::where('location_id', $locationId)
            ->orderBy('month_id')
            ->get();

        if ($existingData->isEmpty()) {
            return response()->json(['message' => 'Keine Klimadaten für diese Location verfügbar.'], 404);
        }

        // Berechnung zukünftiger Monate
        $futureMonths = WwdeClimate::predictFutureMonths($locationId);

        return view('pages.detailSearch.climate_forecast', compact('futureMonths'));
    }
}
