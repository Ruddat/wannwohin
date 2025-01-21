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
        Schema::create('mod_daily_climate_averages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id'); // Fremdschlüssel zur Location
            $table->date('date'); // Datum des Durchschnitts
            $table->float('avg_daily_temperature')->nullable(); // Durchschnittliche Tageshöchsttemperatur
            $table->float('avg_night_temperature')->nullable(); // Durchschnittliche Nachttiefsttemperatur
            $table->float('avg_sunshine_per_day')->nullable(); // Durchschnittliche Sonnenstunden pro Tag
            $table->float('avg_humidity')->nullable(); // Durchschnittliche Luftfeuchtigkeit
            $table->integer('total_rainy_days')->nullable(); // Anzahl der Regentage
            $table->float('avg_water_temperature')->nullable(); // Durchschnittliche Wassertemperatur
            $table->timestamps();

            // Fremdschlüsseldefinition
            $table->foreign('location_id')
                  ->references('id')
                  ->on('wwde_locations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_daily_climate_averages');
    }
};
