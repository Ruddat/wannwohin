<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            // Füge die neuen Spalten hinzu
            $table->string('iso2')->nullable()->after('country_id'); // ISO 3166-1 alpha-2 (z. B. DE)
            $table->string('iso3')->nullable()->after('iso2'); // ISO 3166-1 alpha-3 (z. B. DEU)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            // Entferne die Spalten beim Rollback
            $table->dropColumn('iso2');
            $table->dropColumn('iso3');
        });
    }
};
