<?php

namespace App\Http\Controllers;

use App\Models\RequisicionSt;
use App\Models\User;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCompletadoMail;

class ServicioTecnicoController
{
    public function index(Request $request)
    {
        $query = RequisicionSt::with(['creador', 'tecnico', 'detalles']);

        // 1. APLICAR FILTROS DE BÚSQUEDA
        if ($request->filled('orden')) {
            $query->where('nro_orden_st', 'like', '%' . $request->orden . '%');
        }
        if ($request->filled('cliente')) {
            $query->where('cliente', 'like', '%' . $request->cliente . '%');
        }
        if ($request->filled('equipo')) {
            $query->where('codigo_equipo', 'like', '%' . $request->equipo . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tipo_st')) {
            $query->where('tipo_st', $request->tipo_st);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $columnasPermitidas = ['nro_orden_st', 'cliente', 'codigo_equipo', 'status', 'tipo_st', 'created_at'];
        
        $sortBy = $request->input('sort_by', 'created_at'); 
        $sortDir = $request->input('sort_dir', 'desc');

        if (!in_array($sortBy, $columnasPermitidas)) $sortBy = 'created_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';


        $tickets = $query->orderBy($sortBy, $sortDir)->paginate(50);
            
        return view('st.index', compact('tickets'));
    }

    public function create()
    {
        if (auth()->user()->rol === ['Almacen', 'Produccion']) {
            abort(403, 'Almacén solo puede despachar materiales desde la lupa (Ver Detalle).');
        }
        $ultimoTicket = RequisicionSt::orderBy('id', 'desc')->first();
        
        if ($ultimoTicket && is_numeric($ultimoTicket->nro_orden_st)) {
            $proximoNro = $ultimoTicket->nro_orden_st + 1;
        } else {
            $proximoNro = 12600; 
        }

        $tecnicos = User::whereIn('rol', ['ServicioTecnico', 'SuperAdmin'])->get();
        
        return view('st.create', compact('tecnicos', 'proximoNro'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->rol === ['Almacen', 'Produccion']) {
            abort(403, 'Almacén solo puede despachar materiales desde la lupa (Ver Detalle).');
        }

        $ultimoTicket = RequisicionSt::orderBy('id', 'desc')->first();
        $nuevoNroOrden = ($ultimoTicket && is_numeric($ultimoTicket->nro_orden_st)) ? $ultimoTicket->nro_orden_st + 1 : 12600;

        $validated = $request->validate([
            'tipo_st' => 'required|in:Reparacion,Garantia', 
            'cliente' => 'required|string',
            'telefono_cliente' => 'nullable|string|max:20', 
            'correo_cliente' => 'nullable|email|max:100', 
            'codigo_equipo' => 'required|string',
            'falla_reportada' => 'required|string',
            'tecnico_asignado_id' => 'nullable|exists:usuarios,id' 
        ]);

        $ticket = RequisicionSt::create([
            'nro_orden_st' => (string) $nuevoNroOrden,
            'tipo_st' => $validated['tipo_st'],
            'cliente' => $validated['cliente'],
            'telefono_cliente' => $validated['telefono_cliente'] ?? null,
            'correo_cliente' => $validated['correo_cliente'] ?? null,
            'codigo_equipo' => $validated['codigo_equipo'],
            'falla_reportada' => $validated['falla_reportada'],
            'tecnico_asignado_id' => $validated['tecnico_asignado_id'],
            'usuario_creador_id' => auth()->id(),
            'status' => 'Pendiente', 
            'materiales_entregados' => false,
            'precio_reparacion' => 0
        ]);

        return redirect()->route('st.show', $ticket->id)->with('success', '✅ Orden de ST #' . $nuevoNroOrden . ' creada exitosamente. Imprima el recibo.');
    }
    
    public function generarReporte($id)
    {
        $ticket = RequisicionSt::with(['detalles', 'tecnico', 'creador'])->findOrFail($id);
        $pdf = Pdf::loadView('st.reporte', compact('ticket'));
        return $pdf->stream("Ticket_ST_{$ticket->nro_orden_st}.pdf");
    }

    public function show($id)
    {
        $ticket = RequisicionSt::with(['creador', 'tecnico', 'detalles'])->findOrFail($id);
        $tecnicos = \App\Models\User::whereIn('rol', ['ServicioTecnico', 'SuperAdmin'])->orderBy('nombre', 'asc')->get();
        
        return view('st.show', compact('ticket', 'tecnicos'));
    }

    public function edit($id)
    {
        if (auth()->user()->rol === ['Almacen', 'Produccion']) {
            abort(403, 'Acceso denegado. Producción y Almacén solo pueden consultar el catálogo.');
        }

        $ticket = RequisicionSt::with('detalles.productoCatalogo')->findOrFail($id);

        if (in_array($ticket->status, ['Completado', 'Cancelado'])) {
            return back()->with('error', 'Los tickets finalizados no pueden ser modificados.');
        }

        $tecnicos = \App\Models\User::whereIn('rol', ['ServicioTecnico', 'SuperAdmin'])->orderBy('nombre', 'asc')->get();
        return view('st.edit', compact('ticket', 'tecnicos'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->rol === 'Produccion') {
            abort(403, 'Acceso denegado. Producción y Almacén solo pueden consultar el catálogo.');
        }
        
        $ticket = RequisicionSt::findOrFail($id);

        if ($request->has('reasignar_tecnico') && in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion'])) {
            $ticket->update(['tecnico_asignado_id' => $request->tecnico_asignado_id]);
            return back()->with('success', '✅ Técnico reasignado correctamente.');
        }

        if ($request->has('confirmar_pago') && $ticket->status === 'Completado') {
            $request->validate(['referencia_pago' => 'required|string|max:255']);
            $ticket->update([
                'estado_pago' => 'Pagado',
                'referencia_pago' => $request->referencia_pago
            ]);
            return back()->with('success', 'Pago confirmado y referencia guardada exitosamente.');
        }

        if ($request->has('guardar_diagnostico')) {
            if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico'])) abort(403);
            $request->validate(['diagnostico' => 'required|string']);
            $precio = $ticket->tipo_st === 'Garantia' ? 0 : $request->precio_reparacion;
            
            $ticket->update([
                'precio_reparacion' => $precio,
                'diagnostico' => $request->diagnostico
            ]);
            return back()->with('success', '✅ Diagnóstico técnico y precio actualizados.');
        }

        if ($request->has('guardar_materiales')) {
            if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Almacen'])) abort(403);
            $ticket->update([
                'materiales_entregados' => $request->materiales_entregados
            ]);
            $estado = $request->materiales_entregados ? 'entregados al técnico' : 'marcados como NO entregados';
            return back()->with('success', '📦 Repuestos ' . $estado . '.');
        }

        if ($request->has('actualizar_ticket')) {
            
            $validated = $request->validate([
                'cliente' => 'required|string',
                'codigo_equipo' => 'required|string', 
                'falla_reportada' => 'required|string',
                'tecnico_asignado_id' => 'nullable|exists:usuarios,id',
                'status' => 'required|in:Pendiente,Completado,Cancelado',
                'productos' => 'nullable|array',
                'productos.*.codigo' => 'required_with:productos|string',
                'productos.*.cantidad' => 'required_with:productos|integer|min:1',
                'productos.*.observacion' => 'nullable|string',
            ]);

            $actuales = $ticket->detalles->map(fn($d) => $d->codigo_producto . ':' . $d->cantidad)->sort()->values()->toArray();
            $nuevos = collect($request->productos ?? [])->map(fn($p) => $p['codigo'] . ':' . $p['cantidad'])->sort()->values()->toArray();
            
            $materiales_entregados = $ticket->materiales_entregados;
            $mensaje_extra = '';

            if ($actuales !== $nuevos) {
                $materiales_entregados = false; 
                $mensaje_extra = ' ⚠️ Se modificaron los repuestos, por lo que Almacén debe confirmar el despacho nuevamente.';
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($ticket, $validated, $request, $materiales_entregados) {
                $ticket->update([
                    'cliente' => $validated['cliente'],
                    'telefono_cliente' => $request->telefono_cliente,
                    'correo_cliente' => $request->correo_cliente,
                    'codigo_equipo' => $validated['codigo_equipo'],
                    'falla_reportada' => $validated['falla_reportada'],
                    'tecnico_asignado_id' => $validated['tecnico_asignado_id'],
                    'status' => $validated['status'],
                    'materiales_entregados' => $materiales_entregados 
                ]);

                $ticket->detalles()->delete();
                if ($request->has('productos')) {
                    $nuevosDetalles = array_map(function($prod) {
                        return [
                            'codigo_producto' => $prod['codigo'], 
                            'cantidad' => $prod['cantidad'], 
                            'observacion' => $prod['observacion'] ?? null
                        ];
                    }, $validated['productos']);
                    $ticket->detalles()->createMany($nuevosDetalles);
                }
            });
            
            return redirect()->route('st.show', $ticket->id)
                             ->with('success', '✅ Revisión técnica actualizada.' . $mensaje_extra);
        }
    }
    
    public function avanzarStatus(Request $request, $id)
    {
        if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion'])) {
            abort(403, 'Acceso denegado. Solo Administración puede completar y cerrar los tickets.');
        }

        $ticket = RequisicionSt::with('detalles.productoCatalogo')->findOrFail($id);
        
        if (!$ticket->materiales_entregados) {
            return back()->with('error', '⚠️ Debes entregar los materiales antes de completar el servicio.');
        }

        if (empty($ticket->diagnostico)) {
            return back()->with('error', '⚠️ El Diagnóstico Técnico es obligatorio para completar el servicio.');
        }

        if ($ticket->tipo_st === 'Reparacion') {
            if (empty($ticket->precio_reparacion) || $ticket->precio_reparacion <= 0) {
                return back()->with('error', '⚠️ Las Reparaciones requieren establecer un Precio antes de completarse.');
            }
        } else {
            $ticket->precio_reparacion = 0;
            $ticket->estado_pago = 'Pagado';
            $ticket->referencia_pago = 'Garantía (Sin Costo)';
        }

        // Usamos una transacción para guardar y descontar inventario
        DB::transaction(function () use ($ticket) {
            $ticket->status = 'Completado';
            $ticket->save();

            // ==========================================
            // LÓGICA DE INVENTARIO (EL KARDEX ENTRA EN ACCIÓN)
            // ==========================================
            foreach ($ticket->detalles as $detalle) {
                $producto = \App\Models\Producto::where('codigo_producto', $detalle->codigo_producto)->first();
                
                if ($producto) {
                    MovimientoInventario::create([
                        'producto_id' => $producto->id,
                        'tipo_movimiento' => 'Salida',
                        'cantidad' => $detalle->cantidad, 
                        'motivo' => "Repuestos consumidos en Ticket ST #" . $ticket->id,
                        'referencia_type' => RequisicionSt::class,
                        'referencia_id' => $ticket->id,
                        'usuario_id' => auth()->id()
                    ]);
                }
            }
        });

        // ==========================================
        // LÓGICA DE ENVÍO DE CORREO AUTOMÁTICO
        // ==========================================
        if (!empty($ticket->correo_cliente)) {
            try {
                $pdf = Pdf::loadView('st.reporte', compact('ticket'));
                Mail::to($ticket->correo_cliente)->send(new TicketCompletadoMail($ticket, $pdf->output()));
                $mensajeCorreo = ' y el comprobante PDF fue enviado al cliente.';
            } catch (\Exception $e) {
                \Log::error('Error enviando PDF al cliente: ' . $e->getMessage());
                $mensajeCorreo = ', pero el correo no pudo ser enviado (Modo Local).';
            }
        } else {
            $mensajeCorreo = ' (El cliente no registró correo electrónico).';
        }

        return back()->with('success', '✅ Servicio Técnico finalizado. Inventario actualizado' . $mensajeCorreo);
    }
}