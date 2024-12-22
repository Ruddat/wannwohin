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
        Schema::create('header_contents', function (Blueprint $table) {
            $table->id();
            $table->string('bg_img'); // Hintergrundbild
            $table->string('main_img'); // Hauptbild
            $table->text('main_text'); // Haupttext
            $table->string('title')->nullable(); // Optionale Überschrift
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_contents');
    }
};
