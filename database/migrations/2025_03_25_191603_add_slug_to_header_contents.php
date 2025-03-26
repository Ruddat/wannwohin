<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('header_contents', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });

        // Optional: Bestehende Einträge mit einem Slug versehen
        \App\Models\HeaderContent::all()->each(function ($header) {
            $header->slug = $header->title ? \Illuminate\Support\Str::slug($header->title) : 'header-' . uniqid();
            $header->save();
        });
    }

    public function down()
    {
        Schema::table('header_contents', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
