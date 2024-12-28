<?php

namespace App\Http\Controllers\Frontend\LocationDetails;

use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\LocationImageService;

class LocationDetailsController extends Controller
{
    protected $imageService;

    public function __construct(LocationImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function show(string $continentAlias, string $countryAlias, string $locationAlias)
    {
        // Location abrufen
        $location = WwdeLocation::where('alias', $locationAlias)
            ->whereHas('country', fn($query) => $query->where('alias', $countryAlias))
            ->whereHas('country.continent', fn($query) => $query->where('alias', $continentAlias))
            ->firstOrFail();

        // Galerie-Bilder abrufen
        $activities = collect([
            'Beach' => $location->list_beach,
            'City Travel' => $location->list_citytravel,
            // Weitere Aktivitäten...
        ])->filter(fn($value) => $value)->keys()->toArray();

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


//dd($galleryImages);

        return view('frondend.locationdetails._index', [
            'location' => $location,
            'main_image_path' => $mainImagePath,
            'gallery_images' => $galleryImages,
            'parks_with_opening_times' => $parksWithOpeningTimes,
        ]);
    }
}
