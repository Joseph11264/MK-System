<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('detalles_requisicion', function (Blueprint $table) {
            $table->id();
            // Relación con la tabla requisiciones
            $table->foreignId('requisicion_id')->constrained('requisiciones')->onDelete('cascade');
            $table->string('codigo_producto', 50);
            $table->integer('cantidad');
            $table->text('observacion')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalles_requisicion');
    }
};