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
        Schema::create('wwde_continents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('alias', 120);
            $table->string('iso2', 2)->nullable(); // Neue Spalte für ISO2
            $table->string('iso3', 3)->nullable(); // Neue Spalte für ISO3
            $table->integer('area_km')->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->integer('no_countries')->nullable();
            $table->integer('no_climate_tables')->nullable();
            $table->tinyText('continent_header_text')->nullable();
            $table->tinyText('continent_text')->nullable();

            // New columns for images and flag
            $table->string('image1_path')->nullable();
            $table->string('image2_path')->nullable();
            $table->string('image3_path')->nullable();
            $table->boolean('custom_images')->default(false);

            // Status field
            $table->enum('status', ['active', 'pending', 'inactive'])->default('active');

            $table->timestamps();

            // Indexes
            $table->index('alias', 'continent_alias');
            $table->index('title', 'continent_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_continents');
    }
};
