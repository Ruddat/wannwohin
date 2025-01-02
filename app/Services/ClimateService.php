<?php

namespace App\Services;

use App\Models\WwdeClimate;
use App\Library\WeatherDataManagerLibrary;
use Carbon\Carbon;

class ClimateService
{
    protected $weatherDataManager;

    public function __construct(WeatherDataManagerLibrary $weatherDataManager)
    {
        $this->weatherDataManager = $weatherDataManager;
    }

    /**
     * Gibt die Klimadaten für einen Standort zurück.
     * Falls keine Daten für den ausgewählten Monat vorhanden sind, werden die letzten vorhandenen Daten verwendet.
     *
     * @param object $location
     * @param string $monthName
     * @return array
     */
    public function getClimateData($location, $monthName)
    {
        // Daten aktualisieren (falls notwendig)
        $this->weatherDataManager->fetchAndStoreWeatherData($location->lat, $location->lon, $location->id);
//dd($location, $monthName);

        // Feste Klimadaten für den ausgewählten Monat abrufen
        $climate = WwdeClimate::where('location_id', $location->id)
            ->where('month', $monthName)
            ->first();
//dd($climate);
        // Falls keine Daten für den ausgewählten Monat vorhanden sind, verwende die letzten vorhandenen Daten
        if (!$climate) {
            $climate = WwdeClimate::where('location_id', $location->id)
                ->orderBy('created_at', 'desc') // Neueste Daten zuerst
                ->first();
//dd($climate);
            }

        // Standardwerte, falls keine Daten gefunden wurden
        return [
            'daily_temperature' => $climate->daily_temperature ?? 'N/A',
            'sunshine_per_day' => $climate->sunshine_per_day ?? 'N/A',
            'water_temperature' => $climate->water_temperature ?? 'N/A',
        ];
    }
}
