<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    use HasFactory;

    protected $table = 'ordenes_produccion';

    protected $fillable = [
        'codigo_orden',
        'producto_id',
        'tecnico_id',
        'cantidad',
        'status',
        'usuario_id',
        'notas'
  
    ];

    // Relación: El producto final que se va a fabricar
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación: El usuario que creó la orden
    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // RELACIÓN MÁGICA (Polimórfica): 
    // Trae todos los movimientos del Kardex (Salidas de material y Entrada del producto final)
    // que fueron causados por esta orden específica.
    public function movimientos()
    {
        return $this->morphMany(MovimientoInventario::class, 'referencia');
    }

    // Relación: El técnico asignado para fabricar esto
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}