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
        Schema::create('mod_static_pages', function (Blueprint $table) {
            $table->string('slug')->primary(); // z. B. 'impressum'
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });

        // Beispiel-Daten einfügen
        \App\Models\ModStaticPage::insert([
            ['slug' => 'impressum', 'title' => 'Impressum', 'body' => '<p>Wann-Wohin GmbH<br> Musterstraße 1<br> 12345 Musterstadt</p>'],
            ['slug' => 'kontakt', 'title' => 'Kontakt', 'body' => '<p>E-Mail: support@wann-wohin.de<br> Telefon: +49 123 456789</p>'],
            ['slug' => 'agb', 'title' => 'AGB', 'body' => '<p>Unsere AGB...</p>'],
            ['slug' => 'datenschutz', 'title' => 'Datenschutz', 'body' => '<p>Datenschutzrichtlinien...</p>'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_static_pages');
    }
};
