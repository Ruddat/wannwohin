<?php

namespace App\Models;

use App\Models\WwdeContinent;
use App\Models\ModTravelWarning;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwdeCountry extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wwde_countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'continent_id',
        'title',
        'alias',
        'country_headert_titel',
        'country_header_text',
        'currency_code',
        'currency_name',
        'country_code',
        'country_text',
        'currency_conversion',
        'population',
        'capital',
        'population_capital',
        'area',
        'official_language',
        'language_ezmz',
        'bsp_in_USD',
        'life_expectancy_m',
        'life_expectancy_w',
        'population_density',
        'country_iso_3',
        'continent_iso_2',
        'continent_iso_3',
        'country_visum_needed',
        'country_visum_max_time',
        'count_climatezones',
        'climatezones_ids',
        'climatezones_lnam',
        'climatezones_details_lnam',
        'artikel',
        'travelwarning_id',
        'price_tendency',
        'image1_path',
        'image2_path',
        'image3_path',
        'custom_images',
        'status',
        'panorama_image_path',
        'header_image_path',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'population' => 'integer',
        'population_capital' => 'integer',
        'area' => 'integer',
        'bsp_in_USD' => 'integer',
        'life_expectancy_m' => 'float',
        'life_expectancy_w' => 'float',
        'population_density' => 'float',
        'country_visum_needed' => 'boolean',
        'count_climatezones' => 'integer',
        'travelwarning_id' => 'integer',
    ];

    /**
     * Relationships
     */
    public function continent()
    {
        return $this->belongsTo(WwdeContinent::class, 'continent_id', 'id');
    }

    // Beziehungen
    public function locations()
    {
        return $this->hasMany(WwdeLocation::class, 'country_id', 'id');
    }

    /**
     * Scope for active countries
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordering by title
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByTitle($query)
    {
        return $query->orderBy('title');
    }

    /**
     * Check if country is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    // Beziehungen and Additions
    public function primaryImage()
    {
        $images = array_filter([
            $this->image1_path,
            $this->image2_path,
            $this->image3_path
        ]);

        if (!empty($images)) {
            $randomImage = $images[array_rand($images)]; // Zufälliges Bild auswählen
            return asset('storage/' . $randomImage);
        }

        return asset('img/default-location.png'); // Fallback-Bild, falls kein Bild vorhanden ist
    }

    /**
     * Beziehung zu einer Reisewarnung.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function travelWarning()
    {
        return $this->hasOne(ModTravelWarning::class, 'iso2', 'country_code');
    }

    public function getThumbnailAttribute()
    {
        // Falls das Bild existiert und sich im "storage/app/public" Verzeichnis befindet
        if ($this->image1_path && Storage::exists($this->image1_path)) {
            return Storage::url($this->image1_path);
        }

        // Falls das Bild im "public" Ordner liegt
        if ($this->image1_path && file_exists(public_path($this->image1_path))) {
            return asset($this->image1_path);
        }

        // Falls kein Bild vorhanden ist, Standardbild verwenden
        return asset('img/default-country-thumbnail.png');
    }

    /**
     * Get climate zones as array
     *
     * @return array
     */
    public function getClimateZonesArrayAttribute()
    {
        if (empty($this->climatezones_lnam)) {
            return [];
        }

        // Entferne "der" oder "den" Präfixe und trenne bei "und"
        $zones = preg_split('/\s+und\s+/', $this->climatezones_lnam);

        // Bereinige jeden Eintrag
        return array_map(function($zone) {
            return trim(preg_replace('/^(der|den)\s+/', '', $zone));
        }, $zones);
    }

    /**
     * Get price tendency label
     *
     * @return string
     */
    public function getPriceTendencyLabelAttribute()
    {
        $labels = [
            'Niedrig' => 'Niedrig',
            'Mittel' => 'Mittel',
            'Hoch' => 'Hoch',
        ];

        return $labels[$this->price_tendency] ?? $this->price_tendency;
    }
}
