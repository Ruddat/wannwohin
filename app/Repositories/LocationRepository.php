<?php

namespace App\Repositories;

use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Storage;
use App\Library\WeatherDataManagerLibrary;

class LocationRepository
{
    public function getTopTenLocations($status = 'active')
    {
        return WwdeLocation::with('country')
            ->where('finished', 1)
            ->where('status', $status)
            ->get()
            ->sortByDesc(function ($location) {
                return $location->country->popularity ?? 0;
            })
            ->take(10);
    }

    public function getTotalFinishedLocations($status = 'active')
    {
        return WwdeLocation::where('finished', 1)
            ->where('status', $status)
            ->count();
    }

    public function getLocationsByStatus($status)
    {
        return WwdeLocation::where('status', $status)->get();
    }

    /**
     * Holt Locations basierend auf einem Array von Filtern und Monat.
     *
     * @param array $filters
     * @param int|null $monthId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getLocationsByFiltersAndMonth(array $filters, ?int $monthId = null)
    {
        $query = WwdeLocation::query()
            ->where('wwde_locations.status', 'active') // Explizite Tabellenangabe
            ->where('wwde_locations.finished', 1);     // Explizite Tabellenangabe

        // Filter anwenden (z. B. list_beach, list_nature)
        if (!empty($filters)) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters as $field => $value) {
                    $q->orWhere("wwde_locations.$field", '=', $value); // Explizite Tabellenangabe
                }
            });
        }

        // Monatsfilter, falls vorhanden
        if ($monthId) {
            $query->whereRaw("JSON_CONTAINS(wwde_locations.best_traveltime_json, ?)", [json_encode($monthId)]);
        }

        return $query;
    }

    /**
     * Holt Locations basierend auf Urlaubstyp und Monat (alte Methode, für Kompatibilität erhalten).
     *
     * @param string $urlaubType
     * @param int $monthId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getLocationsByTypeAndMonth($urlaubType, $monthId)
    {
        $urlaubTypeMap = [
            'strand-reise' => 'list_beach',
            'staedte-reise' => 'list_citytravel',
            'sport-reise' => 'list_sports',
            'insel-reise' => 'list_island',
            'kultur-reise' => 'list_culture',
            'natur-reise' => 'list_nature',
            'wassersport-reise' => 'list_watersport',
            'wintersport-reise' => 'list_wintersport',
            'mountainsport-reise' => 'list_mountainsport',
            'biking-reise' => 'list_biking',
            'fishing-reise' => 'list_fishing',
            'amusement-park-reise' => 'list_amusement_park',
            'water-park-reise' => 'list_water_park',
            'animal-park-reise' => 'list_animal_park',
        ];

        if (!array_key_exists($urlaubType, $urlaubTypeMap)) {
            throw new \InvalidArgumentException("Ungültiger Urlaubstyp: $urlaubType");
        }

        $column = $urlaubTypeMap[$urlaubType];

        return WwdeLocation::query()
            ->where('wwde_locations.status', 'active') // Explizite Tabellenangabe
            ->where('wwde_locations.finished', 1)      // Explizite Tabellenangabe
            ->where("wwde_locations.$column", true)    // Explizite Tabellenangabe
            ->whereRaw("JSON_CONTAINS(wwde_locations.best_traveltime_json, ?)", ["[$monthId]"]);
    }

    public function getLocationsByFilters($filters)
    {
        $query = WwdeLocation::query()
            ->select('wwde_locations.*')
            ->join('wwde_climates', 'wwde_locations.id', '=', 'wwde_climates.location_id')
            ->with(['country', 'continent'])
            ->where('wwde_locations.status', 'active')
            ->where('wwde_locations.finished', 1);

        if (!empty($filters['month']) && is_numeric($filters['month'])) {
            $query->whereRaw("JSON_CONTAINS(wwde_locations.best_traveltime_json, ?)", [json_encode((int) $filters['month'])]);
        }

        if (!empty($filters['continent'])) {
            $query->where('wwde_locations.continent_id', $filters['continent']);
        }

        if (!empty($filters['price'])) {
            $query->where('wwde_locations.price_flight', '<=', $filters['price']);
        }

        if (!empty($filters['sonnenstunden'])) {
            $query->where('wwde_climates.sunshine_per_day', '>=', $filters['sonnenstunden']);
        }

        if (!empty($filters['wassertemperatur'])) {
            $query->where('wwde_climates.water_temperature', '>=', $filters['wassertemperatur']);
        }

        if (!empty($filters['spezielle'])) {
            foreach ($filters['spezielle'] as $wish) {
                $query->where("wwde_locations.$wish", 1);
            }
        }

        return $query;
    }

    public function formatLocations($locations, $withWeatherData = false)
    {
        $weatherDataManager = new WeatherDataManagerLibrary();

        foreach ($locations as $location) {
            if ($withWeatherData) {
                $location->climate_data = $weatherDataManager->fetchAndStoreWeatherData(
                    $location->lat,
                    $location->lon,
                    $location->id
                );
            }
        }

        return $locations;
    }

    public function getHeaderContent()
    {
        $headerContent = \Cache::remember('header_content_random', 5 * 60, function () {
            return \App\Models\HeaderContent::inRandomOrder()->first();
        });

        if (!$headerContent) {
            return [
                'headerContent' => null,
                'bgImgPath' => null,
                'mainImgPath' => null,
                'mainText' => null,
            ];
        }

        $bgImgPath = $this->getValidImagePath($headerContent->bg_img);
        $mainImgPath = $this->getValidImagePath($headerContent->main_img);

        return [
            'headerContent' => $headerContent,
            'bgImgPath' => $bgImgPath,
            'mainImgPath' => $mainImgPath,
            'mainText' => $headerContent->main_text ?? null,
        ];
    }

    private function getValidImagePath($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        if (Storage::exists($imagePath)) {
            return Storage::url($imagePath);
        }

        if (file_exists(public_path($imagePath))) {
            return asset($imagePath);
        }

        return null;
    }
}
