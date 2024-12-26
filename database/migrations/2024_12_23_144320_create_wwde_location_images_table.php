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
        Schema::create('wwde_location_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id'); // Beziehung zu Locations
            $table->string('image_path'); // Bildpfad
            $table->string('image_caption', 255)->nullable(); // Bildbeschreibung
            $table->enum('image_type', ['panorama', 'gallery', 'other'])->default('gallery'); // Bildtyp
            $table->boolean('is_primary')->default(false); // Primärbild
            $table->timestamps();

            // Fremdschlüssel
            $table->foreign('location_id')->references('id')->on('wwde_locations')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_location_images');
    }
};
