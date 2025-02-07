<?php

namespace App\Http\Controllers;

use App\Models\WwdeLocation;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Live-Suche für Reiseziele.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        // Suche nach Locations, Ländern oder Kontinenten
        $results = WwdeLocation::where('title', 'LIKE', "%$query%")
            ->orWhereHas('country', function ($q) use ($query) {
                $q->where('title', 'LIKE', "%$query%");
            })
            ->orWhereHas('continent', function ($q) use ($query) {
                $q->where('title', 'LIKE', "%$query%");
            })
            ->limit(8)
            ->get(['id', 'title', 'alias', 'iso2']);

        return response()->json($results);
    }

    /**
     * Zufälliges Reiseziel vorschlagen.
     */
    public function random()
    {
        $randomLocation = WwdeLocation::inRandomOrder()->first(['id', 'title', 'alias', 'iso2']);

        if (!$randomLocation) {
            return response()->json(['error' => 'Kein Ziel gefunden'], 404);
        }

        return response()->json([
            'url' => route('location.details', [
                'continent' => 'unknown',
                'country' => 'unknown',
                'location' => $randomLocation->alias,
            ])
        ]);
    }
}
