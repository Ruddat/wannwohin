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
        Schema::table('mod_electric_standards', function (Blueprint $table) {
            $table->json('plug_images')->nullable()->after('info'); // JSON-Feld für Bild-URLs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_electric_standards', function (Blueprint $table) {
            //
        });
    }
};
