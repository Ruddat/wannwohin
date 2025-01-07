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
        Schema::create('stat_top_ten_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id'); // Referenz zu wwde_locations
            $table->integer('search_count')->default(0); // Anzahl der Suchanfragen
            $table->timestamps(); // Zeitstempel

            // Fremdschlüssel-Definition
            $table->foreign('location_id')->references('id')->on('wwde_locations')->onDelete('cascade');

            // Indizes
            $table->index(['location_id', 'search_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stat_top_ten_locations');
    }
};
