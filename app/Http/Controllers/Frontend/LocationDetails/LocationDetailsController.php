<?php

namespace App\Http\Controllers\Frontend\LocationDetails;

use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use App\Helpers\WeatherHelper;
use App\Services\WeatherService;
use App\Models\ModLocationGalerie;
use App\Models\WwdeLocationImages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\MonthlyClimateSummary;
use Illuminate\Support\Facades\Cache;
use App\Services\LocationImageService;
use Illuminate\Support\Facades\Storage;

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
        // Location abrufen
        $location = WwdeLocation::where('alias', $locationAlias)
            ->whereHas('country', fn($query) => $query->where('alias', $countryAlias))
            ->whereHas('country.continent', fn($query) => $query->where('alias', $continentAlias))
            ->with('electric') // Electric-Relation laden
            ->firstOrFail();

        // Wetterdaten abrufen
        $weatherData = $this->weatherService->getWeatherDataForLocation($location);

        // Eintrag in die Top-Ten-Liste aktualisieren oder erstellen
        $this->updateTopTen($location->id);

        // Galerie-Bilder abrufen
        $activities = $this->getActivities($location);
        //dd($activities);

        // hier werden bilder uber die api automatisch geholt
        // $galleryImages = $this->imageService->getGalleryByActivities($location->id, $location->title, $activities);

        // hier nur aus der datenbank
        $galleryImages = ModLocationGalerie::where('location_id', $location->id)
        ->get()
        ->map(function ($item) {
            return [
                'url' => $item->image_path ? asset('storage/' . $item->image_path) : null,
                'description' => $item->description ?? 'Keine Beschreibung verfügbar',
                'activity' => $item->activity ?? 'Allgemein',
                'image_caption' => $item->image_caption ?? 'Kein Titel verfügbar',
            ];
        })
        ->toArray();

        // Bilder überprüfen und ggf. den Storage-Pfad anpassen
        foreach ($galleryImages as &$image) {
            $imagePath = parse_url($image['url'], PHP_URL_PATH); // Extrahiere den Pfad aus der URL
            $relativePath = ltrim($imagePath, '/'); // Entferne führenden Slash

            // Cache verwenden, um unnötige Prüfungen zu vermeiden
            $cacheKey = 'file_exists_' . md5($relativePath);

            $fileExists = Cache::remember($cacheKey, now()->addHours(1), function () use ($relativePath) {
                return Storage::exists($relativePath); // Prüfe, ob die Datei existiert
            });

            if (!$fileExists) {
                // Wenn der Pfad '/storage/img' enthält, entferne '/storage'
                if (strpos($image['url'], '/storage/img') !== false) {
                    $image['url'] = str_replace('/storage', '', $image['url']);
                }
            }
        }
        // Überprüfte Bilder
     //   dd($galleryImages);

        // Freizeitparks im Umkreis abrufen
        $parksWithOpeningTimes = $this->getAmusementParksWithOpeningTimes($location);

        // Stromnetz-Daten abrufen
        $electricStandard = $location->electricStandard;

        // Klimadaten und Durchschnittswerte abrufen
        $climates = WwdeClimate::where('location_id', $location->id)
            ->orderBy('month_id', 'asc')
            ->get();
        $averages = MonthlyClimateSummary::where('location_id', $location->id)->first();

        // Haupt- und Panorama-Bilder
        $mainImagePath = $location->main_img ? Storage::url($location->main_img) : null;
        $panoramaImagePath = $location->panorama_text_and_style ?? asset('default-bg.jpg');

        // Zeitinformationen der Location abrufen
        $timeInfo = $this->getLocationTimeInfo($location);

        $panoramaData = json_decode($location->panorama_text_and_style, true);

        // Texte für die Bilder
        $pic1Text = $location->text_pic1 ?? 'Standard Text für Bild 1';
        $pic2Text = $location->text_pic2 ?? 'Standard Text für Bild 2';
        $pic3Text = $location->text_pic3 ?? 'Standard Text für Bild 3';
        $headLine = $location->title ?? 'Standard Headline';

        // Beispiel-Ländercode (ISO Alpha-2)
//dd($location);

$countryCode = $location->iso2;

// Überprüfen, ob der countryCode ungültig ist
if (empty($countryCode) || $countryCode === '0') {
    Log::warning("Country code missing for location ID: {$location->id}. Using fallback.");
    $countryCode = 'DE'; // Fallback auf Deutschland
}

$priceTrend = $this->calculatePriceTrend($countryCode);

//dd($priceTrend);

// Deutsche zu Englische Monatsnamen mappen
$germanToEnglishMonths = [
    "Januar" => "January",
    "Februar" => "February",
    "März" => "March",
    "April" => "April",
    "Mai" => "May",
    "Juni" => "June",
    "Juli" => "July",
    "August" => "August",
    "September" => "September",
    "Oktober" => "October",
    "November" => "November",
    "Dezember" => "December",
];

// Beste Reisezeit aus JSON extrahieren und in Monatsindizes umwandeln
$bestTravelMonths = collect(json_decode($location->best_traveltime_json, true))
    ->mapWithKeys(function ($month) use ($germanToEnglishMonths) {
        $englishMonth = $germanToEnglishMonths[$month] ?? $month;
        $index = date('n', strtotime($englishMonth)); // Index (1–12)
        return [$index => $englishMonth];
    })
    ->sortKeys(); // Sortiert nach Monatsindex (1–12)


//dd($bestTravelMonths);

        //dd($location);




        return view('frondend.locationdetails._index', [
            'location' => $location,
            'electric_standard' => $electricStandard, // Stromnetz-Daten hinzufügen
            'climates' => $climates,
            'averages' => $averages,
            'main_image_path' => $mainImagePath,
            'gallery_images' => $galleryImages,
            'parks_with_opening_times' => $parksWithOpeningTimes,
            'panorama_location_picture' => $panoramaImagePath,
            'pic1_text' => $pic1Text,
            'pic2_text' => $pic2Text,
            'pic3_text' => $pic3Text,
            'head_line' => $headLine,
            'weather_data' => $weatherData,
            'current_time' => $timeInfo['current_time'],
            'time_offset' => $timeInfo['offset'],
            'panorama_text_and_style' => $panoramaData,
            'best_travel_months' => $bestTravelMonths, // Hinzugefügt
            'price_trend' => $priceTrend, // Preistendenz hinzufügen

        ]);
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
           // 'City Travel' => $location->list_citytravel,
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

    protected function getAmusementParksWithOpeningTimes(WwdeLocation $location)
    {
        $latitude = $location->lat;
        $longitude = $location->lon;

        $amusementParks = DB::table('amusement_parks')
            ->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', 100)
            ->orderBy('distance', 'asc')
            ->get();

        return $amusementParks->map(function ($park) {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'park' => $park->external_id,
            ])->get('https://api.wartezeiten.app/v1/openingtimes');

            $openingTimes = $response->successful() ? $response->json()[0] ?? null : null;

            $waitingTimesResponse = Http::withHeaders([
                'accept' => 'application/json',
                'language' => 'de',
                'park' => $park->external_id,
            ])->get('https://api.wartezeiten.app/v1/waitingtimes');

            $waitingTimes = $waitingTimesResponse->successful() ? $waitingTimesResponse->json() : [];

            return [
                'park' => $park,
                'opening_times' => $openingTimes,
                'waiting_times' => $waitingTimes,
            ];
        });
    }

    protected function calculatePriceTrend(string $countryCode, string $referenceCountryCode = 'DE'): ?array
    {
        try {
            // Einkommen für das Land und das Referenzland abrufen
            $countryIncome = $this->fetchIncomeData($countryCode);
            $referenceIncome = $this->fetchIncomeData($referenceCountryCode);

            if ($countryIncome && $referenceIncome) {
                $trendFactor = $countryIncome / $referenceIncome;
                $trendCategory = $trendFactor < 0.8 ? 'niedrig' : ($trendFactor <= 1.2 ? 'mittel' : 'hoch');
                return [
                    'factor' => $trendFactor,
                    'category' => $trendCategory,
                ];
            }
        } catch (\Exception $e) {
            Log::error("Error calculating price trend: {$e->getMessage()}");
        }

        return null;
    }

    protected function fetchIncomeData(string $countryCode): ?float
    {
        $url = "https://api.worldbank.org/v2/country/{$countryCode}/indicator/NY.GDP.PCAP.CD?format=json";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                return $data[1][0]['value'] ?? null; // Durchschnittliches Einkommen
            }
        } catch (\Exception $e) {
            Log::error("Error fetching income data for {$countryCode}: {$e->getMessage()}");
        }

        return null;
    }


    protected function getLocationTimeInfo(WwdeLocation $location)
    {
        // Standard-Zeitzone der Location (z. B. "Europe/Berlin")
        $locationTimezone = $location->time_zone ?? config('app.timezone', 'UTC');

        try {
            // Zeitzone des Nutzers basierend auf der IP
            $userIp = request()->ip();
            $userTimezone = $this->getTimezoneFromIp($userIp) ?? config('app.timezone', 'UTC');

            // Zeitobjekte für Nutzer- und Location-Zeitzonen
            $userTime = new \DateTime('now', new \DateTimeZone($userTimezone));
            $locationTime = new \DateTime('now', new \DateTimeZone($locationTimezone));

            // Zeitverschiebung berechnen (in Stunden)
            $offsetInSeconds = $locationTime->getOffset() - $userTime->getOffset();
            $offsetInHours = round($offsetInSeconds / 3600, 1); // Runde auf eine Nachkommastelle

            return [
                'current_time' => $locationTime->format('Y-m-d H:i:s'),
                'offset' => $offsetInHours,
            ];
        } catch (\Exception $e) {
            Log::error("Error calculating timezone for location: {$location->id}. Error: {$e->getMessage()}");
            return [
                'current_time' => null,
                'offset' => null,
            ];
        }
    }

    protected function getTimezoneFromIp(string $ip): ?string
    {
        if ($ip === '127.0.0.1' || $ip === '::1') { // Lokale IPs (IPv4 und IPv6)
            return config('app.timezone', 'Europe/Berlin'); // Standard-Zeitzone für lokale Tests
        }

        try {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            if ($response->successful() && $response->json('timezone')) {
                return $response->json('timezone');
            }
        } catch (\Exception $e) {
            Log::error("Error fetching timezone for IP: {$ip}. Error: {$e->getMessage()}");
        }

        return null;
    }




}


