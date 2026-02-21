<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RequisicionSt extends Model
{
    protected $table = 'requisicion_st';
    protected $fillable = ['nro_orden_st', 'cliente', 'telefono_cliente', 'correo_cliente', 'codigo_equipo', 'status', 'usuario_creador_id', 'tecnico_asignado_id'];

    public function detalles() {
        return $this->hasMany(DetalleRequisicionSt::class, 'requisicion_st_id');
    }

    public function creador() {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function tecnico() {
        return $this->belongsTo(User::class, 'tecnico_asignado_id');
    }
}