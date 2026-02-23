@extends('layouts.app')

@section('titulo', 'Detalle Ticket ST #' . $ticket->id)

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔎 Detalle Ticket ST #{{ $ticket->nro_orden_st }}</h2>
        <div>
            @if($ticket->status === 'Pendiente' && auth()->user()->rol !== 'Almacen')
                <a href="{{ route('st.edit', $ticket->id) }}" class="btn btn-warning fw-bold text-dark shadow-sm me-2">🛠️ Realizar Revisión (Añadir Repuestos)</a>
            @endif
            <a href="{{ route('st.index') }}" class="btn btn-outline-danger fw-bold">Volver al listado</a>
        </div>
    </div>
    <hr>

   @if($ticket->status === 'Completado')
    <div class="card mb-4 border-{{ $ticket->estado_pago === 'Pagado' ? 'success' : 'warning' }} shadow-sm">
        <div class="card-header bg-{{ $ticket->estado_pago === 'Pagado' ? 'success' : 'warning' }} text-{{ $ticket->estado_pago === 'Pagado' ? 'white' : 'body' }} fw-bold d-flex justify-content-between align-items-center">
            <span>💰 Estado de Facturación y Cobro</span>
            <span class="badge bg-light text-dark fs-6">{{ $ticket->estado_pago }}</span>
        </div>
        <div class="card-body bg-body-secondary d-flex justify-content-between align-items-center">
            <div>
                <p class="mb-1 fs-5"><strong>Monto a Cobrar:</strong> <span class="text-success">${{ number_format($ticket->precio_reparacion, 2) }}</span></p>
                @if($ticket->estado_pago === 'Pagado')
                    <p class="mb-0 mt-2 fs-5 text-dark border border-primary p-2 bg-white rounded shadow-sm d-inline-block">
                        <strong>🧾 Ref. de Pago:</strong> <span class="text-primary fw-bold">{{ $ticket->referencia_pago ?? 'No especificada' }}</span>
                    </p>
                @endif
            </div>
            
            @if($ticket->estado_pago === 'Pendiente')
                <form id="formPago" action="{{ route('st.update', $ticket->id) }}" method="POST" class="d-none">
                    @csrf @method('PUT')
                    <input type="hidden" name="confirmar_pago" value="1">
                    <input type="hidden" name="referencia_pago" id="inputReferencia">
                </form>
                
                <button type="button" class="btn btn-success fw-bold btn-lg shadow-sm" onclick="solicitarReferencia()">
                    💳 Confirmar Pago
                </button>
                
                <script>
                    function solicitarReferencia() {
                        let referencia = prompt("Por favor, ingresa el número de referencia de la transferencia o método de pago:");
                        if (referencia != null && referencia.trim() !== "") {
                            document.getElementById('inputReferencia').value = referencia;
                            document.getElementById('formPago').submit();
                        } else if (referencia !== null) {
                            alert("⚠️ La referencia es obligatoria para registrar el pago.");
                        }
                    }
                </script>
            @else
                <span class="badge bg-success fs-5 px-4 py-2">✅ PAGADO</span>
            @endif
        </div>
    </div>
    @endif

    @if($ticket->status === 'Pendiente')
    <div class="row mb-4">
        
        @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Almacen']))
        <div class="col-md-5 mb-3 mb-md-0">
            <div class="card border-warning shadow-sm h-100">
                <div class="card-header bg-warning text-body fw-bold">
                    📦 Despacho de Materiales (Almacén)
                </div>
                <div class="card-body bg-body-secondary">
                    <form action="{{ route('st.update', $ticket->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="guardar_materiales" value="1">
                        
                        <label class="fw-bold d-block mb-3">¿Se entregaron los repuestos al técnico?</label>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="materiales_entregados" id="matNo" value="0" {{ !$ticket->materiales_entregados ? 'checked' : '' }}>
                            <label class="form-check-label text-danger fw-bold" for="matNo">❌ No Entregados (Pendiente)</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="materiales_entregados" id="matSi" value="1" {{ $ticket->materiales_entregados ? 'checked' : '' }}>
                            <label class="form-check-label text-success fw-bold" for="matSi">✅ Entregados al Técnico</label>
                        </div>
                        
                        <button type="submit" class="btn btn-warning fw-bold w-100 text-dark shadow-sm">Confirmar Despacho</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico']))
        <div class="col-md-7">
            <div class="card border-info shadow-sm h-100">
                <div class="card-header bg-info text-body fw-bold">
                    📝 Diagnóstico y Precio (Técnico)
                </div>
                <div class="card-body bg-body-secondary">
                    <form action="{{ route('st.update', $ticket->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="guardar_diagnostico" value="1">
                        
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Diagnóstico de la Falla / Reparación Efectuada (*):</label>
                            <textarea name="diagnostico" class="form-control border-info" rows="3" placeholder="Describe detalladamente el estado del equipo..." required>{{ $ticket->diagnostico }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold mb-1">Precio de Reparación ($):</label>
                            @if($ticket->tipo_st === 'Garantia')
                                <input type="text" class="form-control fw-bold text-success bg-body-secondary" value="0.00 (Cubierto por Garantía)" disabled>
                                <input type="hidden" name="precio_reparacion" value="0">
                            @else
                                <input type="number" step="0.01" min="0" name="precio_reparacion" class="form-control fw-bold text-success border-success" value="{{ $ticket->precio_reparacion }}" placeholder="Ej: 45.50" required>
                            @endif
                        </div>
                        
                        <button type="submit" class="btn btn-info fw-bold w-100 text-dark shadow-sm">💾 Guardar Diagnóstico y Precio</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
    @endif

    <div class="card shadow-sm mb-4 border-start border-5 border-danger bg-body-secondary">
        <div class="card-body py-2">
            <h5 class="text-danger fw-bold mb-1">Falla Reportada por el Cliente al recibir:</h5>
            <p class="mb-0 fs-5 text-body"><i>"{{ $ticket->falla_reportada ?? 'No se registraron observaciones iniciales.' }}"</i></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold">Información del Cliente</div>
                
                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']) && $ticket->status !== 'Completado')
                <div class="card mb-4 border-warning shadow-sm">
                    <div class="card-body bg-body-secondary py-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-body"><i class="text-warning">⚠️</i> Reasignación de Técnico</span>
                        
                        <form action="{{ route('st.update', $ticket->id) }}" method="POST" class="d-flex align-items-center gap-2 m-0">
                            @csrf @method('PUT')
                            <input type="hidden" name="reasignar_tecnico" value="1">
                            <select name="tecnico_asignado_id" class="form-select form-select-sm border-warning fw-bold text-body" style="min-width: 200px;">
                                <option value="">-- Sin asignar --</option>
                                @foreach($tecnicos as $tec)
                                    <option value="{{ $tec->id }}" {{ $ticket->tecnico_asignado_id == $tec->id ? 'selected' : '' }}>
                                        {{ $tec->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-warning btn-sm fw-bold text-body">Cambiar</button>
                        </form>
                    </div>
                </div>
                @endif
                
                <div class="card-body">
                    <p class="mb-2"><strong>Cliente:</strong> {{ $ticket->cliente }}</p>
                    <p class="mb-2"><strong>Teléfono:</strong> {{ $ticket->telefono_cliente ?: 'No registrado' }}</p>
                    <p class="mb-2"><strong>Correo:</strong> {{ $ticket->correo_cliente ?: 'No registrado' }}</p>
                    <p class="mb-0"><strong>Nro. Orden ST:</strong> <span class="badge bg-dark fs-6">{{ $ticket->nro_orden_st }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 border-start border-5 border-info">
                <div class="card-header bg-info text-white fw-bold">Estado y Asignación</div>
                <div class="card-body">
                    <p class="mb-2"><strong>Equipo (Serial):</strong> <span class="fw-bold text-info">{{ $ticket->codigo_equipo }}</span></p>
                    <p class="mb-2"><strong>Técnico Responsable:</strong> {{ $ticket->tecnico->nombre ?? 'Pendiente por asignar' }}</p>
                    <p class="mb-2"><strong>Fecha de Ingreso:</strong> {{ $ticket->created_at->format('Y-m-d H:i') }}</p>
                    <div class="d-flex align-items-center gap-2">
                        <strong>Estado:</strong>
                        @php
                            $badgeColor = 'bg-secondary'; // Gris por defecto para Cancelado
                            if($ticket->status === 'Pendiente') $badgeColor = 'bg-danger text-white'; // Rojo
                            elseif($ticket->status === 'En Curso') $badgeColor = 'bg-warning text-dark'; // Amarillo
                            elseif($ticket->status === 'Completado') $badgeColor = 'bg-success text-white'; // Verde
                        @endphp
                        <span class="badge {{ $badgeColor }} fs-6 shadow-sm border border-light border-opacity-10">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <h3 class="h5 text-primary mb-3">🛠️ Fallas y Repuestos Asignados por el Técnico</h3>
    
    <div class="table-responsive bg-body shadow-sm rounded border mb-4">
        <table class="table table-bordered mb-0 align-middle">
            <thead class="table-secondary">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th style="width: 15%;">Código</th>
                    <th style="width: 35%;">Descripción en Catálogo</th>
                    <th class="text-center" style="width: 10%;">Cant.</th>
                    <th style="width: 35%;">Observación / Falla</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticket->detalles as $index => $detalle)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $detalle->codigo_producto }}</td>
                    <td>
                        @if($detalle->productoCatalogo)
                            <span class="text-success fw-bold">✅ {{ $detalle->productoCatalogo->descripcion }}</span>
                        @else
                            <span class="text-danger fw-bold">⚠️ Producto no añadido al sistema</span>
                        @endif
                    </td>
                    <td class="text-center fw-bold fs-5 text-primary">{{ $detalle->cantidad }}</td>
                    <td class="text-muted">{{ $detalle->observacion ?: 'Ver revisión técnica' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted fs-5">El técnico no ha añadido repuestos a esta orden todavía.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('st.reporte', $ticket->id) }}" target="_blank" class="btn btn-primary fw-bold">
            Imprimir Reporte 📄
        </a>
    </div>

</section>
@endsection