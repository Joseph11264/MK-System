<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('requisicion_st', function (Blueprint $table) {
            $table->decimal('precio_reparacion', 10, 2)->nullable()->after('materiales_entregados');
            $table->string('referencia_pago')->nullable()->after('estado_pago');
        });
    }
    public function down() {
        Schema::table('requisicion_st', function (Blueprint $table) {
            $table->dropColumn(['precio_reparacion', 'referencia_pago']);
        });
    }
};
