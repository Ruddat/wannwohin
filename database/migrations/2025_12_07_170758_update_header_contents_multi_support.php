<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('header_contents', function (Blueprint $table) {

        // Indexliste der Tabelle abrufen
        $indexes = collect(DB::select("SHOW INDEX FROM header_contents"))->pluck('Key_name');

        // Falls ein Unique-Index auf 'slug' existiert → entfernen
        if ($indexes->contains('header_contents_slug_unique')) {
            $table->dropUnique('header_contents_slug_unique');
        }
        if ($indexes->contains('slug')) {
            // Manche Systeme nennen den Key einfach 'slug'
            $table->dropUnique('slug');
        }

        // sort_order hinzufügen, falls nicht vorhanden
        if (!Schema::hasColumn('header_contents', 'sort_order')) {
            $table->integer('sort_order')->default(1)->after('slug');
        }
    });
}

    public function down(): void
    {
        Schema::table('header_contents', function (Blueprint $table) {
            if (Schema::hasColumn('header_contents', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            // Unique wiederherstellen (falls nötig)
            $table->unique('slug');
        });
    }
};
