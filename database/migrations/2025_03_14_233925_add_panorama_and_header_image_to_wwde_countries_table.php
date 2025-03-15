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
        Schema::table('wwde_countries', function (Blueprint $table) {
            $table->string('panorama_image_path')->nullable()->after('image3_path');
            $table->string('header_image_path')->nullable()->after('panorama_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_countries', function (Blueprint $table) {
            $table->dropColumn(['panorama_image_path', 'header_image_path']);
        });
    }
};
