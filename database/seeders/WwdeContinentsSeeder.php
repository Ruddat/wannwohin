<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WwdeContinentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wwde_continents')->insert([
            [
                'title' => 'Africa',
                'alias' => 'africa',
                'iso2' => 'AF',
                'iso3' => 'AFR',
                'area_km' => 30370000,
                'population' => 1340598147,
                'no_countries' => 54,
                'no_climate_tables' => 10,
                'continent_text' => 'Africa is the second largest continent in the world, known for its vast savannahs, deserts, and diverse cultures.',
                'continent_header_text' => 'The cradle of civilization and the second-largest continent.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Antarctica',
                'alias' => 'antarctica',
                'iso2' => 'AN',
                'iso3' => 'ANT',
                'area_km' => 14000000,
                'population' => 0,
                'no_countries' => 0,
                'no_climate_tables' => 1,
                'continent_text' => 'Antarctica is the coldest and least populated continent, covered almost entirely by ice.',
                'continent_header_text' => 'The icy, uninhabited southernmost continent.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Asia',
                'alias' => 'asia',
                'iso2' => 'AS',
                'iso3' => 'ASI',
                'area_km' => 44579000,
                'population' => 4641054775,
                'no_countries' => 49,
                'no_climate_tables' => 15,
                'continent_text' => 'Asia is the largest and most populous continent, home to a wide range of climates and cultures.',
                'continent_header_text' => 'The largest continent with diverse cultures and climates.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Europe',
                'alias' => 'europe',
                'iso2' => 'EU',
                'iso3' => 'EUR',
                'area_km' => 10180000,
                'population' => 746419440,
                'no_countries' => 44,
                'no_climate_tables' => 12,
                'continent_text' => 'Europe is known for its rich history, cultural diversity, and influential countries.',
                'continent_header_text' => 'A continent of history, culture, and innovation.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'North America',
                'alias' => 'north-america',
                'iso2' => 'NA',
                'iso3' => 'NAM',
                'area_km' => 24709000,
                'population' => 592072212,
                'no_countries' => 23,
                'no_climate_tables' => 8,
                'continent_text' => 'North America is known for its vast natural landscapes, cultural diversity, and economic powerhouses.',
                'continent_header_text' => 'The continent of diversity and innovation.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Oceania',
                'alias' => 'oceania',
                'iso2' => 'OC',
                'iso3' => 'OCE',
                'area_km' => 8600000,
                'population' => 43111704,
                'no_countries' => 14,
                'no_climate_tables' => 6,
                'continent_text' => 'Oceania encompasses Australia, New Zealand, and numerous Pacific islands with unique cultures and landscapes.',
                'continent_header_text' => 'A region of islands and diverse ecosystems.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'South America',
                'alias' => 'south-america',
                'iso2' => 'SA',
                'iso3' => 'SAM',
                'area_km' => 17840000,
                'population' => 430759766,
                'no_countries' => 12,
                'no_climate_tables' => 7,
                'continent_text' => 'South America is famous for the Amazon rainforest, the Andes mountains, and vibrant cultures.',
                'continent_header_text' => 'The home of the Amazon and vibrant cultures.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
