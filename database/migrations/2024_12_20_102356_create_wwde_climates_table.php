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
            $table->unsignedBigInteger('location_id')->nullable(); // Fremdschlüssel zur Location-Tabelle
            $table->unsignedTinyInteger('month_id')->nullable(); // Monat als ID (1-12)
            $table->string('month', 120)->nullable(); // Monatsname
            $table->float('daily_temperature')->nullable(); // Tageshöchsttemperatur
            $table->float('night_temperature')->nullable(); // Nachttiefsttemperatur
            $table->float('sunshine_per_day')->nullable(); // Sonnenstunden pro Tag
            $table->float('humidity')->nullable(); // Luftfeuchtigkeit
            $table->float('rainy_days')->nullable(); // Regentage
            $table->float('water_temperature')->nullable(); // Wassertemperatur
            $table->string('icon', 255)->nullable(); // Wetter-Icon
            $table->unsignedBigInteger('weather_id')->nullable(); // Wetter-ID aus der API
            $table->float('feels_like')->nullable(); // Gefühlte Temperatur
            $table->float('temp_min')->nullable(); // Minimale Temperatur
            $table->float('temp_max')->nullable(); // Maximale Temperatur
            $table->integer('pressure')->nullable(); // Luftdruck
            $table->integer('sea_level')->nullable(); // Luftdruck auf Meereshöhe
            $table->integer('grnd_level')->nullable(); // Luftdruck auf Bodenniveau
            $table->integer('visibility')->nullable(); // Sichtweite
            $table->float('wind_speed')->nullable(); // Windgeschwindigkeit
            $table->integer('wind_deg')->nullable(); // Windrichtung in Grad
            $table->integer('clouds_all')->nullable(); // Bewölkung in Prozent
            $table->bigInteger('dt')->nullable(); // Zeitstempel der Datenerhebung
            $table->integer('timezone')->nullable(); // Zeitzone
            $table->string('country', 2)->nullable(); // Ländercode
            $table->bigInteger('sunrise')->nullable(); // Sonnenaufgangszeit
            $table->bigInteger('sunset')->nullable(); // Sonnenuntergangszeit
            $table->string('weather_main', 255)->nullable(); // Hauptwetterbeschreibung (z. B. "Clear")
            $table->string('weather_description', 255)->nullable(); // Detaillierte Wetterbeschreibung
            $table->timestamps(); // created_at und updated_at

            // Fremdschlüsseldefinition
            $table->foreign('location_id')
                  ->references('id')
                  ->on('wwde_locations')
                  ->onDelete('cascade'); // Löscht Klimadaten, wenn eine Location gelöscht wird
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
