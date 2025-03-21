<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NormalizeWwdeRangesTable extends Migration
{
    public function up()
    {
        // Temporäre Tabelle erstellen, um Daten zu sichern
        Schema::create('wwde_ranges_temp', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->string('range_to_show', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->timestamps();
        });

        // Daten aus der alten Tabelle in die temporäre Tabelle kopieren
        DB::statement('INSERT INTO wwde_ranges_temp (id, sort, range_to_show, type)
                       SELECT id, Sort, Range_to_show, Type FROM wwde_ranges');

        // Alte Tabelle löschen
        Schema::drop('wwde_ranges');

        // Neue Tabelle mit normalisierten Spaltennamen erstellen
        Schema::create('wwde_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->string('range_to_show', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->timestamps();
        });

        // Daten aus der temporären Tabelle zurückkopieren
        DB::statement('INSERT INTO wwde_ranges (id, sort, range_to_show, type, created_at, updated_at)
                       SELECT id, sort, range_to_show, type, NOW(), NOW() FROM wwde_ranges_temp');

        // Temporäre Tabelle löschen
        Schema::drop('wwde_ranges_temp');
    }

    public function down()
    {
        // Rückgängig machen: Temporäre Tabelle erstellen
        Schema::create('wwde_ranges_temp', function (Blueprint $table) {
            $table->id();
            $table->integer('Sort')->nullable();
            $table->string('Range_to_show', 50)->nullable();
            $table->string('Type', 50)->nullable();
        });

        // Daten in die temporäre Tabelle kopieren
        DB::statement('INSERT INTO wwde_ranges_temp (id, Sort, Range_to_show, Type)
                       SELECT id, sort, range_to_show, type FROM wwde_ranges');

        // Alte Tabelle löschen
        Schema::drop('wwde_ranges');

        // Ursprüngliche Tabelle wiederherstellen (ohne Timestamps)
        Schema::create('wwde_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('Sort')->nullable();
            $table->string('Range_to_show', 50)->nullable();
            $table->string('Type', 50)->nullable();
        });

        // Daten zurückkopieren
        DB::statement('INSERT INTO wwde_ranges (id, Sort, Range_to_show, Type)
                       SELECT id, Sort, Range_to_show, Type FROM wwde_ranges_temp');

        // Temporäre Tabelle löschen
        Schema::drop('wwde_ranges_temp');
    }
}
