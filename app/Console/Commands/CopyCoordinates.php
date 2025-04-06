<?php

namespace App\Console\Commands;

use App\Models\WwdeLocation;
use Illuminate\Console\Command;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Http;

class CopyCoordinates extends Command
{
    protected $signature = 'copy:coordinates';
    protected $description = 'Copy coordinates from wwde_locations to mod_location_filters with precise geocoding using OpenTripMap';

    // Liste generischer Aktivitäten, die nicht geocodiert werden sollen
    protected $genericActivities = [
        'joggen',
        'laufen',
        'radfahren',
        'shopping',
        'kulinarische genüsse',
        'nachtclubs und nachtleben',
        'skyline',
        'pferdereiten',
        'schwimmen',
        'schnorcheln und tauchen',
        'strände',
        'quad-touren',
        'die feste und veranstaltungen',
    ];

    public function handle()
    {
        $filters = ModLocationFilter::where('is_active', 1)
            ->whereNull('latitude') // Nur Einträge ohne Koordinaten
            ->get();

        if ($filters->isEmpty()) {
            $this->info('No activities need geocoding.');
            return;
        }

        foreach ($filters as $filter) {
            // Hole die zugehörige Stadt aus wwde_locations
            $location = WwdeLocation::find($filter->location_id);

            if (!$location) {
                $this->error("No location found for location_id {$filter->location_id} (Activity: {$filter->uschrift})");
                continue;
            }

            // Prüfe, ob die Stadt Koordinaten hat
            if (is_null($location->lat_new) || is_null($location->lon_new)) {
                $this->error("No coordinates available for location_id {$filter->location_id} (City: {$location->title})");
                continue;
            }

            // Prüfe, ob die Aktivität generisch ist
            $isGeneric = in_array(strtolower($filter->uschrift), $this->genericActivities);

            if ($isGeneric) {
                $this->info("Skipping geocoding for generic activity: {$filter->uschrift}");
                $filter->latitude = $location->lat_new;
                $filter->longitude = $location->lon_new;
                $this->warn("Used fallback coordinates for {$filter->uschrift}: Lat: {$location->lat_new}, Lng: {$location->lon_new}");
            } else {
                // Entferne deutsche Präfixe wie "Die" aus dem Aktivitätsnamen
                $cleanedActivity = preg_replace('/\b(Die|Der|Das)\b\s*/i', '', $filter->uschrift);
                $this->info("Attempting to geocode activity: {$cleanedActivity} near {$location->title}");
                $coordinates = $this->geocodeWithOpenTripMap($cleanedActivity, $location->lat_new, $location->lon_new);

                if ($coordinates) {
                    $filter->latitude = $coordinates['lat'];
                    $filter->longitude = $coordinates['lon'];
                    $this->info("Geocoded {$filter->uschrift}: Lat: {$coordinates['lat']}, Lng: {$coordinates['lon']}");
                } else {
                    // Fallback: Verwende die Stadtkoordinaten aus wwde_locations
                    $filter->latitude = $location->lat_new;
                    $filter->longitude = $location->lon_new;
                    $this->warn("Used fallback coordinates for {$filter->uschrift}: Lat: {$location->lat_new}, Lng: {$location->lon_new}");
                }
            }

            $filter->save();

            // OpenTripMap Rate-Limit: 1 Anfrage pro Sekunde (nur wenn geocodiert wird)
            if (!$isGeneric) {
                sleep(1);
            }
        }

        $this->info('Coordinate copying completed!');
    }

    private function geocodeWithOpenTripMap($activity, $cityLat, $cityLon)
    {
        try {
            // Hole den API-Schlüssel aus der Konfiguration
            $apiKey = config('services.opentripmap.key');
            if (empty($apiKey)) {
                $this->error("OpenTripMap API key is missing in config/services.php");
                return null;
            }

            // Nutze den /places/autosuggest-Endpunkt mit Sprache "en"
            $response = Http::get('https://api.opentripmap.com/0.1/en/places/autosuggest', [
                'name' => $activity,
                'radius' => 50000, // 50 km Radius
                'lon' => $cityLon,
                'lat' => $cityLat,
                'format' => 'json',
                'apikey' => $apiKey,
            ]);

            if ($response->failed()) {
                $this->error("OpenTripMap request failed: HTTP {$response->status()} - Response: {$response->body()}");
                return null;
            }

            $data = $response->json();

            // Prüfe, ob die Antwort leer ist
            if (empty($data)) {
                $this->error("OpenTripMap returned empty response for activity: {$activity}");
                return null;
            }

            // Unterstütze beide Strukturen: GeoJSON (features) und flache Liste
            $place = null;
            if (isset($data['features']) && !empty($data['features'])) {
                // GeoJSON-Struktur (von /places/autosuggest)
                $place = $data['features'][0];
                $this->info("OpenTripMap found (GeoJSON): {$place['properties']['name']}");
                return [
                    'lat' => $place['geometry']['coordinates'][1], // [lon, lat]
                    'lon' => $place['geometry']['coordinates'][0],
                ];
            } elseif (isset($data[0])) {
                // Flache Liste (von /places/radius oder manuelle Anfrage)
                $place = $data[0];
                $this->info("OpenTripMap found (List): {$place['name']}");
                return [
                    'lat' => $place['point']['lat'],
                    'lon' => $place['point']['lon'],
                ];
            }

            $this->error("OpenTripMap found no results for activity: {$activity}");
            return null;
        } catch (\Exception $e) {
            $this->error("Geocoding failed for activity {$activity}: {$e->getMessage()}");
            return null;
        }
    }
}
