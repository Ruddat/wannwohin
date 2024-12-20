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
        Schema::create('wwde_climates', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT PRIMARY KEY
            $table->unsignedSmallInteger('location_id')->nullable();
            $table->unsignedTinyInteger('month_id')->nullable();
            $table->string('month', 120)->nullable();
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
        Schema::dropIfExists('wwde_climates');
    }
};
