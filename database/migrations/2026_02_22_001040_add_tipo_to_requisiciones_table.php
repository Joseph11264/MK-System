<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->enum('tipo', ['Requisicion', 'Devolucion'])->default('Requisicion')->after('nombre_tecnico');
        });
    }
    public function down() {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};