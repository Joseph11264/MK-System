<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'ingrediente_id',
        'cantidad'
    ];

    // Relación hacia el producto que estamos fabricando
    public function productoFinal()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación hacia el componente/material que estamos usando
    public function ingrediente()
    {
        return $this->belongsTo(Producto::class, 'ingrediente_id');
    }
}