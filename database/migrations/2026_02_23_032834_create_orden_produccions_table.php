<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ordenes_produccion', function (Blueprint $table) {
            $table->id();
            
            // Un código visual para el equipo (Ej: OP-0001)
            $table->string('codigo_orden')->unique();
            
            // ¿Qué vamos a fabricar?
            $table->unsignedBigInteger('producto_id');
            
            // ¿Cuántos vamos a fabricar? (Suele ser un número entero para productos terminados)
            $table->integer('cantidad');
            
            // El estado de la orden
            $table->enum('status', ['Pendiente', 'En Curso', 'Completado', 'Cancelado'])->default('Pendiente');
            
            // ¿Quién ordenó la fabricación?
            $table->unsignedBigInteger('usuario_id');
            
            // Instrucciones adicionales para los técnicos
            $table->text('notas')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_produccions');
    }
};
