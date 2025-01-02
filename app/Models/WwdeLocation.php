<?php

namespace App\Models;

use App\Models\WwdeClimate;
use App\Models\WwdeCountry;
use App\Models\WwdeContinent;
use App\Models\LocationGallery;
use App\Models\WwdeLocationImages;
use App\Models\MonthlyClimateSummary;
use Illuminate\Database\Eloquent\Model;
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
    ];

    /**
     * Relationships
     */
    public function continent()
    {
        return $this->belongsTo(WwdeContinent::class);
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
    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    public function galleryImages()
    {
        return $this->hasMany(LocationGallery::class, 'location_id');
    }

    // Beziehungen
    public function gallery()
    {
        return $this->hasMany(LocationGallery::class, 'location_id', 'id');
    }

}
