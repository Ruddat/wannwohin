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

    public function show(string $continentAlias, string $countryAlias, string $locationAlias)
    {
        $location = $this->fetchLocation($continentAlias, $countryAlias, $locationAlias);
        $this->updateTopTen($location->id);

        $weatherData = $this->weatherService->getWeatherDataForLocation($location);
        $forecast = $this->fetchWeatherForecast($location);
        $weatherWidget = $this->fetchWeatherWidgetData($location);
        $galleryImages = $this->fetchGalleryImages($location);
        $parksWithOpeningTimes = $this->fetchAmusementParks($location);
        $timeInfo = $this->getLocationTimeInfo($location);
        $priceTrend = $this->calculatePriceTrend($location->iso2 ?? 'DE');
        $bestTravelMonths = $this->parseBestTravelMonths($location->best_traveltime_json);

        return view('frondend.locationdetails._index', [
            'location' => $location,
            'electric_standard' => $location->electricStandard,
            'climates' => WwdeClimate::where('location_id', $location->id)->orderBy('month_id', 'asc')->get(),
            'averages' => MonthlyClimateSummary::where('location_id', $location->id)->first(),
            'main_image_path' => $location->main_img ? Storage::url($location->main_img) : null,
            'gallery_images' => $galleryImages,
            'parks_with_opening_times' => $parksWithOpeningTimes,
            'panorama_location_picture' => $location->panorama_text_and_style ?? asset('default-bg.jpg'),
            'forecast' => $forecast,
            'pic1_text' => $location->text_pic1 ?? 'Standard Text für Bild 1',
            'pic2_text' => $location->text_pic2 ?? 'Standard Text für Bild 2',
            'pic3_text' => $location->text_pic3 ?? 'Standard Text für Bild 3',
            'head_line' => $location->title ?? 'Standard Headline',
            'panorama_titel' => $location->panorama_title ?? 'Standard Panorama Title',
            'panorama_short_text' => $location->panorama_short_text ?? 'Standard Panorama Short Title',
            'weather_data' => $weatherData,
            'current_time' => $timeInfo['current_time'],
            'time_offset' => $timeInfo['offset'],
            'panorama_text_and_style' => json_decode($location->panorama_text_and_style, true),
            'best_travel_months' => $bestTravelMonths,
            'price_trend' => $priceTrend,
            'hourly_weather' => $weatherWidget['hourly_weather'],
            'weather_data_widget' => $weatherWidget['current_weather'],
        ]);
    }

    private function fetchLocation(string $continentAlias, string $countryAlias, string $locationAlias): WwdeLocation
    {
        return WwdeLocation::where('alias', $locationAlias)
            ->whereHas('country', fn($query) => $query->where('alias', $countryAlias))
            ->whereHas('country.continent', fn($query) => $query->where('alias', $continentAlias))
            ->with('electric')
            ->firstOrFail();
    }

    private function fetchGalleryImages(WwdeLocation $location): array
    {
        return \App\Models\ModLocationGalerie::where('location_id', $location->id)
            ->get()
            ->map(function ($item) {
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
            })->toArray();
    }

    private function fetchWeatherForecast(WwdeLocation $location): array
    {
        return Cache::remember("forecast_{$location->id}", 60 * 60, fn() =>
            (new WeatherDataManagerLibrary())->fetchEightDayForecast($location->lat, $location->lon, $location->id)
        );
    }

    private function fetchWeatherWidgetData(WwdeLocation $location): array
    {
        $cacheKey = "weather_widget_{$location->id}";
        return Cache::remember($cacheKey, 15 * 60, function () use ($location) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $location->lat,
                'longitude' => $location->lon,
                'hourly' => 'temperature_2m,weathercode',
                'daily' => 'sunrise,sunset',
                'timezone' => 'auto',
            ]);

            if (!$response->successful()) {
                return ['current_weather' => [], 'hourly_weather' => []];
            }

            $data = $response->json();
            $sunrise = Carbon::parse($data['daily']['sunrise'][0])->format('H:i');
            $sunset = Carbon::parse($data['daily']['sunset'][0])->format('H:i');
            $currentHour = Carbon::now()->hour;

            $currentWeather = [
                'temperature' => $data['hourly']['temperature_2m'][$currentHour] ?? null,
                'description' => $this->mapWeatherCode($data['hourly']['weathercode'][$currentHour] ?? null),
                'icon' => $this->mapWeatherIcon($data['hourly']['weathercode'][$currentHour] ?? null),
                'sunrise' => $sunrise,
                'sunset' => $sunset,
            ];

            $hourlyWeather = collect(array_slice($data['hourly']['time'], 0, 24))
                ->map(function ($time, $index) use ($data, $sunrise, $sunset) {
                    $hour = Carbon::parse($time)->format('H');
                    $isDaytime = $hour >= Carbon::parse($sunrise)->format('H') && $hour < Carbon::parse($sunset)->format('H');
                    return [
                        'day' => Carbon::parse($time)->isToday() ? 'tod' : 'tom',
                        'hour' => $hour,
                        'weather' => $this->mapWeatherIcon($data['hourly']['weathercode'][$index] ?? null, $isDaytime),
                        'temp' => round($data['hourly']['temperature_2m'][$index] ?? 0),
                        'time' => Carbon::parse($time)->format('H:i'),
                    ];
                })->all();

            return ['current_weather' => $currentWeather, 'hourly_weather' => $hourlyWeather];
        });
    }

    private function fetchAmusementParks(WwdeLocation $location): \Illuminate\Support\Collection
    {
        $cacheKey = "amusement_parks_{$location->id}";
        return Cache::remember($cacheKey, 24 * 60 * 60, function () use ($location) {
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

    private function fetchApiData(string $url, array $headers): ?array
    {
        $response = Http::withHeaders(['accept' => 'application/json'] + $headers)->get($url);
        return $response->successful() ? $response->json() : null;
    }

    private function parseBestTravelMonths(?string $json): \Illuminate\Support\Collection
    {
        return collect(json_decode($json, true) ?? [])
            ->filter(fn($month) => is_numeric($month) && $month >= 1 && $month <= 12)
            ->sort()
            ->mapWithKeys(fn($month) => [$month => DateTime::createFromFormat('!m', $month)->format('F')]);
    }

    protected function updateTopTen(int $locationId): void
    {
        $now = now();
        DB::table('stat_top_ten_locations')->updateOrInsert(
            ['location_id' => $locationId],
            ['search_count' => DB::raw('search_count + 1'), 'updated_at' => $now]
        );
        DB::table('stat_top_ten_locations')->where('updated_at', '<', $now->subWeeks(4))->delete();
    }

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

    protected function calculatePriceTrend(string $countryCode, string $referenceCountryCode = 'DE'): ?array
    {
        $cacheKey = "price_trend_{$countryCode}_{$referenceCountryCode}";
        return Cache::remember($cacheKey, 24 * 60 * 60, function () use ($countryCode, $referenceCountryCode) {
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

    protected function fetchIncomeData(string $countryCode): ?float
    {
        $cacheKey = "income_data_{$countryCode}";
        return Cache::remember($cacheKey, 24 * 60 * 60, function () use ($countryCode) {
            try {
                $response = Http::get("https://api.worldbank.org/v2/country/{$countryCode}/indicator/NY.GDP.PCAP.CD?format=json");
                return $response->successful() ? ($response->json()[1][0]['value'] ?? null) : null;
            } catch (\Exception $e) {
                Log::error("Error fetching income data for {$countryCode}: {$e->getMessage()}");
                return null;
            }
        });
    }

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

    protected function getTimezoneFromIp(string $ip): ?string
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return config('app.timezone', 'Europe/Berlin');
        }

        $cacheKey = "timezone_{$ip}";
        return Cache::remember($cacheKey, 24 * 60 * 60, function () use ($ip) {
            try {
                $response = Http::get("http://ip-api.com/json/{$ip}");
                return $response->successful() ? $response->json('timezone') : null;
            } catch (\Exception $e) {
                Log::error("Error fetching timezone for IP: {$ip}: {$e->getMessage()}");
                return null;
            }
        });
    }

    private function mapWeatherIcon(?int $code, bool $isDaytime = true): string
    {
        $map = [
            0 => $isDaytime ? 'sunny' : 'clear-night',
            1 => $isDaytime ? 'partly-cloudy' : 'partly-cloudy-night',
            2 => $isDaytime ? 'partly-cloudy' : 'partly-cloudy-night',
            3 => 'cloudy',
            45 => 'foggy',
            48 => 'foggy',
            51 => 'rainy',
            53 => 'rainy',
            55 => 'rainy',
            56 => 'snowy',
            57 => 'snowy',
            61 => 'rainy',
            63 => 'rainy',
            65 => 'rainy',
            66 => 'snowy',
            67 => 'snowy',
            71 => 'snowy',
            73 => 'snowy',
            75 => 'snowy',
            77 => 'snowy',
            80 => 'rainy',
            81 => 'rainy',
            82 => 'rainy',
            85 => 'snowy',
            86 => 'snowy',
            95 => 'thunderstorm',
            96 => 'thunderstorm',
            99 => 'thunderstorm'
        ];
        return $map[$code] ?? 'unknown';
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
