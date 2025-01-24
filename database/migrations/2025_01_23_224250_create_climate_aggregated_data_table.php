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
        Schema::create('climate_aggregated_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->unsignedTinyInteger('month');
            $table->float('avg_daily_temp')->nullable();
            $table->float('min_temp')->nullable();
            $table->float('max_temp')->nullable();
            $table->float('avg_humidity')->nullable();
            $table->float('sunshine_hours')->nullable();
            $table->float('total_rainy_days')->nullable();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('wwde_locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climate_aggregated_data');
    }
};
