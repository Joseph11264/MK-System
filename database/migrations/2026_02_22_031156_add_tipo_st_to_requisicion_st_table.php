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
            // Usamos string() que es 100% compatible con SQLite para modificaciones
            $table->string('tipo_st')->default('Reparacion');
        });
    }
    public function down() {
        Schema::table('requisicion_st', function (Blueprint $table) {
            $table->dropColumn('tipo_st');
        });
    }
};