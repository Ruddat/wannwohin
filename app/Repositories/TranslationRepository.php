<?php

namespace App\Repositories;

use App\Models\AutoTranslations;
use Illuminate\Support\Facades\Log;

class TranslationRepository
{
    // Maximale Länge für den Schlüssel (entspricht der Datenbank-Definition)
    const MAX_KEY_LENGTH = 1024;

    public function findTranslation($key, $locale)
    {
        // Kürze den Schlüssel vor der Suche
        $shortenedKey = substr($key, 0, self::MAX_KEY_LENGTH);

        return AutoTranslations::where('key', $shortenedKey)
                              ->where('locale', $locale)
                              ->first();
    }

    public function saveTranslation($key, $locale, $text)
    {
        // Kürze den Schlüssel vor dem Speichern
        $shortenedKey = substr($key, 0, self::MAX_KEY_LENGTH);

        return AutoTranslations::updateOrCreate(
            ['key' => $shortenedKey, 'locale' => $locale],
            ['text' => $text]
        );
    }
}
