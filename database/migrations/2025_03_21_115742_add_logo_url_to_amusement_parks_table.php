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
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->string('logo_url', 255)->nullable()->default(null)->collation('utf8mb4_unicode_ci')->after('video_url');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->dropColumn('logo_url');
        });
    }
};
