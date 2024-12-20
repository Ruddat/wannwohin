<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WwdeRangesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wwde_ranges')->insert([
            ['id' => 1, 'Sort' => 1, 'Range_to_show' => '100€', 'Type' => 'Flight'],
            ['id' => 2, 'Sort' => 2, 'Range_to_show' => '250€', 'Type' => 'Flight'],
            ['id' => 3, 'Sort' => 3, 'Range_to_show' => '500€', 'Type' => 'Flight'],
            ['id' => 4, 'Sort' => 4, 'Range_to_show' => '750€', 'Type' => 'Flight'],
            ['id' => 5, 'Sort' => 5, 'Range_to_show' => '1.000€', 'Type' => 'Flight'],
            ['id' => 6, 'Sort' => 6, 'Range_to_show' => '1.500€', 'Type' => 'Flight'],
            ['id' => 7, 'Sort' => 7, 'Range_to_show' => '2.000€', 'Type' => 'Flight'],
            ['id' => 8, 'Sort' => 8, 'Range_to_show' => '>2.000€', 'Type' => 'Flight'],
            ['id' => 20, 'Sort' => 1, 'Range_to_show' => '100€', 'Type' => 'Hotel'],
            ['id' => 21, 'Sort' => 2, 'Range_to_show' => '250€', 'Type' => 'Hotel'],
            ['id' => 22, 'Sort' => 3, 'Range_to_show' => '500€', 'Type' => 'Hotel'],
            ['id' => 23, 'Sort' => 4, 'Range_to_show' => '750€', 'Type' => 'Hotel'],
            ['id' => 24, 'Sort' => 5, 'Range_to_show' => '1.000€', 'Type' => 'Hotel'],
            ['id' => 25, 'Sort' => 6, 'Range_to_show' => '>1.000€', 'Type' => 'Hotel'],
            ['id' => 40, 'Sort' => 1, 'Range_to_show' => '100€', 'Type' => 'Rental'],
            ['id' => 41, 'Sort' => 2, 'Range_to_show' => '250€', 'Type' => 'Rental'],
            ['id' => 42, 'Sort' => 3, 'Range_to_show' => '500€', 'Type' => 'Rental'],
            ['id' => 43, 'Sort' => 4, 'Range_to_show' => '750€', 'Type' => 'Rental'],
            ['id' => 44, 'Sort' => 5, 'Range_to_show' => '1.500€', 'Type' => 'Rental'],
            ['id' => 45, 'Sort' => 6, 'Range_to_show' => '>1.500€', 'Type' => 'Rental'],
            ['id' => 60, 'Sort' => 1, 'Range_to_show' => '250€', 'Type' => 'Travel'],
            ['id' => 61, 'Sort' => 2, 'Range_to_show' => '500€', 'Type' => 'Travel'],
            ['id' => 62, 'Sort' => 3, 'Range_to_show' => '750€', 'Type' => 'Travel'],
            ['id' => 63, 'Sort' => 4, 'Range_to_show' => '1.000€', 'Type' => 'Travel'],
            ['id' => 64, 'Sort' => 5, 'Range_to_show' => '1.500€', 'Type' => 'Travel'],
            ['id' => 65, 'Sort' => 6, 'Range_to_show' => '2.000€', 'Type' => 'Travel'],
            ['id' => 66, 'Sort' => 7, 'Range_to_show' => '>2.000€', 'Type' => 'Travel'],
        ]);
    }
}
