<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amusement_parks', function (Blueprint $table) {

            // FK zu wwde_countries
            $table->foreignId('country_id')
                ->nullable()
                ->after('country')
                ->constrained('wwde_countries')
                ->nullOnDelete();

            // SEO-Slug
            $table->string('slug')
                ->nullable()
                ->unique()
                ->after('name');

            // Affiliate-Schalter
            $table->boolean('affiliate_enabled')
                ->default(false)
                ->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('country_id');
            $table->dropColumn(['slug', 'affiliate_enabled']);
        });
    }
};
