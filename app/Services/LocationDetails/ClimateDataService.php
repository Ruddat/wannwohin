<?php

namespace App\Services\LocationDetails;

use Carbon\Carbon;
use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Http;

class ClimateDataService
{
    /*
    |--------------------------------------------------------------------------
    | Prüfen ob Import nötig
    |--------------------------------------------------------------------------
    */
    public function needsImport(WwdeLocation $location): bool
    {
        $items = WwdeClimate::where('location_id', $location->id)->get();

        if ($items->count() === 0) return true;
        if ($items->count() < 12) return true;
        if ($items->where('year', null)->count() > 0) return true;
        if ($items->where('daily_temperature', 0)->count() > 0) return true;
        if ($items->where('water_temperature', 0.5)->count() > 0) return true;

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Klima abrufen (nur letztes Jahr)
    |--------------------------------------------------------------------------
    */
    public function get(WwdeLocation $location)
    {
        return WwdeClimate::where('location_id', $location->id)
            ->orderBy('year', 'desc')
            ->orderBy('month_id')
            ->get()
            ->unique('month_id')   // Doppelmonat entfernen
            ->sortBy('month_id')
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Importieren
    |--------------------------------------------------------------------------
    */
    public function import(WwdeLocation $location, ?int $year = null)
    {
        $year = $year ?? now()->year - 1;

        $lat = $location->lat;
        $lon = $location->lon;

        // ---- 1) Klima API ----
        $url = "https://climate-api.open-meteo.com/v1/climate?"
            . "latitude={$lat}&longitude={$lon}"
            . "&daily=temperature_2m_max,temperature_2m_min,"
            . "shortwave_radiation_sum,precipitation_sum"
            . "&start_date={$year}-01-01&end_date={$year}-12-31";

$response = Http::withOptions([
    'force_ip_resolve' => 'v4',
])->retry(5, 500)->get($url);

        if (!$response->successful()) {
            return ['error' => true, 'reason' => 'climate-api failed'];
        }

        $daily = $response->json()['daily'] ?? null;

        if (!$daily) {
            return ['error' => true, 'reason' => 'daily missing'];
        }


        // ---- 2) Monatsdaten aggregieren ----
        $monthly = [];

foreach ($daily['time'] as $i => $date) {

    $month = Carbon::parse($date)->month;

    // Tageswerte sammeln
    $monthly[$month]['max'][] = $daily['temperature_2m_max'][$i];
    $monthly[$month]['min'][] = $daily['temperature_2m_min'][$i];

    // Sonnenstunden
    $sun = ($daily['shortwave_radiation_sum'][$i] ?? 0) / 3.6;
    $monthly[$month]['sun'][] = $sun;

    // Regentag: >1mm
    $rain = $daily['precipitation_sum'][$i] ?? 0;
    $monthly[$month]['rain'][] = $rain > 1 ? 1 : 0;

    // 🔥 HIER muss humidity rein → innerhalb der foreach!
    $monthly[$month]['humidity'][] = $this->calculateHumidity(
        $daily['temperature_2m_max'][$i],
        $daily['precipitation_sum'][$i],
        $location
    );
}
;


        // ---- 3) Fake-Wasser berechnen ----
        $fakeWater = $this->generateFakeWaterTemps($monthly, $location);






        // ---- 4) Speichern ----
        foreach ($monthly as $month => $values) {

// Regenwahrscheinlichkeit
$rainProbability = count($values['rain'])
    ? round(array_sum($values['rain']) / count($values['rain']) * 100, 1)
    : null;

// UV Index – Näherungswert
$uv = $this->estimateUvIndex(
    $this->avg($values['sun']),
    $location->lat,
    $month
);

// Travel Index (0–10)
$travelIndex = $this->calculateTravelIndex(
    $this->avg($values['max']),
    $rainProbability,
    $this->avg($values['sun']),
    $this->avg($values['humidity'])
);

// Komfortscore (1–100)
$comfort = $this->calculateComfortScore(
    $this->avg($values['max']),
    $this->avg($values['humidity']),
    array_sum($values['rain']),
    $this->avg($values['sun'])
);

// Ø Windgeschwindigkeit (Fake)
$wind = $this->estimateWindSpeed($location->lat);





WwdeClimate::updateOrCreate(
    [
        'location_id' => $location->id,
        'month_id'    => $month,
        'year'        => $year
    ],
    [
        'daily_temperature' => $this->avg($values['max']),
        'night_temperature' => $this->avg($values['min']),
        'sunshine_per_day'  => $this->avg($values['sun']),
        'rainy_days'        => array_sum($values['rain']),
        'rain_probability'  => $rainProbability,
        'water_temperature' => $fakeWater[$month] ?? null,
        'humidity'          => $this->avg($values['humidity']),
        'uv_index'          => $uv,
        'travel_index'      => $travelIndex,
        'comfort_score'     => $comfort,
        'wind_speed_avg'    => $wind,
    ]
);
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Fake-Wassertemperaturen erzeugen (realistisch)
    |--------------------------------------------------------------------------
    */
    private function generateFakeWaterTemps(array $monthly, WwdeLocation $location): array
    {
        $zone = $this->climateZone($location->lat);
        $base = $this->baseWaterTempByZone($zone);

        $result = [];

        foreach ($monthly as $month => $values) {

            $air = $this->avg($values['max']);
            $sun = $this->avg($values['sun']);

            // Grundformel: Luft + Sonne beeinflussen Wasser
            $water =
                $base +
                ($air - $base) * 0.3 +   // 30% Lufttemperatur Einfluss
                ($sun * 0.5) +          // Sonnenstunden sehr wirksam
                rand(-7, 7) / 10;       // leichte Variation

            // ---- Realistische Begrenzungen ----

            // Wasser ist NIE wärmer als Luft + 3
            $water = min($water, $air + 3);

            // Wasser wird NIE kälter als Luft - 15
            $water = max($water, $air - 15);

            // Extrembereiche abfangen
            $water = max(-2, min(32, $water));

            $result[$month] = round($water, 1);
        }

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Klimazonen nach Breitengrad
    |--------------------------------------------------------------------------
    */
    private function climateZone($lat)
    {
        $lat = abs($lat);

        return match (true) {
            $lat < 23.5 => 'tropical',
            $lat < 40   => 'subtropical',
            $lat < 55   => 'temperate',
            default     => 'cold',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Basis-Wassertemperatur je Zone
    |--------------------------------------------------------------------------
    */
    private function baseWaterTempByZone($zone)
    {
        return match ($zone) {
            'tropical'    => 27,
            'subtropical' => 22,
            'temperate'   => 15,
            'cold'        => 5,
        };
    }
private function calculateHumidity($tempMax, $precipitation, WwdeLocation $location)
{
    // Klimazone bestimmen
    $zone = $this->climateZone($location->lat);

    // Basiswert je Klimazone
    $base = match ($zone) {
        'tropical'    => 75,  // Tropen immer feucht
        'subtropical' => 65,
        'temperate'   => 70,
        'cold'        => 80,
    };

    // Temperatur-Effekt
    $tempEffect = -($tempMax * 0.8);

    // Regen-Effekt (mehr Regen = mehr Luftfeuchtigkeit)
    $rainEffect = $precipitation * 1.2;

    // Leichte natürliche Variation
    $variation = rand(-5, 5);

    // Finale Formel
    $humidity = $base + $tempEffect + $rainEffect + $variation;

    // Werte clampen
    return max(20, min(95, round($humidity, 1)));
}


    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */
    private function avg($vals)
    {
        return round(array_sum($vals) / count($vals), 1);
    }

private function estimateUvIndex($sunHours, $lat, $month)
{
    $latFactor = max(0.6, (1 - abs($lat) / 90)); // Äquator = stärkere Sonne
    $seasonFactor = match ($month) {
        6,7,8 => 1.3,
        5,9   => 1.1,
        3,4,10 => 0.9,
        default => 0.6,
    };

    return round(min(12, $sunHours * 1.2 * $latFactor * $seasonFactor), 1);
}

private function calculateTravelIndex($temp, $rainProb, $sun, $humidity)
{
    $score = 0;

    // Temperatur ideal 22–28
    if ($temp >= 20 && $temp <= 30) $score += 4;
    elseif ($temp >= 15 && $temp <= 34) $score += 2;

    // Sonne
    $score += min(3, $sun / 3);

    // Regenwahrscheinlichkeit
    if ($rainProb < 20) $score += 2;
    elseif ($rainProb < 40) $score += 1;

    // Luftfeuchtigkeit
    if ($humidity < 70) $score += 1;

    return min(10, round($score));
}

private function calculateComfortScore($temp, $humidity, $rain, $sun)
{
    $score = 60;

    // Temperatur
    $score += (25 - abs(25 - $temp)) * 1.5; // je näher an 25°C, desto besser

    // Sonne
    $score += $sun * 2;

    // Regen
    $score -= $rain * 1.5;

    // Luftfeuchtigkeit
    if ($humidity > 65) $score -= ($humidity - 65) * 0.7;

    return max(1, min(100, round($score)));
}

private function estimateWindSpeed($lat)
{
    return match (true) {
        abs($lat) < 30 => rand(5, 15),
        abs($lat) < 50 => rand(5, 20),
        default         => rand(10, 25),
    };
}












}
