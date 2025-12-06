<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class PathSanitizer
{
    /**
     * Wandelt Locationnamen sauber in Slugs um
     * Beispiel:
     * "Bakı İnzibati Ərazisi" -> "baki-inzibati-erazisi"
     */
    public static function locationSlug(string $name): string
    {
        return Str::slug(self::ascii($name));
    }

    /**
     * Entfernt alle nicht-ASCII-Zeichen
     */
    public static function ascii(string $text): string
    {
        return preg_replace('/[^\x20-\x7E]/', '', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text));
    }

    /**
     * Säubert Dateinamen
     */
    public static function filename(string $filename): string
    {
        $filename = self::ascii($filename);
        $filename = Str::slug(pathinfo($filename, PATHINFO_FILENAME));
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return $filename . ($ext ? ".{$ext}" : '');
    }

    /**
     * Baut einen sauberen relativen Pfad zum Speichern
     */
    public static function imagePath(string $locationSlug, string $fileName): string
    {
        return "uploads/images/locations/{$locationSlug}/{$fileName}";
    }
}
