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
        Schema::create('climate_monthly_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->integer('month'); // 1 for January, 12 for December
            $table->string('month_name');
            $table->integer('year');
            $table->float('temperature_avg')->nullable();
            $table->float('temperature_max')->nullable();
            $table->float('temperature_min')->nullable();
            $table->float('precipitation')->nullable();
            $table->float('snowfall')->nullable();
            $table->float('sunshine_hours')->nullable();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('wwde_locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climate_monthly_data');
    }
};
