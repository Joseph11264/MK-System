<?php

namespace App\Http\Controllers;

use App\Models\RequisicionSt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // Generador de número automático
        $ultimoTicket = RequisicionSt::orderBy('id', 'desc')->first();
        $nuevoNroOrden = ($ultimoTicket && is_numeric($ultimoTicket->nro_orden_st)) ? $ultimoTicket->nro_orden_st + 1 : 12600;

        // VALIDACIÓN: Aquí es donde agregamos 'tipo_st' para que no dé el error
        $validated = $request->validate([
            'tipo_st' => 'required|in:Reparacion,Garantia', 
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

        DB::transaction(function () use ($validated, $nuevoNroOrden) {
            $ticket = RequisicionSt::create([
                'nro_orden_st' => (string) $nuevoNroOrden,
                'tipo_st' => $validated['tipo_st'], // Ya no dará error
                'cliente' => $validated['cliente'],
                'telefono_cliente' => $validated['telefono_cliente'] ?? null,
                'correo_cliente' => $validated['correo_cliente'] ?? null,
                'codigo_equipo' => $validated['codigo_equipo'],
                'tecnico_asignado_id' => $validated['tecnico_asignado_id'],
                'usuario_creador_id' => Auth::id(),
                'status' => 'Pendiente',
                'materiales_entregados' => false
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

        return redirect()->route('st.index')->with('success', 'Ticket ST creado. Orden: ' . $nuevoNroOrden);
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
        return view('st.show', compact('ticket'));
    }

    public function edit($id)
    {
        $ticket = RequisicionSt::with('detalles.productoCatalogo')->findOrFail($id);

        // REGLA 1: Si está completado o cancelado, bloqueo total
        if (in_array($ticket->status, ['Completado', 'Cancelado'])) {
            return back()->with('error', 'Los tickets finalizados no pueden ser modificados.');
        }

        // REGLA 2: Si está En Curso, solo personal autorizado puede entrar
        if ($ticket->status === 'En Curso' && !in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico'])) {
            return back()->with('error', 'No tienes permisos para modificar este ticket En Curso.');
        }

        $tecnicos = User::whereIn('rol', ['ServicioTecnico', 'SuperAdmin'])->get();
        return view('st.edit', compact('ticket', 'tecnicos'));
    }

    public function update(Request $request, $id)
    {
        $ticket = RequisicionSt::findOrFail($id);

        // 1. FLUJO: Confirmar Pago (Aplica para Reparaciones)
        if ($request->has('confirmar_pago') && $ticket->status === 'Completado') {
            $request->validate(['referencia_pago' => 'required|string|max:255']);
            $ticket->update([
                'estado_pago' => 'Pagado',
                'referencia_pago' => $request->referencia_pago
            ]);
            return back()->with('success', 'Pago confirmado y referencia guardada exitosamente.');
        }

        // 2. FLUJO: Guardar Datos de Cierre (Materiales y Precio)
        if ($request->has('guardar_datos_cierre')) {
            // Si es garantía, ignoramos lo que envíe el formulario y ponemos 0
            $precio = $ticket->tipo_st === 'Garantia' ? 0 : $request->precio_reparacion;
            
            $ticket->update([
                'materiales_entregados' => $request->materiales_entregados,
                'precio_reparacion' => $precio
            ]);
            return back()->with('success', 'Datos de cierre (Materiales y Precio) actualizados.');
        }

        // 3. FLUJO: Edición Completa de Repuestos
        if ($ticket->status === 'Pendiente') {
            
            // VALIDACIÓN: También debe estar aquí el 'tipo_st'
            $validated = $request->validate([
                'tipo_st' => 'required|in:Reparacion,Garantia',
                'cliente' => 'required|string',
                'codigo_equipo' => 'required|string', 
                'tecnico_asignado_id' => 'nullable|exists:usuarios,id',
                'status' => 'required|in:Pendiente,Completado,Cancelado',
                'productos' => 'required|array|min:1',
                'productos.*.codigo' => 'required|string',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.observacion' => 'nullable|string',
            ]);

            $actuales = $ticket->detalles->map(fn($d) => $d->codigo_producto . ':' . $d->cantidad)->sort()->values()->toArray();
            $nuevos = collect($request->productos)->map(fn($p) => $p['codigo'] . ':' . $p['cantidad'])->sort()->values()->toArray();
            
            $materiales = $ticket->materiales_entregados;
            $precio = $ticket->precio_reparacion;
            if ($actuales !== $nuevos) {
                $materiales = false; 
                $precio = null;
            }

            DB::transaction(function () use ($ticket, $validated, $materiales, $precio) {
                $ticket->update([
                    'tipo_st' => $validated['tipo_st'], // Ya no dará error
                    'cliente' => $validated['cliente'],
                    'codigo_equipo' => $validated['codigo_equipo'],
                    'tecnico_asignado_id' => $validated['tecnico_asignado_id'],
                    'status' => $validated['status'],
                    'materiales_entregados' => $materiales,
                    'precio_reparacion' => $precio
                ]);

                $ticket->detalles()->delete();
                $nuevosDetalles = array_map(function($prod) {
                    return ['codigo_producto' => $prod['codigo'], 'cantidad' => $prod['cantidad'], 'observacion' => $prod['observacion'] ?? null];
                }, $validated['productos']);
                $ticket->detalles()->createMany($nuevosDetalles);
            });
            return redirect()->route('st.index')->with('success', 'Ticket ST actualizado.');
        }
    }
        public function avanzarStatus(Request $request, $id)
        {
        $ticket = RequisicionSt::findOrFail($id);
        
        if (!$ticket->materiales_entregados) {
            return back()->with('error', '⚠️ Debes entregar los materiales antes de completar el servicio.');
        }

        // Si es Reparación, exigimos el precio
        if ($ticket->tipo_st === 'Reparacion') {
            if (empty($ticket->precio_reparacion) || $ticket->precio_reparacion <= 0) {
                return back()->with('error', '⚠️ Las Reparaciones requieren establecer un Precio antes de completarse.');
            }
        } else {
            // Si es Garantía, forzamos el precio a 0 y lo autoliquidamos
            $ticket->precio_reparacion = 0;
            $ticket->estado_pago = 'Pagado';
            $ticket->referencia_pago = 'Garantía (Sin Costo)';
        }

        $ticket->status = 'Completado';
        $ticket->save();

        return back()->with('success', '✅ Servicio Técnico finalizado correctamente.');
    }

     
}