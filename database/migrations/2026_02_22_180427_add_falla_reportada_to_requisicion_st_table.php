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
        Schema::table('requisicion_st', function (Blueprint $table) {
            $table->text('falla_reportada')->nullable()->after('codigo_equipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisicion_st', function (Blueprint $table) {
            //
        });
    }
};
