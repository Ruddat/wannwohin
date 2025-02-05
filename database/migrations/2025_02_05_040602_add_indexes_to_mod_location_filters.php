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
            $table->index('text_type'); // Index auf text_type für schnellere Filterung
            $table->index('uschrift');  // Index auf uschrift für schnellere Filterung
            $table->index('location_id'); // Index auf location_id für bessere Joins mit WwdeLocation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_location_filters', function (Blueprint $table) {
            $table->dropIndex(['text_type']);
            $table->dropIndex(['uschrift']);
            $table->dropIndex(['location_id']);
        });
    }
};
