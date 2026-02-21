<?php

namespace App\Http\Controllers;

use App\Models\RequisicionSt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ServicioTecnicoController
{
    public function index(Request $request)
    {
        // Traemos las requisiciones con los datos de ambos usuarios y detalles
        $tickets = RequisicionSt::with(['creador', 'tecnico', 'detalles'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return view('st.index', compact('tickets'));
    }

    public function create()
    {
        // Obtenemos solo los técnicos para el menú desplegable
        $tecnicos = User::whereIn('rol', ['ServicioTecnico', 'SuperAdmin'])->get();
        return view('st.create', compact('tecnicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nro_orden_st' => 'required|string',
            'cliente' => 'required|string',
            'telefono_cliente' => 'nullable|string|max:20', 
            'correo_cliente' => 'nullable|email|max:100', 
            'codigo_equipo' => 'required|string',
            'tecnico_asignado_id' => 'nullable|exists:usuarios,id',
            'productos' => 'required|array|min:1',
            'productos.*.codigo' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.observacion' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $ticket = RequisicionSt::create([
                'nro_orden_st' => $validated['nro_orden_st'],
                'cliente' => $validated['cliente'],
                'telefono_cliente' => $validated['telefono_cliente'] ?? null,
                'correo_cliente' => $validated['correo_cliente'] ?? null,   
                'codigo_equipo' => $validated['codigo_equipo'],
                'tecnico_asignado_id' => $validated['tecnico_asignado_id'],
                'usuario_creador_id' => Auth::id(),
                'status' => 'Pendiente'
            ]);

            $detalles = array_map(function($prod) {
                return [
                    'codigo_producto' => $prod['codigo'],
                    'cantidad' => $prod['cantidad'],
                    'observacion' => $prod['observacion'] ?? null,
                ];
            }, $validated['productos']);

            $ticket->detalles()->createMany($detalles);
        });

        return redirect()->route('st.index')->with('success', 'Ticket ST creado exitosamente.');
    }
}