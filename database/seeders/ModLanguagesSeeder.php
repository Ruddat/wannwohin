<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ModLanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['code' => 'en', 'name' => 'Englisch'],
            ['code' => 'de', 'name' => 'Deutsch'],
            ['code' => 'fr', 'name' => 'Französisch'],
            ['code' => 'es', 'name' => 'Spanisch'],
            ['code' => 'it', 'name' => 'Italienisch'],
            ['code' => 'zh', 'name' => 'Chinesisch'],
            ['code' => 'ja', 'name' => 'Japanisch'],
            ['code' => 'ru', 'name' => 'Russisch'],
        ];

        DB::table('mod_languages')->insert($languages);
    }
}
