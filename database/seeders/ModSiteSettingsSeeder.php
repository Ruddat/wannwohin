<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModSiteSettings;

class ModSiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Allgemeine Einstellungen
            [
                'key' => 'site_name',
                'value' => 'WannWohin.de',
                'type' => 'string',
                'description' => 'Name der Website',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'logo',
                'value' => '/storage/uploads/logo.png',
                'type' => 'file',
                'description' => 'Pfad zum Website-Logo',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'owner_name',
                'value' => 'Max Mustermann',
                'type' => 'string',
                'description' => 'Name des Inhabers',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@wannwohin.de',
                'type' => 'string',
                'description' => 'Kontakt-E-Mail-Adresse',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Wartungsmodus aktivieren/deaktivieren',
                'group' => 'general',
                'is_public' => false,
            ],

            // Social Media
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/wannwohin',
                'type' => 'string',
                'description' => 'URL zum Facebook-Profil',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'twitter_handle',
                'value' => '@WannWohin',
                'type' => 'string',
                'description' => 'Twitter-Handle',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/wannwohin',
                'type' => 'string',
                'description' => 'URL zum Instagram-Profil',
                'group' => 'social',
                'is_public' => true,
            ],

            // SEO
            [
                'key' => 'default_meta_keywords',
                'value' => json_encode(['reisen', 'urlaub', 'wetter']),
                'type' => 'json',
                'description' => 'Standard-SEO-Keywords',
                'group' => 'seo',
                'is_public' => false,
            ],
            [
                'key' => 'google_analytics_id',
                'value' => 'UA-12345678-1',
                'type' => 'string',
                'description' => 'Google Analytics Tracking-ID',
                'group' => 'seo',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            ModSiteSettings::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
