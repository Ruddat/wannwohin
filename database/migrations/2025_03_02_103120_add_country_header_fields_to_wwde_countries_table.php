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
            $table->string('country_headert_titel', 255)->nullable()->after('alias');
            $table->longText('country_header_text')->nullable()->after('country_headert_titel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_countries', function (Blueprint $table) {
            $table->dropColumn(['country_headert_titel', 'country_header_text']);
        });
    }
};
