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
            // Feld "Kategorie" hinzufügen, direkt nach "text_type"
            $table->string('category')->nullable()->after('text_type');
            // Feld "addinfo" hinzufügen, direkt nach "text", optional (nullable)
            $table->text('addinfo')->nullable()->after('text');
            // Feld "anzeigen" hinzufügen, direkt nach "addinfo" mit Standardwert true
            $table->boolean('is_active')->default(true)->after('addinfo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_location_filters', function (Blueprint $table) {
            $table->dropColumn(['category', 'addinfo', 'is_active']);
        });
    }
};
