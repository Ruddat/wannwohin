<?php

namespace App\Http\Controllers\Frontend\LocationDetails;

use App\Http\Controllers\Controller;
use App\Services\LocationPageService;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class LocationDetailsController extends Controller
{
public function show($continent, $country, $location, LocationPageService $service)
{
    $slug = strtolower("$continent/$country/$location");

    // Hauptdaten aus Service holen
    $data = $service->get($slug);

    $locationModel = $data['location'];

    $this->trackLocationView($locationModel->id);

    return view('frondend.locationdetails._index', array_merge($data, [

        // HEADER-BILDER
        'mainLocationPicture' => $locationModel->main_img
            ? Storage::url($locationModel->main_img)
            : null,

        'panoramaLocationPicture' => $locationModel->panorama_img
            ? Storage::url($locationModel->panorama_img)
            : null,

        // ALTE TEXT-FELDER (abwärtskompatibel)
        'pic1_text' => $locationModel->text_pic1 ?? null,
        'pic2_text' => $locationModel->text_pic2 ?? null,
        'pic3_text' => $locationModel->text_pic3 ?? null,

        // HEADLINE
        'head_line' => $locationModel->title ?? null,

        // PANORAMA TEXTE
        'panorama_titel' => $locationModel->panorama_title ?? null,
        'panorama_short_text' => $locationModel->panorama_short_text ?? null,

        // GALLERY BILDER
        'gallery_images' => $data['gallery_images'] ?? [],
    ]));
}

private function trackLocationView(int $locationId): void
{
    $month = now()->format('Y-m');

    // Session-Key verhindert Mehrfachzählung
    $sessionKey = "viewed_location_{$locationId}_{$month}";

    if (session()->has($sessionKey)) {
        return;
    }

    DB::table('stat_location_search_histories')->updateOrInsert(
        [
            'location_id' => $locationId,
            'month'       => $month,
        ],
        [
            'search_count' => DB::raw('search_count + 1'),
            'updated_at'   => now(),
        ]
    );

    session()->put($sessionKey, true);
}



}
