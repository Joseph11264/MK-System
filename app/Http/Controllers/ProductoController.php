<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController
{
    // Reemplaza a prodConsultar()
    public function index()
    {
        $productos = Producto::orderBy('codigo_producto', 'asc')->get();
        return view('productos.index', compact('productos'));
    }

    // Reemplaza a prodCrear()
    public function create()
    {
        return view('productos.create');
    }

    // Reemplaza a prodGuardar()
    public function store(Request $request)
    {
        // Validación: el código debe ser único en la tabla productos
        $validated = $request->validate([
            'codigo_producto' => 'required|string|max:50|unique:productos,codigo_producto',
            'descripcion' => 'required|string|max:255',
        ]);

        Producto::create([
            'codigo_producto' => $validated['codigo_producto'],
            'descripcion' => $validated['descripcion'],
            'activo' => true,
        ]);

        return redirect()->route('productos.index')
                         ->with('success', 'Producto agregado al catálogo exitosamente.');
    }
}