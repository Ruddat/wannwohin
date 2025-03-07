<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ModSiteSettings extends Model
{
    protected $table = 'mod_site_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Abrufen einer Einstellung nach Schlüssel.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = Cache::remember("site_setting_{$key}", now()->addHour(), function () use ($key) {
            return static::where('key', $key)->first();
        });

        // Prüfe, ob $setting ein Objekt ist und 'type' hat
        if (!$setting || !is_object($setting) || !isset($setting->type)) {
            return $default;
        }

        // Typgerechte Rückgabe
        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Alle öffentlichen Einstellungen als Array abrufen.
     *
     * @return array
     */
    public static function getPublicSettings()
    {
        return static::where('is_public', true)
            ->pluck('value', 'key')
            ->all();
    }
}
