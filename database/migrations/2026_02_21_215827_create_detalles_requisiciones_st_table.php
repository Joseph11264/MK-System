<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('detalles_requisiciones_st', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisicion_st_id')->constrained('requisicion_st')->onDelete('cascade');
            $table->string('codigo_producto', 50);
            $table->integer('cantidad')->default(1);
            $table->text('observacion')->nullable();
        });
    }

    public function down() { Schema::dropIfExists('detalles_requisiciones_st'); }
};