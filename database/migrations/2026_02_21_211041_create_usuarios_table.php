<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('username', 50)->unique();
            // ¡Actualizado a SuperAdmin como acordamos!
            $table->enum('rol', ['SuperAdmin', 'Administracion', 'ServicioTecnico', 'Almacen', 'Produccion'])->default('Produccion');
            $table->string('password'); // Simplificado para Laravel
            $table->timestamps(); // Crea automáticamente 'created_at' y 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};