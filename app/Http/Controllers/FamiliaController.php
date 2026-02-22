<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use Illuminate\Http\Request;

class FamiliaController
{
    public function index()
    {
        $familias = Familia::orderBy('nombre', 'asc')->get();
        return view('familias.index', compact('familias'));
    }

    public function store(Request $request)
    {
        // 1. Validamos los 3 campos estrictamente
        $request->validate([
            'nombre' => 'required|string|unique:familias,nombre|max:100',
            'rango_inicio' => 'required|digits:6',
            'rango_fin' => 'required|digits:6',
        ], [
            'nombre.unique' => '⚠️ Ya existe una Familia con este nombre.',
            'rango_inicio.digits' => '⚠️ El rango de inicio debe tener exactamente 6 números.',
            'rango_fin.digits' => '⚠️ El rango de fin debe tener exactamente 6 números.'
        ]);

        // 2. Guardamos todo en la base de datos
        \App\Models\Familia::create([
            'nombre' => $request->nombre,
            'rango_inicio' => $request->rango_inicio,
            'rango_fin' => $request->rango_fin,
        ]);

        return back()->with('success', '✅ Familia registrada correctamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $familias = \App\Models\Familia::orderBy('nombre', 'asc')->get();
        return view('productos.edit', compact('producto', 'familias'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'codigo_producto' => ['required', 'string', 'regex:/^\d{6}[A-Za-z]?$/', 'unique:productos,codigo_producto,'.$producto->id],
            'descripcion' => 'required|string|max:255',
            'familia_id' => 'nullable|exists:familias,id',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $datos = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($datos);
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    

    public function destroy($id)
    {
        Familia::findOrFail($id)->delete();
        return back()->with('success', 'Familia eliminada.');
    }
}