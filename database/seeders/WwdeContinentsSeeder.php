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
                'area_km' => 30370000,
                'population' => 1340598147,
                'no_countries' => 54,
                'no_climate_tables' => 10,
                'continent_text' => 'Africa is the second largest continent in the world.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Antarctica',
                'alias' => 'antarctica',
                'area_km' => 14000000,
                'population' => 0,
                'no_countries' => 0,
                'no_climate_tables' => 1,
                'continent_text' => 'Antarctica is the coldest and least populated continent.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Asia',
                'alias' => 'asia',
                'area_km' => 44579000,
                'population' => 4641054775,
                'no_countries' => 49,
                'no_climate_tables' => 15,
                'continent_text' => 'Asia is the largest and most populous continent.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Europe',
                'alias' => 'europe',
                'area_km' => 10180000,
                'population' => 746419440,
                'no_countries' => 44,
                'no_climate_tables' => 12,
                'continent_text' => 'Europe is known for its rich history and culture.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'North America',
                'alias' => 'north-america',
                'area_km' => 24709000,
                'population' => 592072212,
                'no_countries' => 23,
                'no_climate_tables' => 8,
                'continent_text' => 'North America is known for its diverse geography and cultures.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Oceania',
                'alias' => 'oceania',
                'area_km' => 8600000,
                'population' => 43111704,
                'no_countries' => 14,
                'no_climate_tables' => 6,
                'continent_text' => 'Oceania includes Australia, New Zealand, and Pacific islands.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'South America',
                'alias' => 'south-america',
                'area_km' => 17840000,
                'population' => 430759766,
                'no_countries' => 12,
                'no_climate_tables' => 7,
                'continent_text' => 'South America is known for the Amazon rainforest and Andes mountains.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
