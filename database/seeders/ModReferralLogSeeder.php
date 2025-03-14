<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModReferralLog;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ModReferralLogSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $sources = [
            'google', 'direct', 'bing', 'facebook', 'twitter',
            'linkedin', 'instagram', 'youtube', 'pinterest', 'duckduckgo',
            'reddit', 'tiktok', 'organic', 'email', 'adwords'
        ];
        $keywords = [
            null, 'seo', 'laravel', 'webdesign', 'analytics', 'php',
            'marketing', 'ecommerce', 'blogging', 'social media', 'coding',
            'tutorial', 'news', 'trends', 'technology', 'business'
        ];
        $landingPages = [
            '/home', '/about-us', '/products', '/blog', '/contact',
            '/services/seo', '/portfolio', '/pricing', '/team', '/faq',
            '/blog/post-1', '/blog/post-2', '/products/item-1', '/products/item-2',
            '/careers', '/events', '/support', '/login', '/signup'
        ];

        // Generiere 5000 Einträge für verschiedene Zeitbereiche
        for ($i = 0; $i < 5000; $i++) {
            $visitedAt = $this->randomDateInRange();
            $source = $faker->randomElement($sources);
            $keyword = $faker->randomElement($keywords);
            $landingPage = $faker->randomElement($landingPages);

            ModReferralLog::create([
                'user_id' => null, // Immer null, um Fremdschlüsselprobleme zu vermeiden
                'referer_url' => $source === 'direct' ? null : "https://{$source}.com/search?q=" . ($keyword ?? $faker->word),
                'source' => $source,
                'keyword' => $keyword,
                'landing_page' => $landingPage,
                'ip_address' => $faker->ipv4,
                'visit_count' => $faker->numberBetween(1, 20), // Höhere Varianz
                'visited_at' => $visitedAt,
                'created_at' => $visitedAt,
                'updated_at' => $visitedAt,
            ]);
        }

        // Spezifische Szenarien für Tests
        $this->addSpecificData($faker);
    }

    private function randomDateInRange()
    {
        $faker = Faker::create();
        $start = Carbon::now()->subYears(10);
        $end = Carbon::now();

        // Gewichtete Verteilung für verschiedene Zeitbereiche
        $range = $faker->randomElement([
            [$start, Carbon::now()->subYears(5)],                  // Ältere Daten (5-10 Jahre)
            [Carbon::now()->subYears(5), Carbon::now()->subYear()], // 1-5 Jahre
            [Carbon::now()->subYear(), Carbon::now()->subDays(30)],  // Letztes Jahr
            [Carbon::now()->subDays(30), Carbon::now()->subDays(7)], // Letzte 30 Tage
            [Carbon::now()->subDays(7), Carbon::now()->subHours(24)], // Letzte 7 Tage
            [Carbon::now()->subHours(24), $end],                    // Letzte 24 Stunden
        ], [0.2, 0.2, 0.2, 0.2, 0.15, 0.05]); // Gewichte für Verteilung

        return $faker->dateTimeBetween($range[0], $range[1]);
    }

    private function addSpecificData($faker)
    {
        $sources = ['google', 'facebook', 'direct'];
        $landingPages = ['/home', '/products', '/blog'];

        // Daten für März 2024 (zum Testen von Monat/Jahr-Filter)
        for ($i = 0; $i < 200; $i++) {
            $visitedAt = Carbon::create(2024, 3, $faker->numberBetween(1, 31), $faker->numberBetween(0, 23), $faker->numberBetween(0, 59));
            ModReferralLog::create([
                'user_id' => null,
                'referer_url' => "https://google.com/search?q=seo",
                'source' => 'google',
                'keyword' => 'seo',
                'landing_page' => $faker->randomElement($landingPages),
                'ip_address' => $faker->ipv4,
                'visit_count' => $faker->numberBetween(1, 15),
                'visited_at' => $visitedAt,
                'created_at' => $visitedAt,
                'updated_at' => $visitedAt,
            ]);
        }

        // Daten für 2023 (zum Testen von Jahresfilter)
        for ($i = 0; $i < 500; $i++) {
            $visitedAt = Carbon::create(2023, $faker->numberBetween(1, 12), $faker->numberBetween(1, 28));
            $source = $faker->randomElement($sources);
            ModReferralLog::create([
                'user_id' => null,
                'referer_url' => $source === 'direct' ? null : "https://{$source}.com/search?q=" . $faker->word,
                'source' => $source,
                'keyword' => $faker->randomElement(['webdesign', 'marketing', null]),
                'landing_page' => $faker->randomElement($landingPages),
                'ip_address' => $faker->ipv4,
                'visit_count' => $faker->numberBetween(1, 10),
                'visited_at' => $visitedAt,
                'created_at' => $visitedAt,
                'updated_at' => $visitedAt,
            ]);
        }

        // Daten für die letzten 7 Tage (zum Testen von 7d-Filter)
        for ($i = 0; $i < 100; $i++) {
            $visitedAt = Carbon::now()->subDays($faker->numberBetween(0, 6));
            ModReferralLog::create([
                'user_id' => null,
                'referer_url' => "https://facebook.com/ads",
                'source' => 'facebook',
                'keyword' => null,
                'landing_page' => '/products',
                'ip_address' => $faker->ipv4,
                'visit_count' => $faker->numberBetween(1, 5),
                'visited_at' => $visitedAt,
                'created_at' => $visitedAt,
                'updated_at' => $visitedAt,
            ]);
        }
    }
}
