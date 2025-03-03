<?php

namespace App\Http\Controllers\Frontend\LocationDetails;

use DateTime;
use Carbon\Carbon;
use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\MonthlyClimateSummary;
use Illuminate\Support\Facades\Cache;
use App\Services\LocationImageService;
use Illuminate\Support\Facades\Storage;
use App\Library\WeatherDataManagerLibrary;

class LocationDetailsController extends Controller
{
    protected $imageService;
    protected $weatherService;

    public function __construct(LocationImageService $imageService, WeatherService $weatherService)
    {
        $this->imageService = $imageService;
        $this->weatherService = $weatherService;
    }

    /**
     * Zeigt die Details eines Standorts an, einschließlich Wetter- und Klimadaten.
     *
     * @param string $continentAlias
     * @param string $countryAlias
     * @param string $locationAlias
     * @return \Illuminate\View\View
     */
    public function show(string $continentAlias, string $countryAlias, string $locationAlias)
    {
        $location = $this->fetchLocation($continentAlias, $countryAlias, $locationAlias);
        $this->updateTopTen($location->id);

        $weather = $this->fetchAllWeatherData($location);
        $galleryImages = $this->fetchGalleryImages($location);
        $parksWithOpeningTimes = $this->fetchAmusementParks($location);
        $timeInfo = $this->getLocationTimeInfo($location);
        $priceTrend = $this->calculatePriceTrend($location->iso2 ?? 'DE');
        $bestTravelMonths = $this->parseBestTravelMonths($location->best_traveltime_json);

        // Klimadaten für das laufende Jahr (2025)
        $currentYear = date('Y'); // 2025
     ///   dd($currentYear);
      //  $climates = $this->fetchClimateYearData($location, $currentYear);
      //  $climates = $this->fetchSeasonalClimateYearData($location, $currentYear);

// Hole und speichere Klimadaten für das Vorjahr (2024)
//$climates = $this->fetchHistoricalClimateYearData($location, $currentYear);
$climates = $this->fetchAndStoreClimateData($location, $currentYear);

// Klimadaten für das Vorjahr (2024) aus der Datenbank
$currentYear = date('Y'); // 2025
$climates = WwdeClimate::where('location_id', $location->id)
    ->where('year', $currentYear - 1) // Vorjahr 2024
    ->orderBy('month_id', 'asc')
    ->get();


    //  dd($climates);



        return view('frondend.locationdetails._index', [
            'location' => $location,
            'electric_standard' => $location->electricStandard,
            'climates' => $climates,
            'averages' => MonthlyClimateSummary::where('location_id', $location->id)->first(),
            'main_image_path' => $location->main_img ? Storage::url($location->main_img) : null,
            'gallery_images' => $galleryImages,
            'parks_with_opening_times' => $parksWithOpeningTimes,
            'panorama_location_picture' => $location->panorama_text_and_style ?? asset('default-bg.jpg'),
            'forecast' => $weather['forecast'],
            'pic1_text' => $location->text_pic1 ?? 'Standard Text für Bild 1',
            'pic2_text' => $location->text_pic2 ?? 'Standard Text für Bild 2',
            'pic3_text' => $location->text_pic3 ?? 'Standard Text für Bild 3',
            'head_line' => $location->title ?? 'Standard Headline',
            'panorama_titel' => $location->panorama_title ?? 'Standard Panorama Title',
            'panorama_short_text' => $location->panorama_short_text ?? 'Standard Panorama Short Title',
            'current_time' => $timeInfo['current_time'],
            'time_offset' => $timeInfo['offset'],
            'panorama_text_and_style' => json_decode($location->panorama_text_and_style, true),
            'best_travel_months' => $bestTravelMonths,
            'price_trend' => $priceTrend,
            'hourly_weather' => $weather['hourly'],
            'weather_data_widget' => $weather['current'],
        ]);
    }

    /**
     * Holt einen Standort basierend auf Alias-Werten.
     *
     * @param string $continentAlias
     * @param string $countryAlias
     * @param string $locationAlias
     * @return WwdeLocation
     */
    private function fetchLocation(string $continentAlias, string $countryAlias, string $locationAlias): WwdeLocation
    {
        return WwdeLocation::where('alias', $locationAlias)
            ->whereHas('country', fn($query) => $query->where('alias', $countryAlias))
            ->whereHas('country.continent', fn($query) => $query->where('alias', $continentAlias))
            ->with(['electric', 'country', 'country.continent'])
            ->firstOrFail();
    }

    /**
     * Holt Galeriebilder für einen Standort.
     *
     * @param WwdeLocation $location
     * @return array
     */
    private function fetchGalleryImages(WwdeLocation $location): array
    {
        $galleryImages = [];
        \App\Models\ModLocationGalerie::where('location_id', $location->id)
            ->chunk(100, function ($items) use (&$galleryImages) {
                $galleryImages = array_merge($galleryImages, $items->map(function ($item) {
                    $url = $item->image_path ? asset('storage/' . $item->image_path) : null;
                    $relativePath = $url ? ltrim(parse_url($url, PHP_URL_PATH), '/') : null;
                    $cacheKey = 'file_exists_' . md5($relativePath ?? '');

                    $fileExists = $relativePath ? Cache::remember($cacheKey, now()->addHours(1), fn() => Storage::exists($relativePath)) : false;
                    if ($relativePath && !$fileExists && strpos($url, '/storage/img') !== false) {
                        $url = str_replace('/storage', '', $url);
                    }

                    return [
                        'url' => $url,
                        'description' => $item->description ?? 'Keine Beschreibung verfügbar',
                        'activity' => $item->activity ?? 'Allgemein',
                        'image_caption' => $item->image_caption ?? 'Kein Titel verfügbar',
                    ];
                })->toArray());
            });
        return $galleryImages;
    }

    private function fetchAndStoreClimateData(WwdeLocation $location, int $year): void
    {
        $cacheKey = "climate_data_{$location->id}_{$year}";
        $previousYear = $year - 1; // Vorjahr (2024, wenn $year = 2025)

        // Debugging der Koordinaten
        if (!$location->lat || !$location->lon) {
            Log::error("Ungültige Koordinaten für Standort {$location->id}: lat={$location->lat}, lon={$location->lon}");
            return;
        }

        // Versuche, gecachte Daten zu nutzen
        $climateData = Cache::remember($cacheKey, 24 * 60 * 60, function () use ($location, $previousYear) {
            Log::debug("Open-Meteo Historical Weather Request für Standort {$location->id}: latitude={$location->lat}, longitude={$location->lon}, year={$previousYear}");

            $response = Http::get('https://archive-api.open-meteo.com/v1/archive', [
                'latitude' => $location->lat,
                'longitude' => $location->lon,
                'start_date' => "$previousYear-01-01",
                'end_date' => "$previousYear-12-31",
                'daily' => 'weather_code,temperature_2m_max,sunshine_duration,temperature_2m_min,sunrise,sunset,daylight_duration,precipitation_sum,rain_sum,snowfall_sum,precipitation_hours,wind_direction_10m_dominant',
                'timezone' => 'auto',
            ]);

            Log::debug("Open-Meteo Historical Weather Response: Status={$response->status()}, Body=" . $response->body());

            if (!$response->successful()) {
                Log::error("API-Fehler bei Open-Meteo Historical Weather für {$location->id} im Jahr {$previousYear}: Status-Code {$response->status()}, Body: " . $response->body());
                return null;
            }

            $data = $response->json()['daily'];
            $climates = [];

            // Aggregiere tägliche Daten zu monatlichen Durchschnittswerten
            $monthlyData = [];
            foreach ($data['time'] as $key => $date) {
                $month = Carbon::parse($date)->month;
                if (!isset($monthlyData[$month])) {
                    $monthlyData[$month] = [
                        'temps_max' => [],
                        'temps_min' => [],
                        'sunshine_durations' => [],
                        'precipitation_sums' => [],
                        'precipitation_hours' => [],
                        'weather_codes' => [],
                        'wind_directions' => [],
                        'sunrises' => [],
                        'sunsets' => [],
                    ];
                }

                // Debugging der Werte
                Log::debug("Tägliche Daten für {$date}: weather_code=" . ($data['weather_code'][$key] ?? 'null'));

                $monthlyData[$month]['temps_max'][] = $data['temperature_2m_max'][$key] ?? null;
                $monthlyData[$month]['temps_min'][] = $data['temperature_2m_min'][$key] ?? null;
                $monthlyData[$month]['sunshine_durations'][] = $data['sunshine_duration'][$key] ?? null;
                $monthlyData[$month]['precipitation_sums'][] = $data['precipitation_sum'][$key] ?? null;
                $monthlyData[$month]['precipitation_hours'][] = $data['precipitation_hours'][$key] ?? null;
                $monthlyData[$month]['weather_codes'][] = $data['weather_code'][$key] ?? null;
                $monthlyData[$month]['wind_directions'][] = $data['wind_direction_10m_dominant'][$key] ?? null;
                $monthlyData[$month]['sunrises'][] = $data['sunrise'][$key] ?? null;
                $monthlyData[$month]['sunsets'][] = $data['sunset'][$key] ?? null;
            }

            // Konvertiere tägliche Daten in monatliche Durchschnittswerte
            foreach ($monthlyData as $month => $values) {
                // Debugging der monatlichen Werte
                Log::debug("Monatliche Daten für Monat {$month}: weather_codes=" . json_encode($values['weather_codes']));

                $weatherCodes = collect($values['weather_codes'])->filter()->all(); // Filtere null-Werte
                $weatherId = null;
                if (!empty($weatherCodes)) {
                    $mode = collect($weatherCodes)->mode();
                    $weatherId = is_array($mode) ? (isset($mode[0]) ? $mode[0] : null) : $mode; // Nimm den ersten Wert aus mode(), falls ein Array
                }

                $climates[$month] = [
                    'year' => $previousYear,
                    'location_id' => $location->id,
                    'month_id' => sprintf('%02d', $month),
                    'month' => Carbon::create()->month($month)->format('F'),
                    'daily_temperature' => !empty($values['temps_max']) ? $this->safeAvg($values['temps_max']) : null,
                    'night_temperature' => !empty($values['temps_min']) ? $this->safeAvg($values['temps_min']) : null,
                    'water_temperature' => null,
                    'humidity' => null,
                    'sunshine_per_day' => !empty($values['sunshine_durations']) ? $this->safeAvg($values['sunshine_durations']) / 3600 : null,
                    'rainy_days' => !empty($values['precipitation_hours']) ? $this->safeSum($values['precipitation_hours']) / 24 : null,
                    'cloudiness' => null,
                    'weather_id' => $weatherId, // Sicherstellen, dass nur ein Integer oder null gesetzt wird
                    'feels_like' => null,
                    'temp_min' => !empty($values['temps_min']) ? collect($values['temps_min'])->min() : null,
                    'temp_max' => !empty($values['temps_max']) ? collect($values['temps_max'])->max() : null,
                    'pressure' => null,
                    'wind_speed' => null,
                  //  'wind_direction' => !empty($values['wind_directions']) ? $this->getDominantWindDirection($values['wind_directions']) : null,
                    'wind_deg' => !empty($values['wind_directions']) ? $this->safeAvg($values['wind_directions']) : null,
                    'clouds_all' => null,
                    'dt' => !empty($values['time']) ? Carbon::parse(end($data['time']))->timestamp : null,
                    'timezone' => 'auto',
                 //   'country' => $location->country->alias ?? null,
                    'sunrise' => !empty($values['sunrises']) ? $this->safeAvg($values['sunrises']) : null,
                    'sunset' => !empty($values['sunsets']) ? $this->safeAvg($values['sunsets']) : null,
                    'weather_main' => $this->mapWeatherCode($weatherId),
                    'weather_description' => $this->mapWeatherCode($weatherId, true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            return $climates;
        });

        if ($climateData) {
            // Speichere die Daten in der Datenbank
            foreach ($climateData as $monthData) {
                // Anpassung für 'timezone', falls es ein String ist
                if ($monthData['timezone'] === 'auto') {
                    $monthData['timezone'] = null; // Setze auf null, falls 'auto'
                } elseif (is_string($monthData['timezone'])) {
                    try {
                        $timezone = new DateTimeZone($monthData['timezone']);
                        $offset = $timezone->getOffset(new DateTime());
                        $monthData['timezone'] = $offset; // Speichere Offset als Integer
                    } catch (\Exception $e) {
                        $monthData['timezone'] = null;
                        Log::warning("Ungültige Zeitzone für Standort {$location->id}: {$monthData['timezone']}");
                    }
                }

                WwdeClimate::updateOrCreate(
                    [
                        'location_id' => $location->id,
                        'year' => $monthData['year'],
                        'month_id' => $monthData['month_id'],
                    ],
                    $monthData
                );
            }
        }
    }

    // Neue Hilfsmethoden für sichere Berechnungen (unverändert)
    private function safeAvg(array $values): ?float
    {
        $numericValues = array_filter($values, function ($value) {
            return is_numeric($value) && $value !== null;
        });

        return empty($numericValues) ? null : array_sum($numericValues) / count($numericValues);
    }

    private function safeSum(array $values): ?float
    {
        $numericValues = array_filter($values, function ($value) {
            return is_numeric($value) && $value !== null;
        });

        return empty($numericValues) ? null : array_sum($numericValues);
    }

    // Neue Hilfsmethode für die dominante Windrichtung (unverändert)
    private function getDominantWindDirection(array $directions): ?string
    {
        if (empty($directions)) {
            return null;
        }

        $directionCounts = [
            'N' => 0, 'NE' => 0, 'E' => 0, 'SE' => 0,
            'S' => 0, 'SW' => 0, 'W' => 0, 'NW' => 0,
        ];

        foreach ($directions as $deg) {
            if ($deg !== null && is_numeric($deg)) {
                $index = round($deg / 45) % 8;
                $directionsArray = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
                $direction = $directionsArray[$index];
                $directionCounts[$direction]++;
            }
        }

        return array_search(max($directionCounts), $directionCounts);
    }

    private function fetchHistoricalClimateYearData(WwdeLocation $location, int $year): array
    {
        $cacheKey = "historical_climate_year_{$location->id}_{$year}";
        return Cache::remember($cacheKey, 24 * 60 * 60, function () use ($location, $year) {
            $climates = [];
            $previousYear = $year - 1; // Vorjahr (2024, wenn $year = 2025)

            for ($month = 1; $month <= 12; $month++) {
                $climate = WwdeClimate::where('location_id', $location->id)
                    ->where('month_id', $month)
                    ->where('year', $previousYear) // Filtere nach Vorjahr
                    ->first();

                $climates[$month] = [
                    'month_id' => sprintf('%02d', $month),
                    'daily_temperature' => $climate ? $climate->daily_temperature : null,
                    'night_temperature' => $climate ? $climate->night_temperature : null,
                    'water_temperature' => $climate ? $climate->water_temperature : null,
                    'humidity' => $climate ? $climate->humidity : null,
                    'sunshine_per_day' => $climate ? $climate->sunshine_per_day : null,
                    'rainy_days' => $climate ? $climate->rainy_days : null,
                ];
            }

            // Falls keine Daten für das Vorjahr vorhanden sind, hole historische Durchschnittswerte
            if (empty(array_filter($climates, fn($data) => $data['daily_temperature'] !== null))) {
                Log::warning("Keine Daten für das Vorjahr {$previousYear} gefunden, hole historische Durchschnittswerte.");
                for ($month = 1; $month <= 12; $month++) {
                    $climate = WwdeClimate::where('location_id', $location->id)
                        ->where('month_id', $month)
                        ->first(); // Hole den ersten Eintrag für diesen Monat (historischer Durchschnitt)

                    $climates[$month] = [
                        'month_id' => sprintf('%02d', $month),
                        'daily_temperature' => $climate ? $climate->daily_temperature : null,
                        'night_temperature' => $climate ? $climate->night_temperature : null,
                        'water_temperature' => $climate ? $climate->water_temperature : null,
                        'humidity' => $climate ? $climate->humidity : null,
                        'sunshine_per_day' => $climate ? $climate->sunshine_per_day : null,
                        'rainy_days' => $climate ? $climate->rainy_days : null,
                    ];
                }
            }

            return array_values($climates);
        });
    }


    /**
     * Holt alle Wetterdaten (aktuell, stündlich, täglich) für einen Standort.
     *
     * @param WwdeLocation $location
     * @return array
     */
    private function fetchAllWeatherData(WwdeLocation $location): array
    {
        $cacheKey = "all_weather_data_{$location->id}";
        return Cache::remember($cacheKey, config('weather.cache_duration', 30 * 60), function () use ($location) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $location->lat,
                'longitude' => $location->lon,
                'current_weather' => true,
                'hourly' => 'temperature_2m,weathercode,relativehumidity_2m,pressure_msl',
                'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weathercode,sunrise,sunset,apparent_temperature_max,windspeed_10m_max',
                'timezone' => 'auto',
            ]);

            if (!$response->successful()) {
                Log::error("API-Fehler bei Open-Meteo: Status-Code {$response->status()}, Body: " . $response->body());
                return ['current' => [], 'hourly' => [], 'forecast' => []];
            }

            $data = $response->json();
            $currentHour = Carbon::now()->hour;

            // Aktuelle Daten
            $currentWeather = [
                'date' => Carbon::now()->format('F d'),
                'weekday' => Carbon::now()->format('l'),
                'time' => Carbon::now()->format('h:i A'),
                'temperature' => $data['current_weather']['temperature'],
                'description' => $this->mapWeatherCode($data['current_weather']['weathercode']),
                'icon' => $this->mapWeatherIcon($data['current_weather']['weathercode']),
                'wind_speed' => $data['current_weather']['windspeed'],
                'wind_direction' => $this->getWindDirection($data['current_weather']['winddirection']),
                'humidity' => $data['hourly']['relativehumidity_2m'][$currentHour] ?? null,
                'pressure' => $data['hourly']['pressure_msl'][$currentHour] ?? null,
            ];

            // Stündliche Daten (24 Stunden)
            $hourlyWeather = collect(array_slice($data['hourly']['time'], 0, 24))
                ->map(function ($time, $index) use ($data) {
                    $hour = Carbon::parse($time);
                    return [
                        'day' => $hour->isToday() ? 'today' : 'tomorrow',
                        'hour' => $hour->format('H'),
                        'weather' => $this->mapWeatherIcon($data['hourly']['weathercode'][$index]),
                        'temp' => round($data['hourly']['temperature_2m'][$index] ?? 0),
                        'time' => $hour->format('H:i'),
                    ];
                })->all();

            // 8-Tage-Vorhersage
            $forecast = [];
            foreach ($data['daily']['time'] as $key => $date) {
                $weatherCode = $data['daily']['weathercode'][$key];
                $forecast[] = [
                    'date' => Carbon::parse($date)->format('d.m.Y'),
                    'weekday' => Carbon::parse($date)->format('l'),
                    'temp_max' => $data['daily']['temperature_2m_max'][$key],
                    'temp_min' => $data['daily']['temperature_2m_min'][$key],
                    'precipitation' => $data['daily']['precipitation_sum'][$key],
                    'weather' => $this->mapWeatherCode($weatherCode),
                    'icon' => $this->mapWeatherIcon($weatherCode),
                    'sunrise' => Carbon::parse($data['daily']['sunrise'][$key])->format('H:i'),
                    'sunset' => Carbon::parse($data['daily']['sunset'][$key])->format('H:i'),
                    'real_feel' => $data['daily']['apparent_temperature_max'][$key],
                    'wind_speed' => $data['daily']['windspeed_10m_max'][$key],
                ];
            }

            return [
                'current' => $currentWeather,
                'hourly' => $hourlyWeather,
                'forecast' => $forecast,
            ];
        });
    }


    private function fetchSeasonalClimateYearData(WwdeLocation $location, int $year): array
    {
        $cacheKey = "seasonal_climate_year_{$location->id}_{$year}";
        return Cache::remember($cacheKey, 24 * 60 * 60, function () use ($location, $year) {
            $climates = [];
            $currentDate = now()->startOfYear()->setYear($year); // Startdatum des Jahres 2025
            $maxAllowedDate = Carbon::parse('2025-03-18'); // Maximal erlaubtes Datum gemäß API
            $endDate = $currentDate->copy()->endOfYear();

            while ($currentDate <= $endDate) {
                // Bestimme das Enddatum für diesen Monat, aber nicht später als 2025-03-18
                $monthEnd = $currentDate->copy()->endOfMonth();
                $effectiveEndDate = $monthEnd->lte($maxAllowedDate) ? $monthEnd : $maxAllowedDate;

                if ($currentDate->gt($maxAllowedDate)) {
                    // Für Monate nach März 2025 verwende historischen Fallback
                    $month = $currentDate->month;
                    $climate = WwdeClimate::where('location_id', $location->id)
                        ->where('month_id', $month)
                        ->first();

                    $climates[$month] = [
                        'month_id' => sprintf('%02d', $month),
                        'daily_temperature' => $climate ? $climate->daily_temperature : null,
                        'night_temperature' => $climate ? $climate->night_temperature : null,
                        'water_temperature' => $climate ? $climate->water_temperature : null,
                        'humidity' => $climate ? $climate->humidity : null,
                        'sunshine_per_day' => $climate ? $climate->sunshine_per_day : null,
                        'rainy_days' => $climate ? $climate->rainy_days : null,
                    ];
                } else {
                    // Für Monate bis März 2025 (oder bis 2025-03-18) API aufrufen
                    $response = Http::get('https://api.open-meteo.com/v1/ecmwf', [
                        'latitude' => $location->lat,
                        'longitude' => $location->lon,
                        'start_date' => $currentDate->toDateString(),
                        'end_date' => $effectiveEndDate->toDateString(),
                        'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weathercode',
                        'timezone' => 'auto',
                    ]);

                    if (!$response->successful()) {
                        Log::error("API-Fehler bei Open-Meteo Seasonal Forecast für {$currentDate->toDateString()}: Status-Code {$response->status()}, Body: " . $response->body());
                        $currentDate->addMonth();
                        continue;
                    }

                    $data = $response->json()['daily'];
                    $month = $currentDate->month;

                    if (!empty($data['time'])) {
                        $climates[$month] = [
                            'month_id' => sprintf('%02d', $month),
                            'daily_temperature' => collect($data['temperature_2m_max'])->avg(),
                            'night_temperature' => collect($data['temperature_2m_min'])->avg(),
                            'water_temperature' => null,
                            'humidity' => null, // Nicht in der Seasonal-API verfügbar, könnte über eine andere API ergänzt werden
                            'sunshine_per_day' => null, // Nicht in der Seasonal-API verfügbar
                            'rainy_days' => collect($data['precipitation_sum'])->filter(fn($value) => $value > 0)->count(),
                        ];
                    }
                }

                $currentDate->addMonth();
            }

            return array_values($climates);
        });
    }


    private function fetchClimateYearData(WwdeLocation $location, int $year): array
    {
        $cacheKey = "climate_year_{$location->id}_{$year}";
        return Cache::remember($cacheKey, 24 * 60 * 60, function () use ($location, $year) {
            $climates = [];
            $currentDate = now()->startOfYear()->setYear($year); // Startdatum des Jahres 2025
            $endDate = $currentDate->copy()->endOfYear();

            while ($currentDate <= $endDate) {
                $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $location->lat,
                    'longitude' => $location->lon,
                    'start_date' => $currentDate->toDateString(),
                    'end_date' => $currentDate->copy()->endOfMonth()->toDateString(),
                    'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weathercode,sunshine_duration',
                    'timezone' => 'auto',
                ]);
//dd($response->json());

                if (!$response->successful()) {
                    Log::error("API-Fehler bei Open-Meteo Forecast für {$currentDate->toDateString()}: Status-Code {$response->status()}, Body: " . $response->body());
                    $currentDate->addMonth();
                    continue;
                }

                $data = $response->json()['daily'];
                $month = $currentDate->month;

                if (!empty($data['time'])) {
                    $climates[$month] = [
                        'month_id' => sprintf('%02d', $month),
                        'daily_temperature' => collect($data['temperature_2m_max'])->avg(),
                        'night_temperature' => collect($data['temperature_2m_min'])->avg(),
                        'water_temperature' => null,
                      //  'humidity' => collect($data['relativehumidity_2m'])->avg(),
                        'sunshine_per_day' => collect($data['sunshine_duration'])->avg() / 3600,
                        'rainy_days' => collect($data['precipitation_sum'])->filter(fn($value) => $value > 0)->count(),
                    ];
                }

                $currentDate->addMonth();
            }

            return array_values($climates);
        });
    }



    /**
     * Berechnet die Windrichtung basierend auf Grad.
     *
     * @param float $degrees
     * @return string
     */
    private function getWindDirection($degrees): string
    {
        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
        $index = round($degrees / 45) % 8;
        return $directions[$index];
    }

    /**
     * Holt Vergnügungsparks in der Nähe eines Standorts.
     *
     * @param WwdeLocation $location
     * @return \Illuminate\Support\Collection
     */
    private function fetchAmusementParks(WwdeLocation $location): \Illuminate\Support\Collection
    {
        $cacheKey = "amusement_parks_{$location->id}_" . date('Y-m-d');
        return Cache::remember($cacheKey, config('weather.amusement_parks_cache_duration', 12 * 60 * 60), function () use ($location) {
            $amusementParks = DB::table('amusement_parks')
                ->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$location->lat, $location->lon, $location->lat])
                ->having('distance', '<=', 100)
                ->orderBy('distance', 'asc')
                ->get();

            return $amusementParks->map(function ($park) {
                $openingTimes = $this->fetchApiData("https://api.wartezeiten.app/v1/openingtimes", ['park' => $park->external_id])[0] ?? null;
                $waitingTimes = $this->fetchApiData("https://api.wartezeiten.app/v1/waitingtimes", ['park' => $park->external_id, 'language' => 'de']) ?? [];
                return ['park' => $park, 'opening_times' => $openingTimes, 'waiting_times' => $waitingTimes];
            });
        });
    }

    /**
     * Führt einen API-Aufruf durch und gibt die JSON-Antwort zurück.
     *
     * @param string $url
     * @param array $headers
     * @return array|null
     */
    private function fetchApiData(string $url, array $headers): ?array
    {
        $response = Http::withHeaders(['accept' => 'application/json'] + $headers)->get($url);
        if (!$response->successful()) {
            Log::error("API-Fehler bei {$url}: Status-Code {$response->status()}, Body: " . $response->body());
            return null;
        }
        return $response->json();
    }

    /**
     * Parst die besten Reisezeiten aus einem JSON-String.
     *
     * @param string|null $json
     * @return \Illuminate\Support\Collection
     */
    private function parseBestTravelMonths(?string $json): \Illuminate\Support\Collection
    {
        return collect(json_decode($json, true) ?? [])
            ->filter(fn($month) => is_numeric($month) && $month >= 1 && $month <= 12)
            ->sort()
            ->mapWithKeys(fn($month) => [$month => DateTime::createFromFormat('!m', $month)->format('F')]);
    }

    /**
     * Aktualisiert die Top-Ten-Statistik für einen Standort.
     *
     * @param int $locationId
     * @return void
     */
    protected function updateTopTen(int $locationId): void
    {
        $now = now();
        DB::table('stat_top_ten_locations')->updateOrInsert(
            ['location_id' => $locationId],
            ['search_count' => DB::raw('search_count + 1'), 'updated_at' => $now]
        );
        DB::table('stat_top_ten_locations')->where('updated_at', '<', $now->subWeeks(4))->delete();
    }

    /**
     * Holt verfügbare Aktivitäten für einen Standort.
     *
     * @param WwdeLocation $location
     * @return array
     */
    protected function getActivities(WwdeLocation $location): array
    {
        return collect([
            'Beach' => $location->list_beach,
            'Sports' => $location->list_sports,
            'Island' => $location->list_island,
            'Culture' => $location->list_culture,
            'Nature' => $location->list_nature,
            'Watersport' => $location->list_watersport,
            'Wintersport' => $location->list_wintersport,
            'Mountainsport' => $location->list_mountainsport,
            'Biking' => $location->list_biking,
            'Fishing' => $location->list_fishing,
            'Amusement Park' => $location->list_amusement_park,
            'Water Park' => $location->list_water_park,
            'Animal Park' => $location->list_animal_park,
        ])->filter()->keys()->toArray();
    }

    /**
     * Berechnet den Preis-Trend für ein Land im Vergleich zu einem Referenzland.
     *
     * @param string $countryCode
     * @param string $referenceCountryCode
     * @return array|null
     */
    protected function calculatePriceTrend(string $countryCode, string $referenceCountryCode = 'DE'): ?array
    {
        $cacheKey = "price_trend_{$countryCode}_{$referenceCountryCode}_" . date('Y-m-d');
        return Cache::remember($cacheKey, config('weather.price_trend_cache_duration', 12 * 60 * 60), function () use ($countryCode, $referenceCountryCode) {
            $countryIncome = $this->fetchIncomeData($countryCode);
            $referenceIncome = $this->fetchIncomeData($referenceCountryCode);

            if ($countryIncome && $referenceIncome) {
                $trendFactor = $countryIncome / $referenceIncome;
                return [
                    'factor' => $trendFactor,
                    'category' => $trendFactor < 0.8 ? 'niedrig' : ($trendFactor <= 1.2 ? 'mittel' : 'hoch'),
                ];
            }
            return null;
        });
    }

    /**
     * Holt Einkommensdaten für ein Land von der World Bank API.
     *
     * @param string $countryCode
     * @return float|null
     */
    protected function fetchIncomeData(string $countryCode): ?float
    {
        $cacheKey = "income_data_{$countryCode}_" . date('Y-m-d');
        return Cache::remember($cacheKey, config('weather.income_data_cache_duration', 12 * 60 * 60), function () use ($countryCode) {
            try {
                $response = Http::get("https://api.worldbank.org/v2/country/{$countryCode}/indicator/NY.GDP.PCAP.CD?format=json");
                if (!$response->successful()) {
                    Log::error("API-Fehler bei World Bank für {$countryCode}: Status-Code {$response->status()}, Body: " . $response->body());
                    return null;
                }
                return $response->json()[1][0]['value'] ?? null;
            } catch (\Exception $e) {
                Log::error("Error fetching income data for {$countryCode}: {$e->getMessage()}");
                return null;
            }
        });
    }

    /**
     * Holt Zeitzoneninformationen basierend auf einer IP-Adresse.
     *
     * @param string $ip
     * @return string|null
     */
    protected function getTimezoneFromIp(string $ip): ?string
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return config('app.timezone', 'Europe/Berlin');
        }

        $cacheKey = "timezone_{$ip}_" . date('Y-m-d');
        return Cache::remember($cacheKey, config('weather.timezone_cache_duration', 24 * 60 * 60), function () use ($ip) {
            try {
                $response = Http::get("http://ip-api.com/json/{$ip}");
                if (!$response->successful()) {
                    Log::error("API-Fehler bei ip-api für {$ip}: Status-Code {$response->status()}, Body: " . $response->body());
                    return null;
                }
                return $response->json('timezone');
            } catch (\Exception $e) {
                Log::error("Error fetching timezone for IP: {$ip}: {$e->getMessage()}");
                return null;
            }
        });
    }

    /**
     * Holt Zeitzonen- und Offset-Informationen für einen Standort.
     *
     * @param WwdeLocation $location
     * @return array
     */
    protected function getLocationTimeInfo(WwdeLocation $location): array
    {
        $locationTimezone = $location->time_zone ?? config('app.timezone', 'UTC');
        $userTimezone = $this->getTimezoneFromIp(request()->ip()) ?? config('app.timezone', 'UTC');

        try {
            $userTime = new DateTime('now', new \DateTimeZone($userTimezone));
            $locationTime = new \DateTime('now', new \DateTimeZone($locationTimezone));
            $offsetInHours = round(($locationTime->getOffset() - $userTime->getOffset()) / 3600, 1);

            return [
                'current_time' => $locationTime->format('Y-m-d H:i:s'),
                'offset' => $offsetInHours,
            ];
        } catch (\Exception $e) {
            Log::error("Error calculating timezone for location: {$location->id}: {$e->getMessage()}");
            return ['current_time' => null, 'offset' => null];
        }
    }


    private function mapWeatherIcon(?int $code, bool $isDaytime = true, float $temperature = null): string
    {
        // Spezielle Logik für "extrem heiß" (z. B. > 35°C bei Tag)
        if ($temperature && $temperature > 35 && $isDaytime) {
            return 'sunny-hot'; // Extrem heiß, kombiniert mit Sonnenschutz-Icons
        }

        $map = [
            0 => $isDaytime ? 'sunny' : 'clear-night', // Klarer Himmel (Tag/Nacht)
            1 => $isDaytime ? 'partly-cloudy' : 'partly-cloudy-night', // Leicht bewölkt (Tag/Nacht)
            2 => $isDaytime ? 'partly-cloudy2' : 'partly-cloudy-night', // Mäßig bewölkt (Tag/Nacht)
            3 => 'cloudy', // Bedeckt
            45 => 'foggy', // Nebel
            48 => 'foggy', // Nebel mit Reif
            51 => 'rainy', // Leichter Nieselregen
            53 => 'rainy', // Mäßiger Nieselregen
            55 => 'rainy2', // Starker Nieselregen
            61 => 'rainy', // Leichter Regen
            63 => 'rainy', // Mäßiger Regen
            65 => 'rainy2', // Starker Regen
            80 => 'rainy', // Leichte Regenschauer
            81 => 'rainy', // Mäßige Regenschauer
            82 => 'rainy2', // Starke Regenschauer
            95 => 'thunderstorm', // Gewitter
            96 => 'thunderstorm', // Gewitter mit leichtem Hagel
            99 => 'thunderstorm2', // Gewitter mit starkem Hagel

            // Schnee- und Frost-Zustände (kreative Kombination mit Sommerthemen)
            56 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Leichter gefrierender Nieselregen
            57 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Starker gefrierender Nieselregen
            66 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Leichter gefrierender Regen
            67 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Starker gefrierender Regen
            71 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Leichter Schneefall
            73 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Mäßiger Schneefall
            75 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Starker Schneefall
            77 => $isDaytime ? 'snowy-cloudy' : 'snowy-cloudy', // Schneegriesel
            85 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Leichte Schneeschauer
            86 => $isDaytime ? 'snowy-sunny' : 'snowy-cloudy', // Starke Schneeschauer
        ];
        return $map[$code] ?? 'cloudy'; // Fallback auf 'cloudy' statt 'unknown' für eine schönere Darstellung
    }


    private function mapWeatherCode(?int $code): string
    {
        $map = [
            0 => 'Klarer Himmel',
            1 => 'Leicht bewölkt',
            2 => 'Mäßig bewölkt',
            3 => 'Bedeckt',
            45 => 'Nebel',
            48 => 'Nebel mit Reif',
            51 => 'Leichter Nieselregen',
            53 => 'Mäßiger Nieselregen',
            55 => 'Starker Nieselregen',
            56 => 'Leichter gefrierender Nieselregen',
            57 => 'Starker gefrierender Nieselregen',
            61 => 'Leichter Regen',
            63 => 'Mäßiger Regen',
            65 => 'Starker Regen',
            66 => 'Leichter gefrierender Regen',
            67 => 'Starker gefrierender Regen',
            71 => 'Leichter Schneefall',
            73 => 'Mäßiger Schneefall',
            75 => 'Starker Schneefall',
            77 => 'Schneegriesel',
            80 => 'Leichte Regenschauer',
            81 => 'Mäßige Regenschauer',
            82 => 'Starke Regenschauer',
            85 => 'Leichte Schneeschauer',
            86 => 'Starke Schneeschauer',
            95 => 'Gewitter',
            96 => 'Gewitter mit leichtem Hagel',
            99 => 'Gewitter mit starkem Hagel'
        ];
        return $map[$code] ?? 'Unbekannt';
    }
}
