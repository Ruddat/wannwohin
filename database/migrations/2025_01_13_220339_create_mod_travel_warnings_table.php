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
        Schema::create('mod_travel_warnings', function (Blueprint $table) {
            $table->id();
            $table->string('country')->unique(); // Eindeutige Ländereinträge
            $table->string('iso2', 2)->nullable(); // ISO2
            $table->string('iso3', 3)->nullable(); // ISO3
            $table->string('severity')->nullable()->default(null); // Warnstufe kann leer sein
            $table->timestamp('issued_at')->nullable(); // Datum der Warnung
            $table->timestamps(); // Für created_at und updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_travel_warnings');
    }
};
