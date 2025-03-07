<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Füge zusätzliche Wartungsmodus-Einstellungen hinzu
        DB::table('mod_site_settings')->insert([
            [
                'key' => 'maintenance_message',
                'value' => 'Die Seite befindet sich derzeit im Wartungsmodus. Wir sind bald wieder da!',
                'type' => 'string',
                'description' => 'Nachricht für den Wartungsmodus',
                'group' => 'general',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_start_at',
                'value' => null,
                'type' => 'string',
                'description' => 'Startzeit des Wartungsmodus (ISO 8601)',
                'group' => 'general',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_end_at',
                'value' => null,
                'type' => 'string',
                'description' => 'Endzeit des Wartungsmodus (ISO 8601)',
                'group' => 'general',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_allowed_ips',
                'value' => json_encode(['127.0.0.1']),
                'type' => 'json',
                'description' => 'Erlaubte IPs während des Wartungsmodus',
                'group' => 'general',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        // Entferne die hinzugefügten Einstellungen bei Rollback
        DB::table('mod_site_settings')->whereIn('key', [
            'maintenance_message',
            'maintenance_start_at',
            'maintenance_end_at',
            'maintenance_allowed_ips',
        ])->delete();
    }
};
