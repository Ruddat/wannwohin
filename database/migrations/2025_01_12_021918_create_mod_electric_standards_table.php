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
        Schema::create('mod_electric_standards', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
            $table->string('country_code', 3)->nullable();
            $table->unsignedBigInteger('country_id')->nullable()->default(null);
            $table->string('power', 50)->nullable();
            $table->text('info')->nullable();
            $table->boolean('typ_a')->default(0);
            $table->boolean('typ_b')->default(0);
            $table->boolean('typ_c')->default(0);
            $table->boolean('typ_d')->default(0);
            $table->boolean('typ_e')->default(0);
            $table->boolean('typ_f')->default(0);
            $table->boolean('typ_g')->default(0);
            $table->boolean('typ_h')->default(0);
            $table->boolean('typ_i')->default(0);
            $table->boolean('typ_j')->default(0);
            $table->boolean('typ_k')->default(0);
            $table->boolean('typ_l')->default(0);
            $table->boolean('typ_m')->default(0);
            $table->boolean('typ_n')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_electric_standards');
    }
};
