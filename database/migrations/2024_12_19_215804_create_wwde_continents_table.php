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
            $table->integer('area_km')->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->integer('no_countries')->nullable();
            $table->integer('no_climate_tables')->nullable();
            $table->tinyText('continent_text')->nullable();
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
