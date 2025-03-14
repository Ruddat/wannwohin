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
        Schema::create('mod_visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id'); // Eindeutige Session-ID
            $table->string('ip_address'); // IP-Adresse des Besuchers
            $table->string('page_url'); // Aktuelle Seite
            $table->unsignedInteger('dwell_time')->default(0); // Verweildauer in Sekunden
            $table->timestamp('last_activity_at')->useCurrent(); // Letzte Aktivität
            $table->timestamp('started_at')->useCurrent(); // Sitzungsstart
            $table->index('session_id'); // Für schnellere Abfragen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_visitor_sessions');
    }
};
