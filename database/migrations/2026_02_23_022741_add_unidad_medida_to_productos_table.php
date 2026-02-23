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
        Schema::table('productos', function (Blueprint $table) {
            // Agregamos la columna. Por defecto será 'Und' (Unidad) para no romper lo que ya tienes.
            $table->string('unidad_medida', 10)->default('Und')->after('descripcion');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('unidad_medida');
        });
    }
};
