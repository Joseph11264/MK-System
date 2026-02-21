<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up() {
    Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo_producto', 50)->unique();
        $table->string('descripcion', 255);
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
};
