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
        Schema::create('mod_currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3);
            $table->string('target_currency', 3);
            $table->decimal('exchange_rate', 15, 6);
            $table->timestamp('last_updated')->useCurrent();
            $table->unique(['base_currency', 'target_currency']); // Verhindert doppelte Einträge
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_currency_exchange_rates');
    }
};
