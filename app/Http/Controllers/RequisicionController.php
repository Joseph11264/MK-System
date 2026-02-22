<?php
namespace App\Http\Controllers;

use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RequisicionController
{
    // Reemplaza a tu función consultar()
    public function index(Request $request)
    {
        $query = Requisicion::with('detalles')->orderBy('created_at', 'desc');

        // La función when() de Laravel solo aplica el filtro si el usuario realmente escribió algo
        $query->when($request->id, function ($q, $id) {
            return $q->where('id', $id);
        });
        
        $query->when($request->nro_tecnico, function ($q, $nro) {
            return $q->where('nro_tecnico', 'like', '%' . $nro . '%');
        });
        
        $query->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        });
        
        $query->when($request->fecha_inicio, function ($q, $fecha) {
            return $q->whereDate('created_at', '>=', $fecha);
        });
        
        $query->when($request->fecha_fin, function ($q, $fecha) {
            return $q->whereDate('created_at', '<=', $fecha);
        });

        $query->when($request->tipo, function ($q, $tipo) {
            return $q->where('tipo', $tipo);
        });

        $requisiciones = $query->paginate(50);
        return view('requisiciones.index', compact('requisiciones'));
    }

    // 2. MÉTODO PARA VER DETALLES (Arregla el error de "Call to undefined method")
    public function show($id)
    {
        // findOrFail buscará el ID, y si alguien pone un ID falso en la URL, 
        // mostrará automáticamente la pantalla de Error 404
        $requisicion = Requisicion::with('detalles')->findOrFail($id);
        return view('requisiciones.show', compact('requisicion'));
    }

    // 3. MÉTODO PARA MOSTRAR FORMULARIO DE EDICIÓN
    public function edit($id)
    {
        $requisicion = Requisicion::with('detalles.productoCatalogo')->findOrFail($id);

        // REGLA 1: Si está completado, nadie puede editar
        if (in_array($requisicion->status, ['Completado', 'Cancelado'])) {
            return back()->with('error', 'Las requisiciones en estado ' . $requisicion->status . ' no pueden ser modificadas.');
        }

        // REGLA 2: Si está en curso, solo Admin/SuperAdmin pueden entrar a editar
        if ($requisicion->status === 'En Curso' && !in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion'])) {
            return back()->with('error', 'Solo Administración puede modificar una requisición En Curso.');
        }

        return view('requisiciones.edit', compact('requisicion'));
    }

    // 4. MÉTODO PARA GUARDAR LOS CAMBIOS DE LA EDICIÓN
    public function update(Request $request, $id)
    {
        $requisicion = Requisicion::findOrFail($id);

        // Protecciones de seguridad backend
        if ($requisicion->status === 'Completado') abort(403, 'Acción denegada.');

        if ($requisicion->status === 'Pendiente') {
            // SI ESTÁ PENDIENTE: Puede editar TODO (Encabezado y Productos)
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
                // 1. Actualiza encabezado
                $requisicion->update([
                    'tipo' => $validated['tipo'],
                    'nro_tecnico' => $validated['nro_tecnico'],
                    'nombre_tecnico' => $validated['nombre_tecnico'],
                    'status' => $validated['status']
                ]);
                
                // 2. Borra productos viejos y guarda los nuevos (Permite añadir/eliminar)
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
            // SI ESTÁ EN CURSO: Solo puede CANCELARLA
            if (!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion'])) abort(403);
            
            $request->validate(['status' => 'required|in:Cancelado']); // Solo permite enviar "Cancelado"
            
            $requisicion->update(['status' => 'Cancelado']);
            return redirect()->route('requisiciones.index')->with('success', 'La requisición En Curso ha sido Cancelada.');
        }
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
            'tipo' => 'required|in:Requisicion,Devolucion',
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
                'tipo' => $validated['tipo'],
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

        public function generarReporte($id)
{
    // Buscamos la requisición con sus detalles y productos
    $requisicion = Requisicion::with('detalles')->findOrFail($id);

    $requisicion = Requisicion::with('detalles.productoCatalogo')->findOrFail($id);
    
    // Cargamos la vista específica para el PDF y le pasamos los datos
    $pdf = Pdf::loadView('requisiciones.reporte', compact('requisicion'));
    
    // Retornamos el PDF para que se abra en el navegador
    return $pdf->stream("Requisicion_{$id}.pdf");
    }
}