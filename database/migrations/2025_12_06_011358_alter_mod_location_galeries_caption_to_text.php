<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mod_location_galeries', function (Blueprint $table) {
            // Schritt 1: Spalte auf NULL erlauben
            $table->text('image_caption')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('mod_location_galeries', function (Blueprint $table) {
            // Rückgängig machen
            $table->string('image_caption', 255)->nullable()->change();
        });
    }
};
