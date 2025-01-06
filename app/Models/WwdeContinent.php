<?php

namespace App\Models;

use App\Models\WwdeCountry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwdeContinent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wwde_continents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'alias',
        'area_km',
        'population',
        'no_countries',
        'no_climate_tables',
        'continent_text',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'area_km' => 'integer',
        'population' => 'integer',
        'no_countries' => 'integer',
        'no_climate_tables' => 'integer',
    ];

    public function countries()
    {
        return $this->hasMany(WwdeCountry::class, 'continent_id');
    }
    public function getCountriesCountAttribute()
    {
        return $this->countries()->count();
    }
    
}
