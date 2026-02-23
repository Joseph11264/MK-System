<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Formula;
use Illuminate\Http\Request;

class FormulaController
{
    // Mostrar la pantalla de gestión de la fórmula
    public function manage($id)
    {
        // Solo Producción, Admin o SuperAdmin deberían crear recetas
        if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Produccion'])) {
            abort(403, 'No tienes permiso para gestionar fórmulas de producción.');
        }

        // Traemos el producto final y cargamos sus ingredientes actuales
        $producto = Producto::with('ingredientesFormula.ingrediente')->findOrFail($id);
        
        // Traemos todos los demás productos para el select (evitando que el producto sea ingrediente de sí mismo)
        $disponibles = Producto::where('id', '!=', $id)->orderBy('descripcion', 'asc')->get();

        return view('formulas.manage', compact('producto', 'disponibles'));
    }

    // Agregar un componente a la receta
    public function addIngrediente(Request $request, $id)
    {
        $request->validate([
            'ingrediente_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.0001'
        ]);

        if ($id == $request->ingrediente_id) {
            return back()->with('error', '⚠️ Un producto no puede ser ingrediente de sí mismo.');
        }

        // Evitar duplicados en la receta
        $existe = Formula::where('producto_id', $id)->where('ingrediente_id', $request->ingrediente_id)->first();
        if ($existe) {
            return back()->with('error', '⚠️ Este material ya existe en la fórmula. Si deseas cambiar la cantidad, elimínalo y vuelve a agregarlo.');
        }

        Formula::create([
            'producto_id' => $id,
            'ingrediente_id' => $request->ingrediente_id,
            'cantidad' => $request->cantidad
        ]);

        return back()->with('success', '✅ Material agregado a la receta de producción.');
    }

    // Actualizar la cantidad de un componente directamente en la tabla
    public function updateIngrediente(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0.0001'
        ]);

        $formula = Formula::findOrFail($id);
        $formula->update(['cantidad' => $request->cantidad]);

        return back()->with('success', '✅ Cantidad actualizada correctamente.');
    }

    // Quitar un componente de la receta
    public function removeIngrediente($id)
    {
        $formula = Formula::findOrFail($id);
        $formula->delete();
        return back()->with('success', '🗑️ Material removido de la receta.');
    }
}