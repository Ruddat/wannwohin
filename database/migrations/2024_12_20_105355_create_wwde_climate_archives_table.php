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
        Schema::create('wwde_climate_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->unsignedTinyInteger('month_id');
            $table->string('month', 20);
            $table->float('daily_temperature')->nullable();
            $table->float('night_temperature')->nullable();
            $table->float('sunshine_per_day')->nullable();
            $table->float('humidity')->nullable();
            $table->float('rainy_days')->nullable();
            $table->float('water_temperature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_climate_archives');
    }
};
