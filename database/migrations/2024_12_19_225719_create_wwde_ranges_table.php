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
        Schema::create('wwde_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('Sort')->nullable();
            $table->string('Range_to_show', 50)->nullable();
            $table->string('Type', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_ranges');
    }
};
