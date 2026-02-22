<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    protected $fillable = ['nombre', 'rango_inicio', 'rango_fin'];
    
    public function productos() {
        return $this->hasMany(Producto::class);
    }
}