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
            $table->string('type')->nullable()->after('name'); // Fügt das Feld "type" nach "name" hinzu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->dropColumn('type'); // Entfernt das Feld bei einem Rollback
        });
    }
};
