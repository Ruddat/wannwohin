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
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->boolean('open_today')->nullable();
            $table->timestamp('open_from')->nullable();
            $table->timestamp('closed_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            //
        });
    }
};
