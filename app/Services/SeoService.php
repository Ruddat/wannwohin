<?php

namespace App\Services;

use App\Models\ModSeoMeta;
use App\Models\WwdeContinent;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class SeoService
{
    public function getSeoData($model)
    {
        // Falls $model ein Array ist (z.B. für die Startseite oder Suchergebnisse)
        if (is_array($model)) {
            $modelType = $model['model_type'];
            $modelId = $model['model_id'];
        } else {
            $modelType = get_class($model);
            $modelId = $model->id;
        }

        $cacheKey = "seo_{$modelType}_{$modelId}";
        return Cache::remember($cacheKey, now()->addWeek(), function () use ($model, $modelType, $modelId) {
            // Prüfe, ob gespeicherte SEO-Daten existieren
            $seo = ModSeoMeta::where('model_type', $modelType)
                             ->where('model_id', $modelId)
                             ->first();

            if (!$seo) {
                Log::info("Kein SEO-Eintrag für {$modelType} - ID: {$modelId} gefunden. Erstelle neuen.");

                // Extrahiere den tatsächlichen Namen (title oder alias) basierend auf dem Modelltyp
                $titleBase = $this->getModelTitle($model, $modelType);

                // Dynamische Keywords basierend auf der Location
                $keywords = $this->generateKeywords($titleBase, $modelType);

                // Interne Links generieren (z. B. zu verwandten Standorten)
                $internalLinks = $this->generateInternalLinks($model);

                // Standardwerte für OpenGraph und Twitter
                $defaultExtraMeta = [
                    'og:title'       => "Urlaub in {$titleBase} {$keywords['nextYear']} | {$keywords['main']} – WannWohin.de",
                    'og:description' => "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei WannWohin.de!",
                    'og:image'       => $model->main_img ?? asset('default-bg.jpg'),
                    'og:url'         => $model['canonical'] ?? (Route::has('continent.countries') && $model instanceof WwdeContinent ? route('continent.countries', $model->alias) :
                                       (Route::has('list-country-locations') && $model instanceof WwdeCountry ? route('list-country-locations', [$model->continent->alias, $model->alias]) :
                                       (Route::has('location.details') && $model instanceof WwdeLocation ? route('location.details', [$model->continent->alias, $model->country->alias, $model->alias]) : url()->current()))),
                    'og:type'        => 'website',
                    'og:locale'      => 'de_DE',

                    // Twitter Cards
                    'twitter:card'        => 'summary_large_image',
                    'twitter:title'       => "Urlaub in {$titleBase} {$keywords['nextYear']} | {$keywords['main']} – WannWohin.de",
                    'twitter:description' => "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei WannWohin.de!",
                    'twitter:image'       => $model->main_img ?? asset('default-bg.jpg'),
                    'twitter:site'        => '@WannWohin',
                ];

                $seo = ModSeoMeta::create([
                    'model_type'  => $modelType,
                    'model_id'    => $modelId,
                    'title'       => "Urlaub in {$titleBase} {$keywords['nextYear']} | {$keywords['main']} – WannWohin.de",
                    'description' => "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei WannWohin.de!",
                    'canonical'   => $model['canonical'] ?? (Route::has('continent.countries') && $model instanceof WwdeContinent ? route('continent.countries', $model->alias) :
                                   (Route::has('list-country-locations') && $model instanceof WwdeCountry ? route('list-country-locations', [$model->continent->alias, $model->alias]) :
                                   (Route::has('location.details') && $model instanceof WwdeLocation ? route('location.details', [$model->continent->alias, $model->country->alias, $model->alias]) : url()->current()))),
                    'image'       => $model->main_img ?? asset('default-bg.jpg'),
                    'extra_meta'  => json_encode($defaultExtraMeta),
                    'keywords'    => json_encode($keywords),
                ]);
            }

            return [
                'title'       => $seo->title,
                'description' => $seo->description,
                'image'       => $seo->image,
                'canonical'   => $seo->canonical,
                'extra_meta'  => json_decode($seo->extra_meta, true),
                'keywords'    => json_decode($seo->keywords, true) ?? [],
            ];
        });
    }

    private function getModelTitle($model, $modelType)
    {
        if ($model instanceof WwdeLocation) {
            return $model->title ?? $model->alias ?? 'Unbekannter Standort';
        } elseif ($model instanceof WwdeContinent) {
            return $model->title ?? $model->alias ?? 'Unbekannter Kontinent';
        } elseif ($model instanceof WwdeCountry) {
            return $model->title ?? $model->alias ?? 'Unbekanntes Land';
        } elseif (is_array($model)) {
            return $model['title'] ?? $modelType;
        }
        return $modelType;
    }

    private function generateKeywords($locationName, $modelType)
    {
        $nextYear = Carbon::now()->addYear()->year; // Nächstes Jahr

        $keywords = [
            'main' => match ($modelType) {
                WwdeContinent::class => "Urlaub, Reiseziele, Wetter in {$locationName} {$nextYear}",
                WwdeCountry::class => "Urlaub, Reiseziele, Wetter in {$locationName} {$nextYear}",
                WwdeLocation::class => "Urlaub, Reiseziele, Wetter in {$locationName} {$nextYear}",
                default => "Reiseinfos für {$locationName} {$nextYear}",
            },
            'description' => match ($modelType) {
                WwdeContinent::class => "Entdecke die besten Reiseziele, Wetter und Klima in {$locationName} im Jahr {$nextYear}.",
                WwdeCountry::class => "Entdecke die besten Reiseziele, Wetter und Klima in {$locationName} im Jahr {$nextYear}.",
                WwdeLocation::class => "Entdecke die besten Reiseziele, Wetter und Klima in {$locationName} im Jahr {$nextYear}.",
                default => "Reiseinformationen und Wetter für {$locationName} im Jahr {$nextYear}.",
            },
            'tags' => [
                "{$locationName} Urlaub {$nextYear}",
                "Wetter {$locationName} {$nextYear}",
                "Reiseziele in {$locationName} {$nextYear}",
            ],
            'nextYear' => $nextYear, // Speichere das Jahr für die Verwendung in getSeoData
        ];

        return $keywords;
    }

    private function generateInternalLinks($model)
    {
        $links = [];
        if ($model instanceof WwdeContinent) {
            $countries = WwdeCountry::where('continent_id', $model->id)->take(3)->get();
            foreach ($countries as $country) {
                $links[] = "<a href=\"" . route('list-country-locations', [$model->alias, $country->alias]) . "\">{$country->title}</a>";
            }
        } elseif ($model instanceof WwdeCountry) {
            $locations = WwdeLocation::where('country_id', $model->id)->take(3)->get();
            foreach ($locations as $location) {
                $links[] = "<a href=\"" . route('location.details', [$model->continent->alias, $model->alias, $location->alias]) . "\">{$location->title}</a>";
            }
        } elseif ($model instanceof WwdeLocation) {
            $relatedLocations = WwdeLocation::where('country_id', $model->country_id)
                                          ->where('id', '!=', $model->id)
                                          ->take(3)->get();
            foreach ($relatedLocations as $location) {
                $links[] = "<a href=\"" . route('location.details', [$model->continent->alias, $model->country->alias, $location->alias]) . "\">{$location->title}</a>";
            }
        }
        return !empty($links) ? 'Weitere Ziele: ' . implode(', ', $links) . '.' : '';
    }
}
