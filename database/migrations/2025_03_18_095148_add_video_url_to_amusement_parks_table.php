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
            $table->string('video_url')->nullable()->after('description'); // Neues Feld für die Video-URL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};
