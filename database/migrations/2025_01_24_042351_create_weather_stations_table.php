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
        Schema::create('weather_stations', function (Blueprint $table) {
            $table->id();
            $table->string('station_id')->unique();
            $table->string('name')->nullable();
            $table->string('country', 2);
            $table->string('region')->nullable();
            $table->float('latitude');
            $table->float('longitude');
            $table->integer('elevation')->nullable();
            $table->string('timezone')->nullable();
            $table->json('inventory')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_stations');
    }
};
