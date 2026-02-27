<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Spalte hinzufügen (noch ohne unique!)
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->string('full_slug')->nullable()->after('alias');
        });

        // 2️⃣ full_slug generieren
        $locations = DB::table('wwde_locations')->get();

        foreach ($locations as $location) {

            // Beispiel-Struktur:
            // country_slug/city_alias
            // ggf. anpassen an deine Struktur

            $baseSlug = Str::slug($location->alias);

            $slug = $baseSlug;
            $counter = 1;

            // Kollision prüfen
            while (
                DB::table('wwde_locations')
                    ->where('full_slug', $slug)
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            DB::table('wwde_locations')
                ->where('id', $location->id)
                ->update(['full_slug' => $slug]);
        }

        // 3️⃣ Jetzt UNIQUE setzen
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->unique('full_slug');
        });
    }

    public function down(): void
    {
        Schema::table('wwde_locations', function (Blueprint $table) {
            $table->dropUnique(['full_slug']);
            $table->dropColumn('full_slug');
        });
    }
};
