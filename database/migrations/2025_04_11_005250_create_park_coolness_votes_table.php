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
        Schema::create('park_coolness_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')
            ->constrained('amusement_parks') // <-- auch hier
            ->onDelete('cascade');
            $table->unsignedTinyInteger('value');
            $table->ipAddress('ip_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_coolness_votes');
    }
};
