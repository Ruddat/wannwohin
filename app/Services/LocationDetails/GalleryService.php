<?php

namespace App\Services\LocationDetails;

use App\Models\WwdeLocation;
use App\Models\ModLocationGalerie;
use Illuminate\Support\Facades\Cache;


class GalleryService
{
    public function get(WwdeLocation $location)
    {
        $key = "gallery_{$location->id}";
        return Cache::remember($key, 60 * 60 * 24, function () use ($location) {
            return ModLocationGalerie::where('location_id', $location->id)
                ->select(['image_path','image_caption','description','activity'])
                ->get()
                ->map(fn($i) => [
                    'url'         => asset('storage/'.$i->image_path),
                    'caption'     => $i->image_caption,
                    'description' => $i->description,
                    'activity'    => $i->activity
                ]);
        });
    }
}
