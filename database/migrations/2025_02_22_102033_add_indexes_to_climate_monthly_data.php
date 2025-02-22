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
        Schema::table('climate_monthly_data', function (Blueprint $table) {
            // Kombinierter Index
            $table->index(['location_id', 'year', 'month'], 'idx_location_year_month');

            // Einzelne Indizes
            $table->index('year', 'idx_year');
            $table->index('temperature_avg', 'idx_temperature_avg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('climate_monthly_data', function (Blueprint $table) {
            // Indizes entfernen
            $table->dropIndex('idx_location_year_month');
            $table->dropIndex('idx_year');
            $table->dropIndex('idx_temperature_avg');
        });
    }
};
