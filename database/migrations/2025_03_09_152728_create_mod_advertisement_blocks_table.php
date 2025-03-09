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
        Schema::create('mod_advertisement_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('link')->nullable();
            $table->string('type')->default('banner'); // 'banner', 'widget', 'script'
            $table->text('script')->nullable();
            $table->string('position')->nullable(); // z. B. 'sidebar', 'header', 'footer'
            $table->foreignId('provider_id')->constrained('mod_providers')->onDelete('cascade'); // Fremdschlüssel
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_advertisement_blocks');
    }
};
