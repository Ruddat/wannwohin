<?php

namespace App\Repositories;

use App\Models\AutoTranslations;
use Illuminate\Support\Facades\Log;

class TranslationRepository
{
    public function findTranslation($key, $locale)
    {
        return AutoTranslations::where('key', $key)
                               ->where('locale', $locale)
                               ->first();
    }

    public function saveTranslation($key, $locale, $originalText, $translatedText)
    {
        return AutoTranslations::updateOrCreate(
            ['key' => $key, 'locale' => $locale],
            ['original_text' => $originalText, 'text' => $translatedText]
        );
    }
}
