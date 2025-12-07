<?php

namespace App\Helpers;

use App\Models\HeaderContent;
use Illuminate\Support\Facades\Storage;

class HeaderHelper
{
    public static function getHeaderContent(string $context = null): array
    {
        $default = [
            'bgImgPath'   => asset('img/headers/default-header.jpg'),
            'mainImgPath' => null,
            'title'       => 'Dein Reiseabenteuer beginnt hier',
            'title_text'  => '',
            'main_text'   => '',
        ];

        // 1) Spezifischer Header anhand slug
        if ($context) {
            $headerContent = HeaderContent::where('slug', $context)->first();
            if ($headerContent) {
                return self::mapModelToArray($headerContent);
            }
        }

        // 2) Fallback: random header
        $headerContent = HeaderContent::inRandomOrder()->first();
        if ($headerContent) {
            return self::mapModelToArray($headerContent);
        }

        // 3) Letzter Fallback
        return $default;
    }


    private static function mapModelToArray($model): array
    {
        return [
            'bgImgPath'   => self::resolveImagePath($model->bg_img),
            'mainImgPath' => self::resolveImagePath($model->main_img),
            'title'       => $model->title ?? '',
            'title_text'  => $model->main_text ?? '',
            'main_text'   => $model->content ?? '',
        ];
    }


    /**
     * 🔥 Die zentrale Funktion – robust, fehlerfrei, erkennt ALLES
     */
public static function resolveImagePath(?string $path): ?string
{
    if (!$path) {
        return null;
    }

    // Bereits vollständige URL
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    // /uploads/xyz.webp
    if (str_starts_with($path, '/uploads/')) {
        return asset('storage' . $path);
    }

    // uploads/xyz.webp
    if (str_starts_with($path, 'uploads/')) {
        return asset('storage/' . $path);
    }

    // img/... im public-folder
    if (str_starts_with($path, 'img/')) {
        return asset($path);
    }

    // Reiner Dateiname
    if (!str_contains($path, '/')) {
        return asset('storage/uploads/' . $path);
    }

    // Speicherpfad (storage/…)
    if (str_starts_with($path, 'storage/')) {
        return asset($path);
    }

    // Fallback
    return asset($path);
}


public static function buildExploreSlug(?string $activity, ?string $time): string
{
    // Startseite /explore → fester Slug
    if (!$activity) {
        return 'explore';
    }

    // Trips-Seite
    if ($activity === 'trips') {
        return 'explore-trips';
    }

    // Normale Explore-Seiten
    $validActivities = ['relax', 'adventure', 'culture', 'amusement'];
    $validTimes = ['now', 'month', 'later'];

    $activity = in_array($activity, $validActivities) ? $activity : 'relax';
    $time = in_array($time, $validTimes) ? $time : 'now';

    return "explore-{$activity}-{$time}";
}

public static function buildHeaderBlock(string $slug): object
{
    $data = self::getHeaderContent($slug);

    return (object)[
        'bg_img'    => $data['bgImgPath']   ?? null,
        'main_img'  => $data['mainImgPath'] ?? null,
        'title'     => $data['title']       ?? null,
        'main_text' => $data['title_text']  ?? null,
    ];
}

}