<?php

namespace App\Helpers;

class WeatherHelper
{
    /**
     * Wandelt die numerische Windrichtung in eine Himmelsrichtung um.
     *
     * @param float $degrees
     * @return string
     */
    public static function getWindDirection($degrees)
    {
        if ($degrees < 0 || $degrees > 360) {
            return 'Unbekannt';
        }

        $directions = [
            'N', 'NO', 'O', 'SO', 'S', 'SW', 'W', 'NW', 'N'
        ];

        $index = round($degrees / 45) % 8;
        return $directions[$index];
    }

    /**
     * Beschreibt die Windgeschwindigkeit in Kategorien.
     *
     * @param float $speed
     * @return string
     */
    public static function getWindDescription($speed)
    {
        if ($speed < 5) {
            return 'Windstille';
        } elseif ($speed < 20) {
            return 'Leichter Wind';
        } elseif ($speed < 40) {
            return 'Mäßiger Wind';
        } else {
            return 'Starker Wind';
        }
    }

    /**
     * Formatiert die Temperatur (rundet und fügt Einheit hinzu).
     *
     * @param float|null $temperature
     * @return string
     */
    public static function formatTemperature($temperature)
    {
        return $temperature !== null ? round($temperature) . ' °C' : 'Keine Daten';
    }

    /**
     * Formatiert die Windgeschwindigkeit (rundet und fügt Einheit hinzu).
     *
     * @param float|null $speed
     * @return string
     */
    public static function formatWindSpeed($speed)
    {
        return $speed !== null ? round($speed) . ' km/h' : 'Keine Daten';
    }

    /**
     * Gibt ein Standard-Wetter-Icon zurück, falls keins vorhanden ist.
     *
     * @param string|null $icon
     * @return string
     */
    public static function getWeatherIcon($icon)
    {
        return $icon ?? 'default-icon.png';
    }
}
