<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('requisicion_st', function (Blueprint $table) {
            // Añadimos teléfono y correo justo después de 'cliente'
            $table->string('telefono_cliente', 20)->nullable()->after('cliente');
            $table->string('correo_cliente', 100)->nullable()->after('telefono_cliente');
        });
    }

    public function down()
    {
        Schema::table('requisicion_st', function (Blueprint $table) {
            $table->dropColumn(['telefono_cliente', 'correo_cliente']);
        });
    }
};