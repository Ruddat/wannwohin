<?php

namespace App\Models;

use App\Models\WwdeClimate;
use App\Models\WwdeCountry;
use App\Models\WwdeContinent;
use App\Models\ModLocationGalerie;
use App\Models\WwdeLocationImages;
use App\Models\ModElectricStandards;
use App\Models\MonthlyClimateSummary;
use App\Models\ModDailyClimateAverage;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModHistoricalClimateData;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwdeLocation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wwde_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'continent_id',
        'country_id',
        'title',
        'alias',
        'iata_code',
        'flight_hours',
        'stop_over',
        'dist_from_FRA',
        'dist_type',
        'lat',
        'lon',
        'bundesstaat_long',
        'bundesstaat_short',
        'no_city_but',
        'list_beach',
        'list_citytravel',
        'list_sports',
        'list_island',
        'list_culture',
        'list_nature',
        'list_watersport',
        'list_wintersport',
        'list_mountainsport',
        'list_biking',
        'list_fishing',
        'list_amusement_park',
        'list_water_park',
        'list_animal_park',
        'best_traveltime',
        'text_pic1',
        'text_pic2',
        'text_pic3',
        'pic1_text',
        'pic2_text',
        'pic3_text',
        'text_headline',
        'text_short',
        'text_location_climate',
        'text_what_to_do',
        'text_best_traveltime',
        'text_sports',
        'text_amusement_parks',
        'climate_details_id',
        'climate_lnam',
        'climate_details_lnam',
        'price_flight',
        'range_flight',
        'price_hotel',
        'range_hotel',
        'price_rental',
        'range_rental',
        'price_travel',
        'range_travel',
        'finished',
        'panorama_text_and_style',
        'time_zone',
        'lat_new',
        'lon_new',
        'population',
        'status',
        'iso2',
        'iso3',
        'currency',
        'best_traveltime_json',
    ];

    /**
     * Relationships
     */
    public function continent()
    {
        return $this->belongsTo(WwdeContinent::class, 'continent_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(WwdeCountry::class, 'country_id', 'id');
    }


    // Beziehung zu den Klimadaten
    public function climates()
    {
        return $this->hasMany(WwdeClimate::class, 'location_id', 'id');
    }

    public function monthlyClimateSummaries()
    {
        return $this->hasMany(MonthlyClimateSummary::class, 'location_id', 'id');
    }

    public function climateArchives()
    {
        return $this->hasMany(WwdeClimateArchive::class, 'location_id', 'id');
    }


    // Beziehung zu LocationImages
    public function images()
    {
        return $this->hasMany(WwdeLocationImages::class);
    }


    // Primärbild abrufen
    // public function primaryImage()
    // {
    //     return $this->images()->where('is_primary', true)->first();
    // }

    public function primaryImage()
    {
        // Priorisiere das erste Bild, falls vorhanden
        return $this->text_pic1 ?? $this->text_pic2 ?? $this->text_pic3 ?? null;
    }

    public function galleryImages()
    {
        return $this->hasMany(ModLocationGalerie::class, 'location_id');
    }

    // Beziehungen
    public function gallery()
    {
        return $this->hasMany(ModLocationGalerie::class, 'location_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFinished($query)
    {
        return $query->where('finished', 1);
    }

    public function scopeWithClimateData($query, $minTemp = null, $minSunHours = null)
    {
        $query->whereHas('climates', function ($q) use ($minTemp, $minSunHours) {
            if ($minTemp) {
                $q->where('water_temperature', '>=', $minTemp);
            }
            if ($minSunHours) {
                $q->where('sunshine_per_day', '>=', $minSunHours);
            }
        });
    }



    public function electric()
    {
        return $this->hasOne(ModElectricStandards::class, 'country_id', 'country_id');
    }

    public function electricStandard()
    {
        return $this->hasOne(ModElectricStandards::class, 'country_id', 'country_id');
    }

    public function dailyAverages()
    {
        return $this->hasMany(ModDailyClimateAverage::class, 'location_id');
    }

    public function historicalData()
    {
        return $this->hasMany(ModHistoricalClimateData::class, 'location_id');
    }




}
