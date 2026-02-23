<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    // La tabla explícita por si Laravel se confunde con el plural
    protected $table = 'movimientos_inventarios';

    protected $fillable = [
        'producto_id',
        'tipo_movimiento',
        'cantidad',
        'motivo',
        'referencia_type',
        'referencia_id',
        'usuario_id'
    ];

    // Relación: Este movimiento pertenece a un Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Relación: Este movimiento fue hecho por un Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación Mágica: Permite traer la Requisición, el Ticket o la Orden de Producción
    // que causó este movimiento, sin importar de qué tabla venga.
    public function referencia()
    {
        return $this->morphTo();
    }
}