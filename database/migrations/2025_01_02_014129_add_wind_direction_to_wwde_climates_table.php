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
        Schema::table('wwde_climates', function (Blueprint $table) {
            $table->integer('cloudiness')->nullable()->after('humidity'); // Cloudiness hinzufügen
            $table->integer('wind_direction')->nullable()->after('wind_speed'); // Windrichtung hinzufügen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_climates', function (Blueprint $table) {
            $table->dropColumn('cloudiness');
            $table->dropColumn('wind_direction');
        });
    }
};
