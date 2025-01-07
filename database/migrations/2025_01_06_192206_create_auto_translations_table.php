<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auto_translations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->unique(); // Eindeutiger, kurzer Schlüssel
            $table->longText('original_text'); // Der vollständige Originaltext
            $table->string('locale', 10)->index(); // Sprache der Übersetzung
            $table->longText('text'); // Übersetzter Text für sehr lange Inhalte
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_translations');
    }
}
