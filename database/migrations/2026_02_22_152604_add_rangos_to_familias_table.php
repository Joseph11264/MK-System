<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('familias', function (Blueprint $table) {
            $table->string('rango_inicio', 6)->nullable()->after('nombre');
            $table->string('rango_fin', 6)->nullable()->after('rango_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            //
        });
    }
};
