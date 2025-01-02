<?php

namespace App\Http\Controllers\Frontend\LocationDetails;

use App\Models\WwdeClimate;
use App\Models\WwdeLocation;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\MonthlyClimateSummary;
use App\Services\LocationImageService;
use Illuminate\Support\Facades\Storage;

class LocationDetailsController extends Controller
{
    protected $imageService;

    public function __construct(LocationImageService $imageService, WeatherService $weatherService)
    {
        $this->imageService = $imageService;
        $this->weatherService = $weatherService; // Wetterservice injizieren
    }

    public function show(string $continentAlias, string $countryAlias, string $locationAlias)
    {
        // Location abrufen
        $location = WwdeLocation::where('alias', $locationAlias)
            ->whereHas('country', fn($query) => $query->where('alias', $countryAlias))
            ->whereHas('country.continent', fn($query) => $query->where('alias', $continentAlias))
            ->firstOrFail();

            // Wetterdaten prüfen und aktualisieren
            $weatherData = $this->getWeatherDataForLocation($location);

             //$weatherData = $this->WeatherDataManagerLibrary($location);


        //dd($weatherData);

        // Eintrag in die Top-Ten-Liste aktualisieren oder erstellen
        $this->updateTopTen($location->id);

        // Galerie-Bilder abrufen
        $activities = collect([
            'Beach' => $location->list_beach,
            'City Travel' => $location->list_citytravel,
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
            // Weitere Aktivitäten...
        ])->filter(fn($value) => $value)->keys()->toArray();

//dd($activities);


        $galleryImages = $this->imageService->getGalleryByActivities($location->id, $location->title, $activities);
        $mainImagePath = $location->main_img ? Storage::url($location->main_img) : null;

        // Freizeitparks im Umkreis abrufen
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
        ->having('distance', '<=', 300)
        ->orderBy('distance', 'asc')
        ->get();

    $parksWithOpeningTimes = $amusementParks->map(function ($park) {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'park' => $park->external_id,
        ])->get('https://api.wartezeiten.app/v1/openingtimes');

        $openingTimes = $response->successful() ? $response->json()[0] ?? null : null;

        // Wartezeiten abrufen
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


    // Klimadaten abrufen
    $climates = WwdeClimate::where('location_id', $location->id)
        ->orderBy('month_id', 'asc')
        ->get();

    // Durchschnittswerte berechnen
    $averages = MonthlyClimateSummary::where('location_id', $location->id)->first();

    // Haupt- und Panorama-Bilder
    $mainImagePath = $location->main_img ? Storage::url($location->main_img) : null;
    $panoramaImagePath = $location->panorama_text_and_style ?? asset('default-bg.jpg');

    // Texte für die Bilder
    $pic1Text = $location->text_pic1 ?? 'Standard Text für Bild 1';
    $pic2Text = $location->text_pic2 ?? 'Standard Text für Bild 2';
    $pic3Text = $location->text_pic3 ?? 'Standard Text für Bild 3';
    $headLine = $location->text_headline ?? 'Standard Headline';

//dd($headLine);

        return view('frondend.locationdetails._index', [
            'location' => $location,
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
        ]);
    }


/**
 * Aktualisiert die Top-Ten-Liste.
 */
protected function updateTopTen(int $locationId): void
{
    $now = now();

    // Eintrag aktualisieren oder neu erstellen
    DB::table('stat_top_ten_locations')->updateOrInsert(
        ['location_id' => $locationId],
        ['search_count' => DB::raw('search_count + 1'), 'updated_at' => $now]
    );

    // Einträge entfernen, die älter als 4 Wochen sind
    DB::table('stat_top_ten_locations')->where('updated_at', '<', $now->subWeeks(4))->delete();
}


/**
 * Holt Wetterdaten für eine Location und aktualisiert die Datenbank bei Bedarf.
 */
public function getWeatherDataForLocation(WwdeLocation $location)
{
    // Prüfe, ob die Daten in der Datenbank aktuell sind
    if ($location->weather_updated_at && $location->weather_updated_at->diffInHours(now()) < 1) {
        return [
            'temperature' => $location->current_temp_from_api,
            'description' => $location->current_weather_from_api,
            'icon' => $location->weather_icon,
            'humidity' => $location->humidity,
            'cloudiness' => $location->cloudiness,
            'wind_speed' => $location->wind_speed,
            'wind_direction' => $location->wind_direction,
        ];
    }

    // API-Aufruf für Wetterdaten
    $weatherData = $this->weatherService->getWeatherDataForLocation($location);

    if ($weatherData) {
        Log::info('Wetterdaten vor Update', $weatherData);

        // Speichere die Wetterdaten in der wwde_climates Tabelle
        $climateData = [
            'location_id' => $location->id,
            'month_id' => now()->month,
            'month' => now()->format('F'),
            'daily_temperature' => $weatherData['daily_temperature'] ?? null,
            'night_temperature' => $weatherData['night_temperature'] ?? null,
            'sunshine_per_day' => $weatherData['sunshine_per_day'] ?? null,
            'humidity' => $weatherData['humidity'] ?? null,
            'rainy_days' => $weatherData['rainy_days'] ?? null,
            'water_temperature' => $weatherData['water_temperature'] ?? null,
            'icon' => isset($weatherData['icon'])
                ? "https://openweathermap.org/img/wn/{$weatherData['icon']}@2x.png"
                : 'https://openweathermap.org/img/wn/01d@2x.png',
        ];

        WwdeClimate::updateOrCreate(
            ['location_id' => $location->id, 'month_id' => now()->month],
            $climateData
        );

        // Gib die aktualisierten Wetterdaten zurück
        return [
            'temperature' => $weatherData['daily_temperature'] ?? null,
            'night_temperature' => $weatherData['night_temperature'] ?? null,
            'description' => $weatherData['weather'] ?? null,
            'icon' => $climateData['icon'],
            'humidity' => $weatherData['humidity'] ?? null,
            'rainy_days' => $weatherData['rainy_days'] ?? null,
        ];
    }

    Log::error('Fehler beim Abrufen der Wetterdaten');
    return null;
}




}


