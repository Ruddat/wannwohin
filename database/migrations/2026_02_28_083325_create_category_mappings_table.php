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
        Schema::create('wwde_category_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('raw_category')->unique();
            $table->unsignedBigInteger('tag_id'); // Referenz auf wwde_tags
            $table->timestamps();

            $table->foreign('tag_id')
                ->references('id')
                ->on('wwde_tags')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wwde_category_mappings');
    }
};
