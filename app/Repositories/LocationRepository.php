<?php

namespace App\Repositories;

use App\Models\WwdeLocation;

class LocationRepository
{
    public function getTopTenLocations($status = 'active')
    {
        return WwdeLocation::with('country')
            ->where('finished', 1)
            ->where('status', $status) // Status-Abfrage hinzufügen
            ->get()
            ->sortByDesc(function ($location) {
               return $location->country->popularity ?? 0;
           })
            ->take(10);
    }

    public function getTotalFinishedLocations($status = 'active')
    {
        return WwdeLocation::where('finished', 1)
            ->where('status', $status) // Status-Abfrage hinzufügen
            ->count();
    }

    public function getLocationsByStatus($status)
    {
        return WwdeLocation::where('status', $status)->get();
    }

        /**
     * Holt Locations basierend auf Urlaubstyp und Monat.
     *
     * @param string $urlaubType
     * @param int $monthId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLocationsByTypeAndMonth($urlaubType, $monthName)
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
            ->where('status', 'active')
            ->where('finished', 1)
            ->where($column, true);

    }




}
