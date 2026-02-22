<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model {
    protected $fillable = [
        'codigo_producto', 'descripcion', 'familia_id', 'imagen' // Cambiamos familia por familia_id
    ];

    public function familia() {
        return $this->belongsTo(Familia::class, 'familia_id');
    }
}

