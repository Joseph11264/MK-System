<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisicion extends Model
{
    // Le decimos exactamente qué tabla de tu base de datos usar
    protected $table = 'requisiciones'; 

    protected $fillable = [
        'tipo',
        'nro_tecnico',
        'nombre_tecnico',
        'status',
        'creado_por_usuario_id'
    ];

    // Relación: Una Requisición tiene MUCHOS Detalles
    public function detalles()
    {
        return $this->hasMany(DetalleRequisicion::class, 'requisicion_id');
    }

    // Relación: Una Requisición PERTENECE A un Usuario
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por_usuario_id');
    }
}