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
        Schema::table('location_galleries', function (Blueprint $table) {
            $table->string('activity', 100)->nullable()->after('image_hash')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_galleries', function (Blueprint $table) {
            $table->dropColumn('activity');
        });
    }
};
