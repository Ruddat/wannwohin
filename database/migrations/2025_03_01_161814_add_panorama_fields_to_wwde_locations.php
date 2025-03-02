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
            $table->string('panorama_title', 255)->nullable()->after('panorama_text_and_style');
            $table->text('panorama_short_text')->nullable()->after('panorama_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->dropColumn(['panorama_title', 'panorama_short_text']);
        });
    }
};
