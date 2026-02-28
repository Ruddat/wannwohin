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
        Schema::table('wwde_tags', function (Blueprint $table) {
    $table->string('normalized')->index()->after('slug');
    $table->unsignedInteger('usage_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwde_tags', function (Blueprint $table) {
    $table->dropColumn('normalized');
    $table->dropColumn('usage_count');
        });
    }
};
