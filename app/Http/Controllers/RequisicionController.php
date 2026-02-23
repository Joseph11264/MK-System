<?php
namespace App\Http\Controllers;

use App\Models\Requisicion;
use App\Models\MovimientoInventario; // <-- NUEVO
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RequisicionController 
{
    // Reemplaza a tu función consultar()
    public function index(Request $request)
    {
        $query = \App\Models\Requisicion::query();

        // 1. FILTROS DE BÚSQUEDA
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('tecnico')) {
            // Buscamos si coincide con el Nro de Técnico o con el Nombre
            $query->where(function($q) use ($request) {
                $q->where('nro_tecnico', 'like', '%' . $request->tecnico . '%')
                  ->orWhere('nombre_tecnico', 'like', '%' . $request->tecnico . '%');
            });
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // 2. LÓGICA DE ORDENAMIENTO (ASC / DESC)
        $columnasPermitidas = ['id', 'status', 'tipo', 'created_at'];
        
        $sortBy = $request->input('sort_by', 'created_at'); 
        $sortDir = $request->input('sort_dir', 'desc');

        if (!in_array($sortBy, $columnasPermitidas)) $sortBy = 'created_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        // Paginamos los resultados
        $requisiciones = $query->orderBy($sortBy, $sortDir)->paginate(50);

        return view('requisiciones.index', compact('requisiciones'));
    }

    public function show($id)
    {
        $requisicion = Requisicion::with('detalles')->findOrFail($id);
        return view('requisiciones.show', compact('requisicion'));
    }

    public function edit($id)
    {
        $requisicion = Requisicion::with('detalles.productoCatalogo')->findOrFail($id);

        if (in_array($requisicion->status, ['Completado', 'Cancelado'])) {
            return back()->with('error', 'Las requisiciones en estado ' . $requisicion->status . ' no pueden ser modificadas.');
        }

        if ($requisicion->status === 'En Curso' && !in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion'])) {
            return back()->with('error', 'Solo Administración puede modificar una requisición En Curso.');
        }

        return view('requisiciones.edit', compact('requisicion'));
    }

    public function update(Request $request, $id)
    {
        $requisicion = Requisicion::findOrFail($id);

        if ($requisicion->status === 'Completado') abort(403, 'Acción denegada.');

        if ($requisicion->status === 'Pendiente') {
            $validated = $request->validate([
                'tipo' => 'required|in:Requisicion,Devolucion',
                'nro_tecnico' => 'required|string',
                'nombre_tecnico' => 'required|string',
                'status' => 'required|in:Pendiente,En Curso,Completado,Cancelado',
                'productos' => 'required|array|min:1',
                'productos.*.codigo' => 'required|string',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.observacion' => 'nullable|string',
            ]);

            DB::transaction(function () use ($requisicion, $validated) {
                $requisicion->update([
                    'tipo' => $validated['tipo'],
                    'nro_tecnico' => $validated['nro_tecnico'],
                    'nombre_tecnico' => $validated['nombre_tecnico'],
                    'status' => $validated['status']
                ]);
                
                $requisicion->detalles()->delete();
                
                $nuevosDetalles = array_map(function($prod) {
                    return [
                        'codigo_producto' => $prod['codigo'],
                        'cantidad' => $prod['cantidad'],
                        'observacion' => $prod['observacion'] ?? null,
                    ];
                }, $validated['productos']);
                
                $requisicion->detalles()->createMany($nuevosDetalles);
            });

            return redirect()->route('requisiciones.index')->with('success', 'Requisición actualizada (Productos modificados).');

        } elseif ($requisicion->status === 'En Curso') {
            if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion'])) abort(403);
            
            $request->validate(['status' => 'required|in:Cancelado']);
            
            $requisicion->update(['status' => 'Cancelado']);
            return redirect()->route('requisiciones.index')->with('success', 'La requisición En Curso ha sido Cancelada.');
        }
    }

    public function create()
    {
        return view('requisiciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:Requisicion,Devolucion',
            'nro_tecnico' => 'required|string|max:50',
            'nombre_tecnico' => 'required|string|max:100',
            'productos' => 'required|array|min:1',
            'productos.*.codigo' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.observacion' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $requisicion = Requisicion::create([
                'tipo' => $validated['tipo'],
                'nro_tecnico' => $validated['nro_tecnico'],
                'nombre_tecnico' => $validated['nombre_tecnico'],
                'status' => 'Pendiente',
                'creado_por_usuario_id' => Auth::id(),
            ]);

            $detallesParaGuardar = array_map(function($producto) {
                return [
                    'codigo_producto' => $producto['codigo'],
                    'cantidad' => $producto['cantidad'],
                    'observacion' => $producto['observacion'] ?? null,
                ];
            }, $validated['productos']);

            $requisicion->detalles()->createMany($detallesParaGuardar);
        });

        return redirect()->route('requisiciones.index')
                         ->with('success', '¡Requisición creada con éxito!');
    }

    public function avanzarStatus(Requisicion $requisicion, Request $request)
    {
        if (auth()->user()->rol === 'Produccion' || auth()->user()->rol === 'ServicioTecnico') {
            abort(403, 'Acceso denegado. Solo Almacén o Administración pueden avanzar el estado de las requisiciones.');
        }

        if (auth()->user()->rol === ['Administracion', 'SuperAdmin'] && $requisicion->status === 'En Curso') { 
            abort(403, 'Acceso denegado. Solo Administración puede preparar (avanzar a En Curso).');
        }
        
        $request->validate(['new_status' => 'required|in:En Curso,Completado,Cancelado']);

        // Usamos una transacción para asegurar que la requisición y el inventario se sincronicen
        DB::transaction(function () use ($requisicion, $request) {
            $requisicion->update(['status' => $request->new_status]);

            // ==========================================
            // LÓGICA DE INVENTARIO (EL KARDEX ENTRA EN ACCIÓN)
            // ==========================================
            if ($request->new_status === 'Completado') {
                foreach ($requisicion->detalles as $detalle) {
                    $producto = \App\Models\Producto::where('codigo_producto', $detalle->codigo_producto)->first();
                    
                    if ($producto) {
                        // Si es una Requisición normal, es una Salida. Si es una Devolución, es Entrada.
                        $tipoMovimiento = $requisicion->tipo === 'Devolucion' ? 'Entrada' : 'Salida';

                        MovimientoInventario::create([
                            'producto_id' => $producto->id,
                            'tipo_movimiento' => $tipoMovimiento,
                            'cantidad' => $detalle->cantidad,
                            'motivo' => "Material despachado/recibido en {$requisicion->tipo} #" . $requisicion->id,
                            'referencia_type' => Requisicion::class,
                            'referencia_id' => $requisicion->id,
                            'usuario_id' => auth()->id()
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'El estado de la requisición ha avanzado a: ' . $request->new_status . ' y el inventario fue actualizado.');
    }

    public function generarReporte($id)
    {
        $requisicion = Requisicion::with('detalles.productoCatalogo')->findOrFail($id);
        
        $pdf = Pdf::loadView('requisiciones.reporte', compact('requisicion'));
        return $pdf->stream("Requisicion_{$id}.pdf");
    }
}