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
use App\Helpers\WeatherHelper;

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
        $galleryImages = $this->imageService->getGalleryByActivities($location->id, $location->title, $activities);

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
        $headLine = $location->text_headline ?? 'Standard Headline';



        // Beste Reisezeit aus JSON extrahieren und in Monatsindizes umwandeln
        $bestTravelMonths = collect(json_decode($location->best_traveltime_json, true))
        ->mapWithKeys(function ($month) {
            $index = date('n', strtotime($month)); // Index (1–12)
            return [$index => $month];
        });


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
            ->having('distance', '<=', 300)
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



    protected function getLocationTimeInfo(WwdeLocation $location)
    {
        // Standard-Zeitzone der Location (z. B. "Europe/Berlin")
        $locationTimezone = $location->time_zone ?? 'UTC';

        try {
            // Aktuelle Zeit in der Server-Zeitzone
            $serverTimezone = new \DateTimeZone(config('app.timezone', 'UTC'));
            $currentTime = new \DateTime('now', $serverTimezone);

            // Zeit in der Zeitzone der Location
            $locationTimeZone = new \DateTimeZone($locationTimezone);
            $locationTime = $currentTime->setTimezone($locationTimeZone);

            // Zeitverschiebung berechnen (in Stunden)
            $offsetInSeconds = $locationTimeZone->getOffset($currentTime);
            $offsetInHours = $offsetInSeconds / 3600;

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



}


