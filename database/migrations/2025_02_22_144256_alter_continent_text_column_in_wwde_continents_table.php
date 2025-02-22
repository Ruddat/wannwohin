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
        Schema::table('wwde_continents', function (Blueprint $table) {
            // Ändere den Typ der Spalte zu MEDIUMTEXT
            $table->mediumText('continent_text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_continents', function (Blueprint $table) {
            // Rückgängig machen auf tinyText
            $table->tinyText('continent_text')->nullable()->change();
        });
    }
};
