<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRequisicion extends Model
{
    protected $table = 'detalles_requisicion';

    // Desactivamos los timestamps porque tu tabla original de detalles no los tiene
    public $timestamps = false; 

    protected $fillable = [
        'requisicion_id',
        'codigo_producto',
        'cantidad',
        'observacion'
    ];

    // Relación inversa: Este detalle PERTENECE A una Requisición
    public function requisicion()
    {
        return $this->belongsTo(Requisicion::class, 'requisicion_id');
    }
}