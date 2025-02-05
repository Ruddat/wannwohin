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

            // Falls `location_id` bereits ein Integer ist, stellen wir sicher, dass es `unsignedBigInteger` ist.
            $table->unsignedBigInteger('location_id')->change();

            // Füge den Fremdschlüssel hinzu
          $table->foreign('location_id')
          ->references('id')
          ->on('wwde_locations')
          ->onDelete('cascade'); // Optional: Löscht alle Einträge, wenn die Location gelöscht wird.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_location_filters', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });
    }
};
