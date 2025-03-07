<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mod_site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->unique(); // Eindeutiger Schlüssel
            $table->text('value')->nullable(); // Wert der Einstellung
            $table->enum('type', ['string', 'file', 'json', 'boolean'])->default('string'); // Typ der Einstellung
            $table->text('description')->nullable(); // Beschreibung für Admins
            $table->string('group')->default('general'); // Gruppierung (z. B. "general", "social", "seo")
            $table->boolean('is_public')->default(true); // Öffentlich oder intern?
            $table->timestamps();
        });

        // Standardwerte direkt in die Datenbank einfügen
        DB::table('mod_site_settings')->insert([
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
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_site_settings');
    }
};
