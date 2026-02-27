<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('park_tag', function (Blueprint $table) {
            $table->id();

            $table->foreignId('park_id')
                ->constrained('amusement_parks')
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->constrained('wwde_tags')
                ->cascadeOnDelete();

            $table->unique(['park_id', 'tag_id']); // verhindert Duplikate
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('park_tag');
    }
};
