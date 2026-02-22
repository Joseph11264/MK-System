<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\RequisicionSt;
use Illuminate\Http\Request;

class ClienteController
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Búsqueda inteligente por nombre, teléfono o correo
        if ($request->filled('buscar')) {
            $termino = '%' . $request->buscar . '%';
            $query->where('nombre', 'like', $termino)
                  ->orWhere('telefono', 'like', $termino)
                  ->orWhere('correo', 'like', $termino);
        }

        $clientes = $query->orderBy('nombre', 'asc')->paginate(20);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clientes,nombre',
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:255'
        ], [
            'nombre.unique' => '⚠️ Ya existe un cliente registrado con este nombre exacto.'
        ]);

        Cliente::create($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clientes,nombre,' . $cliente->id,
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:255'
        ]);

        $cliente->update($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    // EL HISTORIAL DEL CLIENTE
    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        
        // Buscamos todos los tickets de ST que coincidan con el nombre de este cliente
        $tickets = RequisicionSt::where('cliente', $cliente->nombre)
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
                                
        return view('clientes.show', compact('cliente', 'tickets'));
    }

    public function destroy($id)
    {
        Cliente::findOrFail($id)->delete();
        return back()->with('success', 'Cliente eliminado del directorio.');
    }
}