<?php

namespace App\Http\Controllers;

use App\Models\RequisicionSt;
use App\Models\User;
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
        // Calculamos el próximo número exacto para mostrarlo en la vista
        $ultimoTicket = RequisicionSt::orderBy('id', 'desc')->first();
        
        if ($ultimoTicket && is_numeric($ultimoTicket->nro_orden_st)) {
            $proximoNro = $ultimoTicket->nro_orden_st + 1;
        } else {
            $proximoNro = 12600; // El número inicial
        }

        $tecnicos = User::whereIn('rol', ['ServicioTecnico', 'SuperAdmin'])->get();
        
        // Enviamos la variable $proximoNro a la vista
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
            // CORREGIDO: Tabla 'usuarios' en lugar de 'users'
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
            'status' => 'Pendiente', // Solo Pendiente
            'materiales_entregados' => false,
            'precio_reparacion' => 0
        ]);

        return redirect()->route('st.show', $ticket->id)->with('success', '✅ Orden de ST #' . $nuevoNroOrden . ' creada exitosamente. Imprima el recibo.');
    }
    
    public function generarReporte($id)
    {
    // Buscamos el ticket con sus relaciones
    $ticket = RequisicionSt::with(['detalles', 'tecnico', 'creador'])->findOrFail($id);
    
    // Cargamos la vista del reporte
    $pdf = Pdf::loadView('st.reporte', compact('ticket'));
    
    // Retornamos el PDF para visualización
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

        // 1. FLUJO: Reasignar Técnico Rápido
        if ($request->has('reasignar_tecnico') && in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion'])) {
            $ticket->update(['tecnico_asignado_id' => $request->tecnico_asignado_id]);
            return back()->with('success', '✅ Técnico reasignado correctamente.');
        }

        // 2. FLUJO: Confirmar Pago
        if ($request->has('confirmar_pago') && $ticket->status === 'Completado') {
            $request->validate(['referencia_pago' => 'required|string|max:255']);
            $ticket->update([
                'estado_pago' => 'Pagado',
                'referencia_pago' => $request->referencia_pago
            ]);
            return back()->with('success', 'Pago confirmado y referencia guardada exitosamente.');
        }

        // ========================================================
        // 3. FLUJO: Guardar Diagnóstico y Precio (SOLO TÉCNICOS)
        // ========================================================
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

        // ========================================================
        // 4. FLUJO: Despacho de Materiales (SOLO ALMACÉN)
        // ========================================================
        if ($request->has('guardar_materiales')) {
            if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Almacen'])) abort(403);
            $ticket->update([
                'materiales_entregados' => $request->materiales_entregados
            ]);
            $estado = $request->materiales_entregados ? 'entregados al técnico' : 'marcados como NO entregados';
            return back()->with('success', '📦 Repuestos ' . $estado . '.');
        }

        // 5. FLUJO: Edición de Revisión y Repuestos (Desde el botón "Realizar Revisión")
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

            // LÓGICA INTELIGENTE: Comparamos los repuestos viejos con los nuevos
            // Convertimos ambas listas a formato "codigo:cantidad" para compararlas exactamente
            $actuales = $ticket->detalles->map(fn($d) => $d->codigo_producto . ':' . $d->cantidad)->sort()->values()->toArray();
            $nuevos = collect($request->productos ?? [])->map(fn($p) => $p['codigo'] . ':' . $p['cantidad'])->sort()->values()->toArray();
            
            $materiales_entregados = $ticket->materiales_entregados;
            $mensaje_extra = '';

            // Si hubo AL GÚN CAMBIO en los repuestos, quitamos la marca de "Entregado"
            if ($actuales !== $nuevos) {
                $materiales_entregados = false; // El candado de seguridad se activa
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
                    'materiales_entregados' => $materiales_entregados // <-- AQUÍ SE GUARDA EL RESETEO
                ]);

                // Borramos los viejos y guardamos la nueva lista
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

        $ticket->status = 'Completado';
        $ticket->save();

        // ==========================================
        // LÓGICA DE ENVÍO DE CORREO AUTOMÁTICO
        // ==========================================
        if (!empty($ticket->correo_cliente)) {
            try {
                // 1. Generamos el PDF "al vuelo" (en memoria)
                $pdf = Pdf::loadView('st.reporte', compact('ticket'));
                
                // 2. Intentamos enviar el correo
                Mail::to($ticket->correo_cliente)->send(new TicketCompletadoMail($ticket, $pdf->output()));
                
                $mensajeCorreo = ' y el comprobante PDF fue enviado al cliente.';
            } catch (\Exception $e) {
                // Si falla (por estar en local o sin internet), no se cae el sistema.
                \Log::error('Error enviando PDF al cliente: ' . $e->getMessage());
                $mensajeCorreo = ', pero el correo no pudo ser enviado (Modo Local).';
            }
        } else {
            $mensajeCorreo = ' (El cliente no registró correo electrónico).';
        }

        return back()->with('success', '✅ Servicio Técnico finalizado' . $mensajeCorreo);
    }

     
}