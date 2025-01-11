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
        Schema::create('mod_location_galeries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id'); // Beziehung zu Locations
            $table->string('location_name')->nullable(); // Optionaler Standortname
            $table->string('image_path'); // Bildpfad
            $table->string('image_caption', 255)->nullable(); // Bildbeschreibung
            $table->string('activity', 100)->nullable(); // Aktivität (z. B. "Natur", "Kultur")
            $table->string('description')->nullable(); // Zusätzliche Beschreibung
            $table->string('image_hash')->unique(); // Eindeutiger Hash für das Bild
            $table->enum('image_type', ['panorama', 'gallery', 'other'])->default('gallery'); // Bildtyp
            $table->boolean('is_primary')->default(false); // Primärbild

            $table->timestamps();

            // Indexe und Fremdschlüssel
            $table->foreign('location_id')->references('id')->on('wwde_locations')->onDelete('cascade');
            $table->index(['location_id', 'image_type']); // Index für häufige Abfragen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_location_galeries');
    }
};
