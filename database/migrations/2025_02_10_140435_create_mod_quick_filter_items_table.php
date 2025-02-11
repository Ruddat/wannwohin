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
        Schema::create('mod_quick_filter_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            // new additional text fields
            $table->string('title_text')->nullable();
            $table->text('content')->nullable();
            // images
            $table->string('thumbnail')->nullable();
            $table->string('panorama')->nullable();
            $table->string('image')->nullable();
            // store multiple months in JSON
            $table->json('filter_months')->nullable();
            // remove year column
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_quick_filter_items');
    }
};
