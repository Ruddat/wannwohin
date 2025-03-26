<?php

namespace App\Helpers;

use App\Models\HeaderContent;
use Illuminate\Support\Facades\Storage;

class HeaderHelper
{
    public static function getHeaderContent(string $context = null): array
    {
        // Standardstruktur für Fallback
        $default = [
            'bgImgPath' => asset('img/headers/default-header.jpg'),
            'mainImgPath' => null,
            'title' => 'Dein Reiseabenteuer beginnt hier',
            'title_text' => '',
            'main_text' => '',
        ];

        // Wenn ein Kontext gegeben ist, versuche spezifischen Content zu finden
        if ($context) {
            $headerContent = HeaderContent::where('slug', $context)->first();
            if ($headerContent) {
                return [
                    'bgImgPath' => self::getImagePath($headerContent->bg_img),
                    'mainImgPath' => self::getImagePath($headerContent->main_img),
                    'title' => $headerContent->title ?? '',
                    'title_text' => $headerContent->main_text ?? '',
                    'main_text' => $headerContent->content ?? '',
                ];
            }
        }

        // Fallback: Zufälliger Eintrag
        $headerContent = HeaderContent::inRandomOrder()->first();
        if ($headerContent) {
            return [
                'bgImgPath' => self::getImagePath($headerContent->bg_img),
                'mainImgPath' => self::getImagePath($headerContent->main_img),
                'title' => $headerContent->title ?? '',
                'title_text' => $headerContent->main_text ?? '',
                'main_text' => $headerContent->content ?? '',
            ];
        }

        // Letzter Fallback: Standardwerte
        return $default;
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
