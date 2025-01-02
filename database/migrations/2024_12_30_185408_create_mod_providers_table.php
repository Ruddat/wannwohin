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
        Schema::create('mod_providers', function (Blueprint $table) {
            $table->id(); // Primärschlüssel
            $table->string('name'); // Name des Anbieters
            $table->string('email')->nullable(); // Optional: Kontakt-E-Mail
            $table->string('phone')->nullable(); // Optional: Kontakt-Telefon
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_providers');
    }
};
