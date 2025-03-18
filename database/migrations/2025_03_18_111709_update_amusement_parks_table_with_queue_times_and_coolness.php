<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            // queue_times_id hinzufügen, falls nicht vorhanden
            if (!Schema::hasColumn('amusement_parks', 'queue_times_id')) {
                $table->integer('queue_times_id')->nullable()->after('external_id');
            }
            // coolness_score hinzufügen, falls nicht vorhanden
            if (!Schema::hasColumn('amusement_parks', 'coolness_score')) {
                $table->tinyInteger('coolness_score')->nullable()->default(0)->after('queue_times_id');
            }
            // video_url aus vorheriger Anpassung, falls gewünscht
            if (!Schema::hasColumn('amusement_parks', 'video_url')) {
                $table->string('video_url')->nullable()->after('description');
            }
        });
    }
    public function down()
    {
        Schema::table('amusement_parks', function (Blueprint $table) {
            if (Schema::hasColumn('amusement_parks', 'queue_times_id')) {
                $table->dropColumn('queue_times_id');
            }
            if (Schema::hasColumn('amusement_parks', 'coolness_score')) {
                $table->dropColumn('coolness_score');
            }
            if (Schema::hasColumn('amusement_parks', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
