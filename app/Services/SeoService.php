<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\ModSeoMeta;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\MaintenanceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class SeoService
{
    public function getSeoData($model)
    {

        $maintenanceService = app(MaintenanceService::class);
    if ($maintenanceService->isMaintenanceModeActive()) {
        return [
            'title' => 'Wartungsmodus',
            'description' => $maintenanceService->getMaintenanceMessage(),
            'image' => asset('maintenance-image.jpg'), // Optional: Bild hinzufügen
            'canonical' => url()->current(),
            'extra_meta' => [],
            'keywords' => [],
        ];
    }


        // Falls $model ein Array ist (z. B. für die Startseite oder Suchergebnisse)
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

            // Extrahiere aktuelle Modelldaten
            $titleBase = $this->getModelTitle($model, $modelType);
            $keywords = $this->generateKeywords($titleBase, $modelType);
            $imageUrl = $this->getModelImage($model, $modelType);
            $canonicalUrl = $model['canonical'] ?? $this->generateCanonicalUrl($model, $modelType);

            if (!$seo) {
                Log::info("Kein SEO-Eintrag für {$modelType} - ID: {$modelId} gefunden. Erstelle neuen.");
                $seo = $this->createSeoEntry($modelType, $modelId, $titleBase, $keywords, $imageUrl, $canonicalUrl);
            } elseif (!$seo->prevent_override) {
                // Aktualisiere den Eintrag, wenn prevent_override nicht gesetzt ist
                $this->updateSeoEntry($seo, $titleBase, $keywords, $imageUrl, $canonicalUrl);
            }

            return [
                'title'       => $seo->title,
                'description' => $seo->description,
                'image'       => $seo->image,
                'canonical'   => $seo->canonical,
             //   'extra_meta'  => json_decode($seo->extra_meta, true),
            //    'keywords'    => json_decode($seo->keywords, true) ?? [],
'extra_meta' => is_array($seo->extra_meta) ? $seo->extra_meta : json_decode($seo->extra_meta, true),
'keywords'    => is_array($seo->keywords) ? $seo->keywords : (json_decode($seo->keywords, true) ?? []),


        ];
        });
    }

    private function createSeoEntry($modelType, $modelId, $titleBase, $keywords, $imageUrl, $canonicalUrl)
    {

        $siteName = $this->getSiteSetting('site_name', 'WannWohin.de'); // Dynamischer Site-Name
        $defaultExtraMeta = $this->generateDefaultExtraMeta($titleBase, $keywords, $imageUrl, $canonicalUrl);

        return ModSeoMeta::create([
            'model_type'      => $modelType,
            'model_id'        => $modelId,
            'title'           => "Urlaub in {$titleBase} {$keywords['nextYear']} | {$keywords['main']} – {$siteName}",            'description'     => "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei WannWohin.de!",
            'description'     => "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei {$siteName}!",
            'canonical'       => $canonicalUrl,
            'image'           => $imageUrl,
            'extra_meta'      => json_encode($defaultExtraMeta),
            'keywords'        => json_encode($keywords),
            'prevent_override' => false, // Standardmäßig erlauben wir Überschreiben
        ]);
    }

    private function updateSeoEntry($seo, $titleBase, $keywords, $imageUrl, $canonicalUrl)
    {
        $defaultExtraMeta = $this->generateDefaultExtraMeta($titleBase, $keywords, $imageUrl, $canonicalUrl);

        // Prüfe, ob sich relevante Daten geändert haben
        $needsUpdate = $seo->title !== "Urlaub in {$titleBase} {$keywords['nextYear']} | {$keywords['main']} – WannWohin.de" ||
                       $seo->description !== "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei WannWohin.de!" ||
                       $seo->image !== $imageUrl ||
                       $seo->canonical !== $canonicalUrl;

        if ($needsUpdate) {
            $seo->update([
                'title'       => "Urlaub in {$titleBase} {$keywords['nextYear']} | {$keywords['main']} – WannWohin.de",
                'description' => "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei WannWohin.de!",
                'canonical'   => $canonicalUrl,
                'image'       => $imageUrl,
                'extra_meta'  => json_encode($defaultExtraMeta),
                'keywords'    => json_encode($keywords),
            ]);
            Log::info("SEO-Eintrag für {$seo->model_type} - ID: {$seo->model_id} wurde aktualisiert.");
        }
    }

    private function generateDefaultExtraMeta($titleBase, $keywords, $imageUrl, $canonicalUrl)
    {


        $siteName = $this->getSiteSetting('site_name', 'WannWohin.de');
        $twitterHandle = $this->getSiteSetting('twitter_handle', '@WannWohin');
        $facebookUrl = $this->getSiteSetting('facebook_url', 'https://facebook.com/wannwohin');

        return [

            'og:title'       => "Urlaub in {$titleBase} {$keywords['nextYear']} | {$keywords['main']} – {$siteName}",
            'og:description' => "Entdecke die besten Reiseziele in {$titleBase}: {$keywords['description']}, Wetter, Klima und Reiseziele. Buchen Sie jetzt bei {$siteName}!",
            'og:image'       => $imageUrl,
            'og:url'         => $canonicalUrl,
            'og:type'        => 'website',
            'og:locale'      => 'de_DE',
            'fb:app_id'      => '1234567890',
            'article:published_time' => now()->toIso8601String(),
            'article:modified_time'  => now()->toIso8601String(),
            'twitter:card'    => 'summary_large_image',
            'twitter:title'   => "{$titleBase} - Beste Reisezeiten & Klima",
            'twitter:description' => "Erfahre mehr über das Klima & die besten Reisezeiten für {$titleBase}.",
            'twitter:image'   => $imageUrl,
            'twitter:site'    => $twitterHandle,
            'twitter:creator' => $twitterHandle,
            'apple-itunes-app' => 'app-id=123456789, affiliate-data=myAffiliateData, app-argument=myAppURL',
        ];
    }

    private function generateCanonicalUrl($model, $modelType)
    {
        if ($model instanceof WwdeContinent && Route::has('continent.countries')) {
            return route('continent.countries', $model->alias);
        } elseif ($model instanceof WwdeCountry && Route::has('list-country-locations')) {
            return route('list-country-locations', [$model->continent->alias, $model->alias]);
        } elseif ($model instanceof WwdeLocation && Route::has('location.details')) {
            return route('location.details', [$model->continent->alias, $model->country->alias, $model->alias]);
        }
        return url()->current();
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

    private function getModelImage($model, $modelType)
    {
        if ($model instanceof WwdeLocation) {
            // Nutze die primaryImage()-Methode, um das Hauptbild zu priorisieren
            $primaryImage = $model->primaryImage();
            if ($primaryImage) {
                return $this->formatImageUrl($primaryImage); // Verarbeite den Bildpfad
            }

            // Fallback auf andere Bildfelder, wenn primaryImage() nichts zurückgibt
            $imageFields = [
                'main_img',
                'panorama_text_and_style', // Annahme: JSON oder String mit Bildpfad
                'text_pic1',
                'text_pic2',
                'text_pic3',
            ];

            foreach ($imageFields as $field) {
                $image = $model->$field;
                if ($image && !empty($image)) {
                    // Prüfe, ob es ein JSON-Feld (z. B. panorama_text_and_style) ist
                    if ($field === 'panorama_text_and_style' && is_string($image)) {
                        $decoded = json_decode($image, true);
                        if ($decoded && isset($decoded['image'])) {
                            return $this->formatImageUrl($decoded['image']);
                        }
                    } else {
                        return $this->formatImageUrl($image);
                    }
                }
            }

            // Fallback auf default-bg.jpg, wenn kein Bild gefunden wurde
            return asset('default-bg.jpg');
        } elseif ($model instanceof WwdeContinent) {
            return $model->main_img ? $this->formatImageUrl($model->main_img) : asset('default-bg.jpg');
        } elseif ($model instanceof WwdeCountry) {
            return $model->main_img ? $this->formatImageUrl($model->main_img) : asset('default-bg.jpg');
        } elseif (is_array($model)) {
            return $model['image'] ?? asset('default-bg.jpg');
        }
        return asset('default-bg.jpg');
    }

    private function formatImageUrl($imagePath)
    {
        // Prüfe, ob der Pfad bereits eine absolute URL ist (beginnt mit http:// oder https://)
        if (preg_match('/^https?:\/\//i', $imagePath)) {
            return $imagePath; // Rückgabe der absoluten URL unverändert
        }

        // Prüfe, ob der Pfad bereits mit /storage beginnt (z. B. /storage/uploads/images/...)
        if (Str::startsWith($imagePath, '/storage')) {
            return url($imagePath); // Verwende url(), um die Basis-URL hinzuzufügen, ohne zu duplizieren
        }

        // Für relative Pfade (z. B. uploads/images/locations/hamburg/city_image_1.jpg)
        return Storage::url($imagePath); // Füge die Storage-Basis-URL hinzu
    }

    private function generateKeywords($locationName, $modelType)
    {
        $nextYear = Carbon::now()->year;
        $defaultKeywords = $this->getSiteSetting('default_meta_keywords', ['reisen', 'urlaub', 'wetter']);

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
            'tags' => array_merge($defaultKeywords, [
                "{$locationName} Urlaub {$nextYear}",
                "Wetter {$locationName} {$nextYear}",
                "Reiseziele in {$locationName} {$nextYear}",
            ]),
            'nextYear' => $nextYear,
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



    /**
     * Einstellung aus der mod_site_settings-Tabelle abrufen
     */
    private function getSiteSetting($key, $default = null)
    {
        $setting = DB::table('mod_site_settings')->where('key', $key)->first();

        if (!$setting) {
            return $default; // Fallback-Wert, falls die Einstellung nicht existiert
        }

        // Typkonvertierung basierend auf dem 'type'-Feld
        return match ($setting->type) {
            'json' => json_decode($setting->value, true),
            'boolean' => (bool) $setting->value,
            'file', 'string' => $setting->value,
            default => $setting->value,
        };
    }


}
