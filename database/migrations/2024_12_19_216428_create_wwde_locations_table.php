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
        Schema::create('wwde_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('continent_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('title', 50)->nullable();
            $table->string('alias', 50)->nullable();
            $table->string('iata_code', 30)->nullable();
            $table->string('currency_code', 30)->nullable(); // 'currency_code'
            $table->float('flight_hours')->nullable();
            $table->integer('stop_over')->nullable();
            $table->integer('dist_from_FRA')->nullable();
            $table->string('dist_type', 50)->nullable();
            $table->string('lat', 50)->nullable();
            $table->string('lon', 50)->nullable();
            $table->string('bundesstaat_long', 50)->nullable();
            $table->string('bundesstaat_short', 50)->nullable();
            $table->char('no_city_but', 50)->nullable();
            $table->integer('population')->nullable(); // 'population'


            // Boolean flags
            $table->boolean('list_beach')->default(false);
            $table->boolean('list_citytravel')->default(false);
            $table->boolean('list_sports')->default(false);
            $table->boolean('list_island')->default(false);
            $table->boolean('list_culture')->default(false);
            $table->boolean('list_nature')->default(false);
            $table->boolean('list_watersport')->default(false);
            $table->boolean('list_wintersport')->default(false);
            $table->boolean('list_mountainsport')->default(false);
            $table->boolean('list_biking')->default(false);
            $table->boolean('list_fishing')->default(false);
            $table->boolean('list_amusement_park')->default(false);
            $table->boolean('list_water_park')->default(false);
            $table->boolean('list_animal_park')->default(false);

            // Erhöhte Länge für 'best_traveltime'
            $table->string('best_traveltime', 1000)->nullable();
            $table->string('pic1_text', 300)->nullable();
            $table->string('pic2_text', 300)->nullable();
            $table->string('pic3_text', 300)->nullable();
            $table->string('text_headline', 255)->nullable();
            $table->string('text_short', 1000)->nullable();

            // Long texts
            $table->text('text_location_climate')->nullable();
            $table->text('text_what_to_do')->nullable();
            $table->text('text_best_traveltime')->nullable();
            $table->text('text_sports')->nullable();
            $table->text('text_amusement_parks')->nullable();

            $table->unsignedBigInteger('climate_details_id')->nullable();
            $table->char('climate_lnam', 50)->nullable();
            $table->char('climate_details_lnam', 50)->nullable();

            // Pricing and ranges
            $table->integer('price_flight')->nullable();
            $table->integer('range_flight')->nullable();
            $table->integer('price_hotel')->nullable();
            $table->integer('range_hotel')->nullable();
            $table->integer('price_rental')->nullable();
            $table->integer('range_rental')->nullable();
            $table->integer('price_travel')->nullable();
            $table->integer('range_travel')->nullable();

            $table->boolean('finished')->default(false);
            $table->json('best_traveltime_json')->nullable();
            $table->text('panorama_text_and_style')->nullable();

            $table->string('time_zone', 255)->nullable();
            $table->string('lat_new', 255)->nullable();
            $table->string('lon_new', 255)->nullable();
            // Bilder locations
            $table->string('text_pic1', 300)->nullable();
            $table->string('text_pic2', 300)->nullable();
            $table->string('text_pic3', 300)->nullable();


            $table->unique(['title', 'country_id']);

            // Indexes
            $table->foreign('continent_id')->references('id')->on('wwde_continents')->nullOnDelete();
            $table->foreign('country_id')->references('id')->on('wwde_countries')->nullOnDelete();


            // Status field
            $table->enum('status', ['active', 'pending', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_locations');
    }
};
