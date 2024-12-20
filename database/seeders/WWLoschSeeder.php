<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WWLoschSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['ID' => 1, 'Name' => 'bulgarien', 'BSP' => 25312, 'EW' => 6687717, 'Preis' => 'Niedrig'],
            ['ID' => 2, 'Name' => 'aegypten', 'BSP' => 3636, 'EW' => 112716598, 'Preis' => 'Niedrig'],
            ['ID' => 3, 'Name' => 'armenien', 'BSP' => 14257, 'EW' => 2777970, 'Preis' => 'Niedrig'],
            ['ID' => 4, 'Name' => 'deutschland', 'BSP' => 57530, 'EW' => 83294633, 'Preis' => 'Mittel'],
            ['ID' => 5, 'Name' => 'italien', 'BSP' => 45722, 'EW' => 58870762, 'Preis' => 'Mittel'],
            // Weitere Einträge hier einfügen
            ['ID' => 412, 'Name' => 'haiti', 'BSP' => 3034, 'EW' => 11724763, 'Preis' => 'Niedrig'],
        ];

        foreach ($data as $row) {
            DB::table('w_w_losches')->insert([
                'id' => $row['ID'],
                'Name' => $row['Name'],
                'BSP' => $row['BSP'],
                'EW' => $row['EW'],
                'Preis' => $row['Preis'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
