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
        Schema::table('ordenes_produccion', function (Blueprint $table) {
            // Lo ponemos nullable por si acaso, pero lo exigiremos en el formulario
            $table->unsignedBigInteger('tecnico_id')->nullable()->after('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes_produccion', function (Blueprint $table) {
            //
        });
    }
};
