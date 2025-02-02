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
            $table->longText('pic1_text')->nullable()->change();
            $table->longText('pic2_text')->nullable()->change();
            $table->longText('pic3_text')->nullable()->change();
            $table->longText('text_headline')->nullable()->change();
            $table->longText('text_short')->nullable()->change();
            $table->longText('text_location_climate')->nullable()->change();
            $table->longText('text_what_to_do')->nullable()->change();
            $table->longText('text_sports')->nullable()->change();
            $table->longText('text_amusement_parks')->nullable()->change();
            $table->longText('text_best_traveltime')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->text('pic1_text')->nullable()->change();
            $table->text('pic2_text')->nullable()->change();
            $table->text('pic3_text')->nullable()->change();
            $table->text('text_headline')->nullable()->change();
            $table->text('text_short')->nullable()->change();
            $table->text('text_location_climate')->nullable()->change();
            $table->text('text_what_to_do')->nullable()->change();
            $table->text('text_sports')->nullable()->change();
            $table->text('text_amusement_parks')->nullable()->change();
            $table->text('text_best_traveltime')->nullable()->change();
        });
    }
};
