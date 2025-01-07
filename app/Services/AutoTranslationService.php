<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Repositories\TranslationRepository;
use Stichoza\GoogleTranslate\GoogleTranslate;

class AutoTranslationService
{
    protected $translationRepository;
    protected $googleTranslate;

    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
        $this->googleTranslate = new GoogleTranslate();
    }

    public function trans($originalText, $locale = null)
    {
        $locale = $locale ?? config('app.locale');
        $key = $this->generateKey($originalText);

        $translation = $this->translationRepository->findTranslation($key, $locale);

        if ($translation) {
            return $translation->text;
        }

        $translatedText = $this->translateAndSave($key, $locale, $originalText);
        return $translatedText;
    }

    protected function translateAndSave($key, $locale, $originalText)
    {
        try {
            $this->googleTranslate->setTarget($locale);
            $this->googleTranslate->setSource('auto');

            $translatedText = $this->googleTranslate->translate($originalText);

            $this->translationRepository->saveTranslation($key, $locale, $originalText, $translatedText);

            return $translatedText;
        } catch (\Exception $e) {
            return $originalText; // Fallback zum Originaltext
        }
    }

    protected function generateKey($text)
    {
        return substr(md5($text), 0, 191); // Erzeugt einen eindeutigen Schlüssel basierend auf dem Text
    }
}
