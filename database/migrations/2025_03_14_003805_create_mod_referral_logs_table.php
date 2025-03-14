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
        Schema::create('mod_referral_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('referer_url')->nullable();
            $table->string('source')->nullable();
            $table->string('keyword')->nullable();
            $table->string('landing_page');
            $table->string('ip_address')->nullable();
            $table->unsignedInteger('visit_count')->default(1);
            $table->timestamp('visited_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_referral_logs');
    }
};
