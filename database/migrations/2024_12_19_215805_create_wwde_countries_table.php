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
        Schema::create('wwde_countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('continent_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('alias', 255)->nullable();
            $table->string('currency_code', 255)->nullable();
            $table->char('currency_name', 50)->nullable();
            $table->string('country_code', 3);
            $table->string('country_text', 500)->nullable();
            $table->string('currency_conversion', 255)->nullable();
            $table->integer('population')->nullable();
            $table->string('capital', 255)->nullable();
            $table->integer('population_capital')->nullable();
            $table->integer('area')->nullable();
            $table->string('official_language', 255)->nullable();
            $table->string('language_ezmz', 255)->nullable();
            $table->integer('bsp_in_USD')->nullable();
            $table->float('life_expectancy_m')->nullable();
            $table->float('life_expectancy_w')->nullable();
            $table->float('population_density')->nullable();
            $table->char('country_iso_3', 3)->nullable();
            $table->char('continent_iso_2', 2)->nullable();
            $table->char('continent_iso_3', 3)->nullable();
            $table->boolean('country_visum_needed')->nullable();
            $table->char('country_visum_max_time', 50)->nullable();
            $table->tinyInteger('count_climatezones')->nullable();
            $table->char('climatezones_ids', 50)->nullable();
            $table->char('climatezones_lnam', 255)->nullable();
            $table->char('climatezones_details_lnam', 255)->nullable();
            $table->char('artikel', 50)->nullable();
            $table->integer('travelwarning_id')->nullable();
            $table->char('price_tendency', 10)->nullable();

            // New columns for images and flag
            $table->string('image1_path')->nullable();
            $table->string('image2_path')->nullable();
            $table->string('image3_path')->nullable();
            $table->boolean('custom_images')->default(false);

            // Status field
            $table->enum('status', ['active', 'pending', 'inactive'])->default('active');

            $table->timestamps();

            // Indexes
            $table->foreign('continent_id')->references('id')->on('wwde_continents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_countries');
    }
};
