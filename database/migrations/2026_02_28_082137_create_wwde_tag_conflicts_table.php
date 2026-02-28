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
        Schema::create('wwde_tag_conflicts', function (Blueprint $table) {
    $table->id();
    $table->string('raw_category');
    $table->string('suggested_slug')->nullable();
    $table->boolean('resolved')->default(false);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_tag_conflicts');
    }
};
