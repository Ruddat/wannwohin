<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wwde_climates', function (Blueprint $table) {

            // Beste Reisezeit Index (0–10)
            $table->unsignedTinyInteger('travel_index')->nullable()->after('water_temperature');

            // Regenwahrscheinlichkeit (%) – float
            $table->float('rain_probability', 5, 1)->nullable()->after('travel_index');

            // UV-Index – float
            $table->float('uv_index', 4, 1)->nullable()->after('rain_probability');

            // Komfortwert (1–100)
            $table->unsignedTinyInteger('comfort_score')->nullable()->after('uv_index');

            // Ø Windgeschwindigkeit
            $table->float('wind_speed_avg', 4, 1)->nullable()->after('comfort_score');
        });
    }

    public function down(): void
    {
        Schema::table('wwde_climates', function (Blueprint $table) {
            $table->dropColumn([
                'travel_index',
                'rain_probability',
                'uv_index',
                'comfort_score',
                'wind_speed_avg',
            ]);
        });
    }
};
