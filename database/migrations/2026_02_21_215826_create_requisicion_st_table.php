<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('requisicion_st', function (Blueprint $table) {
            $table->id();
            $table->string('nro_orden_st', 50)->nullable();
            $table->string('cliente', 100);
            $table->string('codigo_equipo', 50);
            $table->enum('status', ['Pendiente', 'En Curso', 'Completado', 'Cancelado'])->default('Pendiente');
            
            // Relaciones dobles con la tabla usuarios
            $table->foreignId('usuario_creador_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('tecnico_asignado_id')->nullable()->constrained('usuarios')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down() { Schema::dropIfExists('requisicion_st'); }
};