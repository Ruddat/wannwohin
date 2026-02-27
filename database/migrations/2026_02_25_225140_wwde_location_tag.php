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
        Schema::create('wwde_location_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->primary(['location_id', 'tag_id']);

            $table->foreign('location_id')
                ->references('id')
                ->on('wwde_locations')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('wwde_tags')
                ->onDelete('cascade');

            $table->index(['tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wwde_location_tag');
    }
};
