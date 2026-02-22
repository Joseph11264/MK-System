<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('requisicion_st', function (Blueprint $table) {
            $table->boolean('materiales_entregados')->default(false)->after('status');
        });
    }
    public function down() {
        Schema::table('requisicion_st', function (Blueprint $table) {
            $table->dropColumn('materiales_entregados');
        });
    }
};