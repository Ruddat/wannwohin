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
        Schema::table('mod_location_filters', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('addinfo');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_location_filters', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
