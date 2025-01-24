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
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->string('station_id')->nullable()->after('lon'); // Füge `station_id` hinzu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->dropColumn('station_id');
        });
    }
};
