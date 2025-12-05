<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            // UNIQUE(alias) hinzufügen, falls noch nicht vorhanden
            try {
                $table->unique('alias', 'wwde_locations_alias_unique');
            } catch (\Exception $e) {
                // Index existiert bereits → ignorieren
            }
        });
    }

    public function down(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            // UNIQUE(alias) entfernen, falls existiert
            try {
                $table->dropUnique('wwde_locations_alias_unique');
            } catch (\Exception $e) {}
        });
    }
};
