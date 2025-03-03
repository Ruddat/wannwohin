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
        Schema::table('wwde_climates', function (Blueprint $table) {
            $table->string('timezone', 100)->nullable()->change(); // Ändere zu varchar(100) für Zeitzonen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_climates', function (Blueprint $table) {
            $table->integer('timezone')->nullable()->change(); // Rückänderung zu int, falls nötig
        });
    }
};
