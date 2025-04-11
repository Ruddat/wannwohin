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
        Schema::create('mod_trips', function (Blueprint $table) {
            $table->id();

            // Optionaler Bezug zu eingeloggtem User
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            // Für Gruppierung und Vorschau
            $table->string('main_location')->nullable(); // z. B. "Berlin"
            $table->json('days'); // strukturierte Planung
            $table->boolean('use_days')->default(false); // Tagesplan oder Liste?

            // Public-Features
            $table->boolean('is_public')->default(false); // Vorschläge öffentlich anzeigen
            $table->unsignedInteger('views')->default(0); // wie oft angesehen
            $table->unsignedInteger('clicks')->default(0); // wie oft geklickt/geladen

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_trips');
    }
};
