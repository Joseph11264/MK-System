<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenProduccionController 
{
    // Mostrar lista de Órdenes
    public function index()
    {
        $ordenes = OrdenProduccion::with(['producto', 'creador'])->orderBy('created_at', 'desc')->paginate(15);
        return view('ordenes.index', compact('ordenes'));
    }

    // Formulario para crear una Orden
    public function create()
    {
        $productosFabricables = Producto::has('ingredientesFormula')->orderBy('descripcion', 'asc')->get();
        // NUEVO: Traer técnicos
        $tecnicos = \App\Models\User::whereIn('rol', ['ServicioTecnico', 'Produccion'])->orderBy('nombre', 'asc')->get();
        
        return view('ordenes.create', compact('productosFabricables', 'tecnicos'));
    }

    // El Motor Principal: Crear y descontar inventario
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            // ¡CORREGIDO AQUÍ! Cambiado 'users' por 'usuarios'
            'tecnico_id' => 'required|exists:usuarios,id',
            'cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string'
        ]);

        $productoFinal = Producto::with('ingredientesFormula.ingrediente')->findOrFail($request->producto_id);

        // 1. SIMULACIÓN: ¿Tenemos suficiente material?
        $faltantes = [];
        foreach ($productoFinal->ingredientesFormula as $item) {
            $cantidadNecesaria = $item->cantidad * $request->cantidad;
            $stockDisponible = floatval($item->ingrediente->stock);

            if ($stockDisponible < $cantidadNecesaria) {
                $diferencia = $cantidadNecesaria - $stockDisponible;
                $faltantes[] = "Faltan {$diferencia} {$item->ingrediente->unidad_medida} de [{$item->ingrediente->codigo_producto}] {$item->ingrediente->descripcion}";
            }
        }

        // Si falta aunque sea un tornillo, bloqueamos la creación
        if (count($faltantes) > 0) {
            return back()->withInput()->with('error', '⚠️ Stock insuficiente para fabricar. Detalles: ' . implode(' | ', $faltantes));
        }

        // 2. EJECUCIÓN SEGURA: Si hay material, creamos la OP y descontamos (Transacción)
        DB::transaction(function () use ($request, $productoFinal) {
            
            // Generar Código (Ej: OP-0001)
            $lastOp = OrdenProduccion::orderBy('id', 'desc')->first();
            $nextId = $lastOp ? $lastOp->id + 1 : 1;
            $codigo = 'OP-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Crear la Orden
            $orden = OrdenProduccion::create([
                'codigo_orden' => $codigo,
                'producto_id' => $productoFinal->id,
                'tecnico_id' => $request->tecnico_id,
                'cantidad' => $request->cantidad,
                'status' => 'Pendiente', // Inicia en Pendiente (Material ya apartado)
                'usuario_id' => auth()->id(),
                'notas' => $request->notas
            ]);

            // Asignación Estricta (Hard Allocation): Descontar del Kardex inmediatamente
            foreach ($productoFinal->ingredientesFormula as $item) {
                MovimientoInventario::create([
                    'producto_id' => $item->ingrediente_id,
                    'tipo_movimiento' => 'Salida',
                    'cantidad' => $item->cantidad * $request->cantidad, // Fórmula multiplicada por lo que vas a fabricar
                    'motivo' => "Material consumido por Orden de Producción {$codigo}",
                    'referencia_type' => OrdenProduccion::class,
                    'referencia_id' => $orden->id,
                    'usuario_id' => auth()->id()
                ]);
            }
        });

        return redirect()->route('ordenes.index')->with('success', '✅ Orden de Producción creada. Los materiales han sido descontados del inventario.');
    }

    // Cambiar de estado (En Curso, Completado, etc.)
    public function updateStatus(Request $request, $id)
    {
        $orden = OrdenProduccion::findOrFail($id);
        
        // Regla: No puedes cambiar el estado si ya está Completado o Cancelado
        if (in_array($orden->status, ['Completado', 'Cancelado'])) {
            return back()->with('error', 'Esta orden ya está cerrada y no se puede modificar.');
        }

        $nuevoEstado = $request->status;
        
        DB::transaction(function () use ($orden, $nuevoEstado) {
            $orden->update(['status' => $nuevoEstado]);

            // Si se completó con éxito, INYECTAR el producto final al inventario
            if ($nuevoEstado === 'Completado') {
                MovimientoInventario::create([
                    'producto_id' => $orden->producto_id,
                    'tipo_movimiento' => 'Entrada',
                    'cantidad' => $orden->cantidad,
                    'motivo' => "Ingreso por Orden de Producción {$orden->codigo_orden} terminada.",
                    'referencia_type' => OrdenProduccion::class,
                    'referencia_id' => $orden->id,
                    'usuario_id' => auth()->id()
                ]);
            }
        });

        return back()->with('success', "Estado actualizado a: {$nuevoEstado}.");
    }
}