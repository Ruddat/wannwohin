<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModElectricStandardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('electric_standards')->insert([
            [
                'country_id' => 7,
                'power' => '220V, 50Hz',
                'info' => null,
                'typ_a' => 0,
                'typ_b' => 0,
                'typ_c' => 1,
                'typ_d' => 0,
                'typ_e' => 0,
                'typ_f' => 1,
                'typ_g' => 0,
                'typ_h' => 0,
                'typ_i' => 0,
                'typ_j' => 0,
                'typ_k' => 0,
                'typ_l' => 1,
                'typ_m' => 0,
                'typ_n' => 0,
            ],
            [
                'country_id' => 8,
                'power' => '230V, 50Hz',
                'info' => null,
                'typ_a' => 0,
                'typ_b' => 0,
                'typ_c' => 1,
                'typ_d' => 0,
                'typ_e' => 0,
                'typ_f' => 1,
                'typ_g' => 0,
                'typ_h' => 0,
                'typ_i' => 0,
                'typ_j' => 0,
                'typ_k' => 0,
                'typ_l' => 0,
                'typ_m' => 0,
                'typ_n' => 0,
            ],
            // Weitere Einträge...
        ]);
    }
}
