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
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->string('continent')->nullable()->after('country'); // Neues Feld für Kontinent
            $table->string('timezone')->nullable()->after('longitude'); // Neues Feld für Zeitzone
            $table->string('group_name')->nullable()->after('name'); // Name der Parkgruppe (z. B. "Cedar Fair")
            $table->integer('group_id')->nullable()->after('external_id'); // ID der Parkgruppe
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->dropColumn(['continent', 'timezone', 'group_name', 'group_id']);
        
        });
    }
};
