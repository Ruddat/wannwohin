<?php

namespace App\Helpers;

use App\Models\HeaderContent;
use Illuminate\Support\Facades\Storage;

class HeaderHelper
{
    public static function getHeaderContent()
    {
        // HeaderContent abrufen
        $headerContent = HeaderContent::inRandomOrder()->first();

        // Falls kein HeaderContent vorhanden ist, gib eine leere Standardstruktur zurück
        if (!$headerContent) {
            return [
                'bgImgPath' => null,
                'mainImgPath' => null,
                'title' => '',
                'title_text' => '',
                'main_text' => '',
            ];
        }

        return [
            'bgImgPath' => self::getImagePath($headerContent->bg_img),
            'mainImgPath' => self::getImagePath($headerContent->main_img),
            'title' => $headerContent->title ?? '',
            'title_text' => $headerContent->main_text ?? '',
            'main_text' => $headerContent->content ?? '',
        ];
    }

    private static function getImagePath($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        if (Storage::exists($imagePath)) {
            return Storage::url($imagePath);
        }

        if (file_exists(public_path($imagePath))) {
            return asset($imagePath);
        }

        return null;
    }
}
