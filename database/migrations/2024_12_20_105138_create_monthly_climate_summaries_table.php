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
        Schema::create('monthly_climate_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->unsignedTinyInteger('month_id');
            $table->float('avg_daily_temperature')->nullable();
            $table->float('avg_night_temperature')->nullable();
            $table->float('avg_sunshine_per_day')->nullable();
            $table->float('avg_humidity')->nullable();
            $table->integer('total_rainy_days')->nullable();
            $table->float('avg_water_temperature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_climate_summaries');
    }
};
