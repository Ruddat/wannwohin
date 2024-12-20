<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WwdeLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wwde_locations')->insert([
            [
                'continent_id' => 3, // Europa
                'country_id' => 1,   // Deutschland
                'title' => 'Berlin',
                'alias' => 'berlin',
                'iata_code' => 'BER',
                'flight_hours' => 1.5,
                'stop_over' => 0,
                'dist_from_FRA' => 500,
                'dist_type' => 'km',
                'lat' => '52.5200',
                'lon' => '13.4050',
                'bundesstaat_long' => null,
                'bundesstaat_short' => null,
                'no_city_but' => null,
                'list_beach' => false,
                'list_citytravel' => true,
                'list_sports' => true,
                'list_island' => false,
                'list_culture' => true,
                'list_nature' => false,
                'list_watersport' => false,
                'list_wintersport' => false,
                'list_mountainsport' => false,
                'list_biking' => true,
                'list_fishing' => false,
                'list_amusement_park' => true,
                'list_water_park' => false,
                'list_animal_park' => false,
                'best_traveltime' => 'April - September',
                'text_pic1' => 'berlin_pic1.jpg',
                'text_pic2' => 'berlin_pic2.jpg',
                'text_pic3' => 'berlin_pic3.jpg',
                'text_headline' => 'Explore Berlin',
                'text_short' => 'Berlin, the capital city of Germany, is known for its history and vibrant culture.',
                'text_location_climate' => 'Berlin has a temperate climate with warm summers and cold winters.',
                'text_what_to_do' => 'Visit the Brandenburg Gate, Museum Island, and enjoy the vibrant nightlife.',
                'text_best_traveltime' => 'April to September offers pleasant weather for exploring the city.',
                'text_sports' => 'Cycling is very popular in Berlin with dedicated bike lanes across the city.',
                'text_amusement_parks' => 'Berlin has various amusement parks like Tropical Islands and Legoland Discovery Centre.',
                'climate_details_id' => null,
                'climate_lnam' => null,
                'climate_details_lnam' => null,
                'price_flight' => 100,
                'range_flight' => 200,
                'price_hotel' => 120,
                'range_hotel' => 3,
                'price_rental' => 50,
                'range_rental' => 2,
                'price_travel' => 300,
                'range_travel' => 3,
                'finished' => true,
                'best_traveltime_json' => json_encode(['April', 'May', 'June', 'July', 'August', 'September']),
                'panorama_text_and_style' => 'Berlin offers a blend of modern and historical architecture.',
                'time_zone' => 'Europe/Berlin',
                'lat_new' => '52.5200',
                'lon_new' => '13.4050',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Weitere Standorte können hier hinzugefügt werden
        ]);
    }
}
