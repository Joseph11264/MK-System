<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id();
            $table->string('nro_tecnico', 50);
            $table->string('nombre_tecnico', 100);
            $table->enum('status', ['Pendiente', 'En Curso', 'Completado', 'Cancelado'])->default('Pendiente');
            // Relación con la tabla usuarios
            $table->foreignId('creado_por_usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('requisiciones');
    }
};