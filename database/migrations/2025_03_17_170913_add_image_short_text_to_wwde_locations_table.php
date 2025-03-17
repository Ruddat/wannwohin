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
            $table->text('image_short_text')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->dropColumn('image_short_text');
        });
    }
};
