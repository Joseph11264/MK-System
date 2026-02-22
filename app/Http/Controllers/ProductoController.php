<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController
{
    public function index(Request $request)
    {
        $query = Producto::with('familia'); // Cargamos la relación

        if ($request->filled('codigo')) {
            $query->where('codigo_producto', 'like', '%' . $request->codigo . '%');
        }
        if ($request->filled('nombre')) {
            $query->where('descripcion', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('familia_id')) { // Filtramos por ID
            $query->where('familia_id', $request->familia_id);
        }

        $productos = $query->orderBy('descripcion', 'asc')->paginate(20);
        $familias = \App\Models\Familia::orderBy('nombre', 'asc')->get();

        return view('productos.index', compact('productos', 'familias'));
    }

    public function create()
    {
        $familias = \App\Models\Familia::orderBy('nombre', 'asc')->get();
        return view('productos.create', compact('familias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_producto' => 'required|string|unique:productos,codigo_producto',
            'descripcion' => 'required|string|max:255',
            'familia_id' => 'nullable|exists:familias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ], [
            'codigo_producto.unique' => '⚠️ Este código de producto ya existe.'
        ]);

        $datos = $request->all();
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($datos);
        return redirect()->route('productos.index')->with('success', 'Producto registrado.');
    }

    // Método para eliminar
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        return back()->with('success', 'Producto eliminado.');
    }
}