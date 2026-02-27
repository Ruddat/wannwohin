<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // alias darf nicht global unique sein
        // bewusst leer
    }

    public function down(): void
    {
        // nichts zu tun
    }
};
