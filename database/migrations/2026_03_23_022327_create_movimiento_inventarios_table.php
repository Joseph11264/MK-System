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
        Schema::create('movimientos_inventarios', function (Blueprint $table) {
            $table->id();
            
            // Campos a prueba de balas (sin restricción estricta de SQLite)
            $table->unsignedBigInteger('producto_id');
            
            $table->enum('tipo_movimiento', ['Entrada', 'Salida']);
            $table->decimal('cantidad', 10, 4);
            $table->string('motivo'); 
            $table->nullableMorphs('referencia'); 
            
            // Campo a prueba de balas para el usuario
            $table->unsignedBigInteger('usuario_id');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventarios');
    }
};
