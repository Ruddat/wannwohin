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
            $table->double('temperature_avg')->nullable();
            $table->double('temperature_max')->nullable();
            $table->double('temperature_min')->nullable();
            $table->double('precipitation')->nullable();
            $table->double('snowfall')->nullable();
            $table->double('sunshine_hours')->nullable();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('wwde_locations')->onDelete('cascade');
            $table->unique(['location_id', 'year', 'month']);
            $table->index(['location_id', 'year', 'month']);
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
