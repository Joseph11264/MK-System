<?php
namespace App\Http\Controllers;

use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RequisicionController
{
    // Reemplaza a tu función consultar()
    public function index(Request $request)
    {
        // Laravel pagina automáticamente por ti. Y le pedimos que traiga los detalles de una vez ('with').
        $requisiciones = Requisicion::with('detalles')
                            ->orderBy('fecha_creacion', 'desc')
                            ->paginate(50); // Reemplaza todo tu cálculo de $offset y $limit manual
        
        return view('requisiciones.index', compact('requisiciones'));
    }

    // Reemplaza a tu función crear()
    public function create()
    {
        return view('requisiciones.create');
    }

    // Reemplaza a tu función guardarRequisicion()
    public function store(Request $request)
    {
        // 1. Validación estricta
        $validated = $request->validate([
            'nro_tecnico' => 'required|string|max:50',
            'nombre_tecnico' => 'required|string|max:100',
            'productos' => 'required|array|min:1',
            'productos.*.codigo' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.observacion' => 'nullable|string',
        ]);

        // 2. Transacción Segura (Reemplaza a $this->db->beginTransaction())
        DB::transaction(function () use ($validated) {
            
            // Creamos la cabecera de la requisición
            $requisicion = Requisicion::create([
                'nro_tecnico' => $validated['nro_tecnico'],
                'nombre_tecnico' => $validated['nombre_tecnico'],
                'status' => 'Pendiente',
                'creado_por_usuario_id' => Auth::id(),
            ]);

            // Guardamos todos los detalles en la base de datos de un solo golpe
            // Mapeamos los campos para que coincidan con la base de datos
            $detallesParaGuardar = array_map(function($producto) {
                return [
                    'codigo_producto' => $producto['codigo'],
                    'cantidad' => $producto['cantidad'],
                    'observacion' => $producto['observacion'] ?? null,
                ];
            }, $validated['productos']);

            $requisicion->detalles()->createMany($detallesParaGuardar);
        });

        // 3. Redirección con alerta para Bootstrap
        return redirect()->route('requisiciones.index')
                         ->with('success', '¡Requisición creada con éxito!');
    }

    // Reemplaza a tu función avanzarStatus()
    public function avanzarStatus(Requisicion $requisicion, Request $request)
    {
        // Validación rápida del nuevo estado
        $request->validate(['new_status' => 'required|in:En Curso,Completado,Cancelado']);

        // Actualización en 1 línea (Reemplaza tu consulta UPDATE manual)
        $requisicion->update(['status' => $request->new_status]);

        return back()->with('success', 'El estado de la requisición ha avanzado a: ' . $request->new_status);
    }
}