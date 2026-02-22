<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('familia')->nullable();
            $table->string('imagen')->nullable();
            // Creamos un índice único para evitar duplicados a nivel de base de datos
            $table->unique('codigo_producto');
        });
    }

    public function down() {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropUnique(['codigo_producto']);
            $table->dropColumn(['familia', 'imagen']);
        });
    }
};
