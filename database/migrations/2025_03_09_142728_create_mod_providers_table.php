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
        Schema::create('mod_providers', function (Blueprint $table) {
            $table->id(); // Primärschlüssel
            $table->string('name')->unique(); // Name des Anbieters, eindeutig
            $table->string('email')->nullable(); // Kontakt-E-Mail
            $table->string('phone')->nullable(); // Kontakt-Telefonnummer
            $table->string('website')->nullable(); // Website des Anbieters
            $table->text('description')->nullable(); // Beschreibung des Anbieters
            $table->string('contact_person')->nullable(); // Ansprechpartner
            $table->boolean('is_active')->default(true); // Status des Anbieters
            $table->timestamps(); // created_at, updated_at
        });


        App\Models\ModProviders::create([
            'name' => 'Check24',
            'email' => 'check@check24.com',
            'phone' => null,
            'website' => 'https://www.check24.net',
            'description' => 'Vergleichsportal für Reisen, Versicherungen und mehr.',
            'contact_person' => null,
            'is_active' => true,
        ]);

        App\Models\ModProviders::create([
            'name' => 'Kiwi.com',
            'email' => 'support@kiwi.com',
            'phone' => null,
            'website' => 'https://tequila.kiwi.com/',
            'description' => 'Plattform für günstige Flug- und Reisebuchungen.',
            'contact_person' => null,
            'is_active' => true,
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_providers');
    }
};
