<?php

namespace App\Repositories;

use App\Models\HeaderContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ContinentRepository
{
    /**
     * Get images for a continent, using fallback images if none are defined.
     *
     * @param $continent
     * @return array
     */
    public function getContinentImages($continent)
    {
        $bgImgPath = $continent->image1_path ? Storage::url($continent->image1_path) : null;
        $mainImgPath = $continent->image2_path ? Storage::url($continent->image2_path) : null;
//dd($mainImgPath, $bgImgPath);

   // Bildpfade validieren
   $bgImgPath = $continent->image1_path ? (Storage::exists($continent->image1_path) ? Storage::url($continent->image1_path) : (file_exists(public_path($continent->image1_path)) ? asset($continent->image1_path) : null)) : null;
  // $mainImgPath = $headerContent->main_img ? (Storage::exists($headerContent->main_img) ? Storage::url($headerContent->main_img) : (file_exists(public_path($headerContent->main_img)) ? asset($headerContent->main_img) : null)) : null;
  //dd(Storage::exists($continent->image1_path));


 // dd($continent->image1_path, $bgImgPath);
      //  dd($bgImgPath);

        // Fallback: Use HeaderContent if no images are defined
        if (!$bgImgPath || !$mainImgPath) {
            $headerContent = Cache::remember('header_content_random', 5 * 60, function () {
                return HeaderContent::inRandomOrder()->first();
            });

            // Bildpfade validieren
            $bgImgPath = $headerContent->bg_img ? (Storage::exists($headerContent->bg_img) ? Storage::url($headerContent->bg_img) : (file_exists(public_path($headerContent->bg_img)) ? asset($headerContent->bg_img) : null)) : null;
            $mainImgPath = $headerContent->main_img ? (Storage::exists($headerContent->main_img) ? Storage::url($headerContent->main_img) : (file_exists(public_path($headerContent->main_img)) ? asset($headerContent->main_img) : null)) : null;

        }

        //dd($mainImgPath, $bgImgPath);

        return [
            'bgImgPath' => $bgImgPath,
            'mainImgPath' => $mainImgPath,
        ];
    }

    public function getAndStoreContinentImages($continent)
    {

        $images = $this->getContinentImages($continent);

        // Speichere die Bilder in der Session
        session()->put('headerData', [
            'bgImgPath' => $images['bgImgPath'],
            'mainImgPath' => $images['mainImgPath'],
            'headerContent' => [
                'main_text' => $continent->continent_text ?? 'Standardtext',
            ],
        ]);

        return $images;
    }

}
