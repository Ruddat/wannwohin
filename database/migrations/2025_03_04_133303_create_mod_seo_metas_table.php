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
        Schema::create('mod_seo_metas', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // Model-Typ (Location, Country, Continent, CustomPage)
            $table->unsignedBigInteger('model_id'); // ID des Models
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical')->nullable();
            $table->string('image')->nullable();
            $table->json('extra_meta')->nullable(); // Für OpenGraph, Twitter, Structured Data
            $table->text('keywords')->nullable(); // Text für eine Liste von Keywords
            $table->boolean('prevent_override')->default(false); // Verhindert das Überschreiben von SEO-Daten
            $table->timestamps();

            $table->index(['model_type', 'model_id']); // Performance-Optimierung
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_seo_metas');
    }
};
