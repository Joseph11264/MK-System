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
        if (in_array(auth()->user()->rol, ['Produccion', 'Almacen', 'ServicioTecnico'])) {
            abort(403, 'Acceso denegado. Este rol solo puede consultar el catálogo.');
        }

        $familias = \App\Models\Familia::orderBy('nombre', 'asc')->get();
        return view('productos.create', compact('familias'));
    }

    public function store(Request $request)
    {
        if (in_array(auth()->user()->rol, ['Produccion', 'Almacen', 'ServicioTecnico'])) {
            abort(403, 'Acceso denegado. Este rol solo puede consultar el catálogo.');
        }

        $request->validate([
            'codigo_producto' => ['required', 'string', 'regex:/^\d{6}[A-Za-z]?$/', 'unique:productos,codigo_producto'],
            'descripcion' => 'required|string|max:255',
            'familia_id' => 'nullable|exists:familias,id',
            // Añadimos las reglas estrictas de formato y peso (2048 KB = 2MB)
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' 
        ], [
            'codigo_producto.regex' => '⚠️ El código debe tener exactamente 6 números y una letra opcional.',
            'codigo_producto.unique' => '⚠️ Este código de producto ya existe.',
            // Nuevos mensajes de error para la imagen
            'imagen.image' => '⚠️ El archivo subido debe ser una imagen.',
            'imagen.mimes' => '⚠️ Solo se permiten imágenes en formato JPG, PNG o WEBP.',
            'imagen.max' => '⚠️ La imagen es muy pesada. El tamaño máximo es de 2MB.'
        ]);

        $datos = $request->all();
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($datos);
        return redirect()->route('productos.index')->with('success', 'Producto registrado.');
    }

    public function show($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Buscamos dónde se ha usado este producto (requiere tener los modelos de Detalle creados)
        $salidasAlmacen = \App\Models\DetalleRequisicion::where('codigo_producto', $producto->codigo_producto)->with('requisicion')->get();
        $salidasST = \App\Models\DetalleRequisicionSt::where('codigo_producto', $producto->codigo_producto)->with('requisicionSt')->get();

        return view('productos.show', compact('producto', 'salidasAlmacen', 'salidasST'));
    }

    public function edit($id)
    {
        if (in_array(auth()->user()->rol, ['Produccion', 'Almacen', 'ServicioTecnico'])) {
            abort(403, 'Acceso denegado. Este rol solo puede consultar el catálogo.');
        }

        // 1. Buscamos el producto y las familias
        $producto = Producto::findOrFail($id);
        $familias = \App\Models\Familia::orderBy('nombre', 'asc')->get();
        
        // 2. Mandamos los datos a la vista edit.blade.php
        return view('productos.edit', compact('producto', 'familias'));
    }

    public function update(Request $request, $id)
    {
        if (in_array(auth()->user()->rol, ['Produccion', 'Almacen', 'ServicioTecnico'])) {
            abort(403, 'Acceso denegado. Este rol solo puede consultar el catálogo.');
        }

        $producto = Producto::findOrFail($id);

        // Validamos asegurándonos de que el código no se repita con OTRO producto,
        // pero ignorando el ID del producto actual para que no dé error consigo mismo.
        $request->validate([
            'codigo_producto' => ['required', 'string', 'regex:/^\d{6}[A-Za-z]?$/', 'unique:productos,codigo_producto'],
            'descripcion' => 'required|string|max:255',
            'familia_id' => 'nullable|exists:familias,id',
            // Añadimos las reglas estrictas de formato y peso (2048 KB = 2MB)
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' 
        ], [
            'codigo_producto.regex' => '⚠️ El código debe tener exactamente 6 números y una letra opcional.',
            'codigo_producto.unique' => '⚠️ Este código de producto ya existe.',
            // Nuevos mensajes de error para la imagen
            'imagen.image' => '⚠️ El archivo subido debe ser una imagen.',
            'imagen.mimes' => '⚠️ Solo se permiten imágenes en formato JPG, PNG o WEBP.',
            'imagen.max' => '⚠️ La imagen es muy pesada. El tamaño máximo es de 2MB.'
        ]);

        $datos = $request->except('imagen');

        // Si el usuario sube una nueva imagen, borramos la vieja y guardamos la nueva
        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($datos);
        
        return redirect()->route('productos.index')->with('success', '✅ Producto actualizado correctamente.');
    }

    // Método para eliminar
    public function destroy($id)
    {
        if (in_array(auth()->user()->rol, ['Produccion', 'Almacen', 'ServicioTecnico'])) {
            abort(403, 'Acceso denegado. Este rol solo puede consultar el catálogo.');
        }
        
        $producto = Producto::findOrFail($id);
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        return back()->with('success', 'Producto eliminado.');
    }
}