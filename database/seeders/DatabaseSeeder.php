<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\WWLoschSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            WwdeContinentsSeeder::class, // Für Kontinente
            WwdeCountriesSeeder::class,  // Für Länder
            WwdeLocationsSeeder::class,  // Für Standorte
            WwdeRangesSeeder::class,     // Für Preisbereiche
            HeaderContentSeeder::class,     // Für Preisbereiche
            ModLanguagesSeeder::class, // Für Sprachen
            AdminSeeder::class, // Für Admins
        ]);


    }
}
