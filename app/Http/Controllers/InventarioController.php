<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\MovimientoInventario;

class InventarioController
{
    // Mostrar el formulario
    public function ajuste()
    {
        // Solo Admin o Almacén pueden hacer ajustes manuales
        if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Almacen'])) {
            abort(403, 'No tienes permiso para ajustar el inventario manualmente.');
        }

        // Traemos los productos ordenados para el selector
        $productos = Producto::orderBy('descripcion', 'asc')->get();
        return view('inventario.ajuste', compact('productos'));
    }

    // Procesar y guardar el movimiento
    public function registrarAjuste(Request $request)
    {
        if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Almacen'])) {
            abort(403, 'No tienes permiso para ajustar el inventario manualmente.');
        }

        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo_movimiento' => 'required|in:Entrada,Salida',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
        ]);

        MovimientoInventario::create([
            'producto_id' => $request->producto_id,
            'tipo_movimiento' => $request->tipo_movimiento,
            'cantidad' => $request->cantidad,
            'motivo' => $request->motivo,
            'usuario_id' => auth()->id(),

        ]);

        return redirect()->route('productos.index')->with('success', '📦 Movimiento de inventario registrado correctamente.');
    }
}