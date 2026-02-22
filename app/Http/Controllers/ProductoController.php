<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();

        // FILTROS
        if ($request->filled('codigo')) {
            $query->where('codigo_producto', 'like', '%' . $request->codigo . '%');
        }
        if ($request->filled('nombre')) {
            $query->where('descripcion', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('familia')) {
            $query->where('familia', $request->familia);
        }

        $productos = $query->orderBy('descripcion', 'asc')->paginate(20);
        
        // Obtenemos las familias únicas para el select del filtro
        $familias = Producto::whereNotNull('familia')->distinct()->pluck('familia');

        return view('productos.index', compact('productos', 'familias'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        // VALIDACIÓN DE CÓDIGO ÚNICO
        $request->validate([
            'codigo_producto' => 'required|string|unique:productos,codigo_producto',
            'descripcion' => 'required|string|max:255',
            'familia' => 'nullable|string|max:100',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' // Max 2MB
        ], [
            'codigo_producto.unique' => '⚠️ Este código de producto ya existe en el sistema.'
        ]);

        $datos = $request->all();

        // PROCESAR IMAGEN
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($datos);
        return redirect()->route('productos.index')->with('success', 'Producto registrado con éxito.');
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