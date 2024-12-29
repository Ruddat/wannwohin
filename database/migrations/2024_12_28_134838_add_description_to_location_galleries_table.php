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
            $table->string('description')->nullable()->after('image_path');
            $table->string('image_hash')->unique()->after('image_path');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_galleries', function (Blueprint $table) {
            //
            $table->dropColumn('description');
            $table->dropColumn('image_hash');
        });
    }
};
