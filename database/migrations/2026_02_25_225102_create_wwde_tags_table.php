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
        Schema::create('wwde_tags', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50);      // sport|erlebnisse|natur|parks|urlaubstyp
            $table->string('slug', 120);
            $table->string('title', 160);
            $table->timestamps();

            $table->unique(['group', 'slug']);   // eindeutig je Gruppe
            $table->index(['group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwde_tags');
    }
};
