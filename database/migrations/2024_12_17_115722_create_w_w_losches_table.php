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
        Schema::create('w_w_losches', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->string('Name', 255)->nullable(); // Name
            $table->integer('BSP')->nullable(); // BSP
            $table->integer('EW')->nullable(); // Einwohner
            $table->string('Preis', 50)->nullable(); // Preis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('w_w_losches');
    }
};
