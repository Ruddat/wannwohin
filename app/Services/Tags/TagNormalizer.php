<?php

namespace App\Services\Tags;

use Illuminate\Support\Str;

class TagNormalizer
{
    public function normalize(string $value): string
    {
        $value = strtolower(trim($value));

        // & und "und" vereinheitlichen
        $value = str_replace(['&amp;', '&'], ' und ', $value);

        // Mehrfach-Leerzeichen entfernen
        $value = preg_replace('/\s+/', ' ', $value);

        return Str::slug($value);
    }
}
