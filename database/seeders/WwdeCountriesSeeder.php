<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WwdeCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wwde_countries')->insert([
            [
                'continent_id' => 3, // Beispiel: Europa
                'title' => 'Germany',
                'alias' => 'germany',
                'currency_code' => 'EUR',
                'currency_name' => 'Euro',
                'country_code' => 'DE',
                'country_text' => 'Germany is a country in central Europe known for its rich history and culture.',
                'currency_conversion' => '1.0',
                'population' => 83166711,
                'capital' => 'Berlin',
                'population_capital' => 3644826,
                'area' => 357022,
                'official_language' => 'German',
                'language_ezmz' => 'German',
                'bsp_in_USD' => 45000,
                'life_expectancy_m' => 78.6,
                'life_expectancy_w' => 83.4,
                'population_density' => 233,
                'country_iso_3' => 'DEU',
                'continent_iso_2' => 'EU',
                'continent_iso_3' => 'EUR',
                'country_visum_needed' => false,
                'country_visum_max_time' => null,
                'count_climatezones' => 1,
                'climatezones_ids' => '1',
                'climatezones_lnam' => 'Temperate',
                'climatezones_details_lnam' => 'Moderate climate zone with cold winters and mild summers.',
                'artikel' => 'das',
                'travelwarning_id' => null,
                'price_tendency' => 'Medium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'continent_id' => 3, // Europa
                'title' => 'France',
                'alias' => 'france',
                'currency_code' => 'EUR',
                'currency_name' => 'Euro',
                'country_code' => 'FR',
                'country_text' => 'France is known for its art, cuisine, and iconic landmarks like the Eiffel Tower.',
                'currency_conversion' => '1.0',
                'population' => 67413000,
                'capital' => 'Paris',
                'population_capital' => 2140526,
                'area' => 551695,
                'official_language' => 'French',
                'language_ezmz' => 'French',
                'bsp_in_USD' => 41000,
                'life_expectancy_m' => 79.5,
                'life_expectancy_w' => 85.3,
                'population_density' => 122,
                'country_iso_3' => 'FRA',
                'continent_iso_2' => 'EU',
                'continent_iso_3' => 'EUR',
                'country_visum_needed' => false,
                'country_visum_max_time' => null,
                'count_climatezones' => 3,
                'climatezones_ids' => '1,2,3',
                'climatezones_lnam' => 'Temperate, Mediterranean',
                'climatezones_details_lnam' => 'Diverse climate zones with both temperate and Mediterranean regions.',
                'artikel' => 'das',
                'travelwarning_id' => null,
                'price_tendency' => 'Medium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Füge hier weitere Länder hinzu
        ]);
    }
}
