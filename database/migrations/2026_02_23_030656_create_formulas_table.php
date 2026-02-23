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
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            
            // El ID del producto final que vamos a ensamblar/fabricar
            $table->unsignedBigInteger('producto_id'); 
            
            // El ID del material o componente que se necesita
            $table->unsignedBigInteger('ingrediente_id'); 
            
            // La cantidad que se requiere de este ingrediente (4 decimales para soportar 0.25 Mts)
            $table->decimal('cantidad', 10, 4);
            
            $table->timestamps();

            // Esto evita que agregues por accidente el mismo ingrediente dos veces a la misma receta
            $table->unique(['producto_id', 'ingrediente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};
