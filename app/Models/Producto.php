<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model {
    protected $fillable = [
        'codigo_producto', 'descripcion', 'unidad_medida', 'familia_id', 'imagen' 
    ];

    public function familia() {
        return $this->belongsTo(Familia::class, 'familia_id');
    }

    // ==========================================
    // RELACIONES Y CÁLCULOS DEL MINI-ERP
    // ==========================================

    /**
     * Relación: Un producto tiene muchos movimientos en el Kardex.
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    /**
     * Accesorio Mágico: Calcula el stock actual en tiempo real.
     * Se manda a llamar usando: $producto->stock
     */
    public function getStockAttribute()
    {
        // Sumamos todas las entradas
        $entradas = $this->movimientos()->where('tipo_movimiento', 'Entrada')->sum('cantidad');
        
        // Sumamos todas las salidas
        $salidas = $this->movimientos()->where('tipo_movimiento', 'Salida')->sum('cantidad');
        
        // El stock es la diferencia
        return $entradas - $salidas;
    }

    /**
     * Accesorio Visual: Devuelve el stock limpio junto con su Unidad de Medida.
     * Ejemplo: "5 Und", "1.25 Mts"
     * Se manda a llamar usando: $producto->stock_format
     */
    public function getStockFormatAttribute()
    {
        // floatval() quita los ceros decimales innecesarios (Ej: 10.5000 se vuelve 10.5)
        $stockLimpio = floatval($this->stock); 
        
        return $stockLimpio . ' ' . $this->unidad_medida;
    }

    /**
     * FÓRMULA: Los ingredientes que necesito para ser fabricado.
     * Ejemplo: Un "Módulo" llama a esta función para ver qué repuestos requiere.
     */
    public function ingredientesFormula()
    {
        return $this->hasMany(Formula::class, 'producto_id');
    }

    /**
     * FÓRMULA INVERSA: En qué otros productos me usan como ingrediente.
     * Ejemplo: Una "Resistencia" llama a esta función para saber en qué Módulos se utiliza.
     */
    public function usadoEnFormulas()
    {
        return $this->hasMany(Formula::class, 'ingrediente_id');
    }

}

