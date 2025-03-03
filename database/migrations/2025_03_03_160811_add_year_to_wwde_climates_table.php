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
            if (!Schema::hasColumn('wwde_climates', 'year')) {
                $table->unsignedSmallInteger('year')->nullable()->after('month_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_climates', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
};
