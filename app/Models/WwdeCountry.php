<?php

namespace App\Models;

use App\Models\WwdeContinent;
use Illuminate\Database\Eloquent\Model;
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

    public function primaryImage()
    {
        // Priorisiere das erste Bild, falls vorhanden
        return $this->image1_path ?? $this->image2_path ?? $this->image3_path ?? null;
    }

}
