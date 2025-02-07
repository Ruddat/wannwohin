<?php

namespace App\Http\Controllers;

use App\Models\WwdeLocation;
use App\Models\ModLocationFilter;
use Illuminate\Http\Request;

class NewSearchController extends Controller
{
    /**
     * Erweiterte Live-Suche mit Kontinent, Land und bester Reisezeit.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $activity = $request->input('activity'); // Aktivitätsfilter
            $climate = $request->input('climate'); // Klima-Filter
            $budget = $request->input('budget'); // Budget-Filter
            $month = $request->input('month'); // Reisezeit-Filter

            if (!$query && !$activity && !$climate && !$budget && !$month) {
                return response()->json(['locations' => [], 'places' => []]);
            }

            // 🔍 Suche nach Locations
            $results = WwdeLocation::query()
                ->leftJoin('wwde_continents', 'wwde_locations.continent_id', '=', 'wwde_continents.id')
                ->leftJoin('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id')
                ->select(
                    'wwde_locations.id',
                    'wwde_locations.title',
                    'wwde_locations.alias',
                    'wwde_locations.iso2',
                    'wwde_continents.alias as continent_alias',
                    'wwde_countries.alias as country_alias',
                    'wwde_locations.best_traveltime_json'
                )
                ->where(function ($q) use ($query) {
                    if ($query) {
                        $q->where('wwde_locations.title', 'LIKE', "%$query%");
                    }
                });

            // 🎭 Sehenswürdigkeiten & Aktivitäten suchen
            $places = [];
            if ($query) {
                $places = ModLocationFilter::where('uschrift', 'LIKE', "%$query%")
                    ->orWhere('text', 'LIKE', "%$query%")
                    ->limit(8)
                    ->get(['id', 'location_id', 'text_type', 'uschrift', 'text']);

                if ($places->isNotEmpty()) {
                    $results = $results->orWhereIn('wwde_locations.id', $places->pluck('location_id'));
                }
            }

            // 🔥 Filter nach Aktivität
            if ($activity) {
                $results->whereHas('filters', function ($q) use ($activity) {
                    $q->where('text_type', 'LIKE', "%$activity%");
                });
            }

            // 🌡️ Klima-Filter
            if ($climate) {
                $results->whereHas('climate', function ($q) use ($climate) {
                    if ($climate === 'warm') {
                        $q->where('daily_temperature', '>', 20);
                    } elseif ($climate === 'kalt') {
                        $q->where('daily_temperature', '<', 10);
                    } elseif ($climate === 'mäßig') {
                        $q->whereBetween('daily_temperature', [10, 20]);
                    }
                });
            }

            // 💰 Budget-Filter
            if ($budget) {
                if ($budget === 'günstig') {
                    $results->where('price_hotel', '<', 50);
                } elseif ($budget === 'mittel') {
                    $results->whereBetween('price_hotel', [50, 150]);
                } elseif ($budget === 'teuer') {
                    $results->where('price_hotel', '>', 150);
                }
            }

            // 🗓️ Reisezeit-Filter
            if ($month) {
                $results->where('best_traveltime_json', 'LIKE', "%$month%");
            }

            // Ergebnisse abrufen
            $locations = $results->limit(10)->get();

            // 🌍 JSON-Fehlertoleranz für `best_traveltime_json`
            foreach ($locations as $location) {
                try {
                    $travelTimeArray = json_decode($location->best_traveltime_json, true);
                    $location->best_traveltime_text = is_array($travelTimeArray) ? implode(", ", $travelTimeArray) : "N/A";
                } catch (\Exception $e) {
                    $location->best_traveltime_text = "N/A"; // Falls JSON kaputt ist
                }
            }

            return response()->json([
                'locations' => $locations,
                'places' => $places ?? []
            ]);
        } catch (\Exception $e) {
            \Log::error('Fehler in search(): ' . $e->getMessage());
            return response()->json(['error' => 'Serverfehler'], 500);
        }
    }


/**
 * Zufälliges Reiseziel vorschlagen.
 */
public function randomDestination()
{
    try {
        // Prüfe, ob es überhaupt Locations gibt
        $randomLocation = WwdeLocation::query()
            ->leftJoin('wwde_continents', 'wwde_locations.continent_id', '=', 'wwde_continents.id')
            ->leftJoin('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id')
            ->select(
                'wwde_locations.id',
                'wwde_locations.title',
                'wwde_locations.alias',
                'wwde_locations.iso2',
                'wwde_continents.alias as continent_alias',
                'wwde_countries.alias as country_alias'
            )
            ->inRandomOrder()
            ->first();

        // Falls keine Location gefunden wurde
        if (!$randomLocation) {
            \Log::error('Kein zufälliges Reiseziel gefunden!');
            return response()->json(['error' => 'Kein Ziel gefunden'], 404);
        }

        // Debugging: Logge die gefundene Location
        \Log::info('Zufälliges Reiseziel gefunden:', [
            'id' => $randomLocation->id,
            'title' => $randomLocation->title,
            'continent' => $randomLocation->continent_alias ?? 'unknown',
            'country' => $randomLocation->country_alias ?? 'unknown',
            'alias' => $randomLocation->alias,
        ]);

        return response()->json([
            'url' => route('location.details', [
                'continent' => $randomLocation->continent_alias ?? 'unknown',
                'country' => $randomLocation->country_alias ?? 'unknown',
                'location' => $randomLocation->alias
            ])
        ]);
    } catch (\Exception $e) {
        \Log::error('Fehler in randomDestination(): ' . $e->getMessage());
        return response()->json(['error' => 'Serverfehler'], 500);
    }
}




}
