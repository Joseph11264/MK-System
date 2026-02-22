@extends('layouts.app')

@section('titulo', 'Detalle Ticket ST #' . $ticket->id)

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔎 Detalle Ticket ST #{{ $ticket->id }}</h2>
        <a href="{{ route('st.index') }}" class="btn btn-outline-danger fw-bold">Volver al listado</a>
    </div>
    <hr>

   @if($ticket->status === 'Completado')
    <div class="card mb-4 border-{{ $ticket->estado_pago === 'Pagado' ? 'success' : 'warning' }} shadow-sm">
        <div class="card-header bg-{{ $ticket->estado_pago === 'Pagado' ? 'success' : 'warning' }} text-{{ $ticket->estado_pago === 'Pagado' ? 'white' : 'dark' }} fw-bold d-flex justify-content-between align-items-center">
            <span>💰 Estado de Facturación y Cobro</span>
            <span class="badge bg-light text-dark fs-6">{{ $ticket->estado_pago }}</span>
        </div>
        <div class="card-body bg-light d-flex justify-content-between align-items-center">
            <div>
                <p class="mb-1 fs-5"><strong>Monto a Cobrar:</strong> <span class="text-success">${{ number_format($ticket->precio_reparacion, 2) }}</span></p>
                @if($ticket->estado_pago === 'Pagado')
                    <p class="mb-0 fs-5 text-dark border p-2 bg-white rounded shadow-sm">
                        <strong>🧾 Ref. de Pago:</strong> <span class="text-primary fw-bold">{{ $ticket->referencia_pago }}</span>
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
    <div class="card mb-4 border-info shadow-sm">
        <div class="card-header bg-info text-dark fw-bold">
            📦 Datos de Cierre (Materiales y Precio)
        </div>
        <div class="card-body bg-light">
            <form action="{{ route('st.update', $ticket->id) }}" method="POST" class="row align-items-center">
                @csrf @method('PUT')
                <input type="hidden" name="guardar_datos_cierre" value="1">
                
                <div class="col-md-5">
                    <label class="fw-bold d-block mb-2">Entrega de Materiales:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="materiales_entregados" id="matNo" value="0" {{ !$ticket->materiales_entregados ? 'checked' : '' }}>
                        <label class="form-check-label text-danger fw-bold" for="matNo">❌ No Entregados</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="materiales_entregados" id="matSi" value="1" {{ $ticket->materiales_entregados ? 'checked' : '' }}>
                        <label class="form-check-label text-success fw-bold" for="matSi">✅ Entregados</label>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="fw-bold mb-1">Precio de Reparación ($):</label>
                    @if($ticket->tipo_st === 'Garantia')
                        <input type="text" class="form-control fw-bold text-success bg-light" value="0.00 (Cubierto por Garantía)" disabled>
                        <input type="hidden" name="precio_reparacion" value="0">
                    @else
                        <input type="number" step="0.01" min="0" name="precio_reparacion" class="form-control fw-bold text-success" value="{{ $ticket->precio_reparacion }}" placeholder="Ej: 45.50" required>
                    @endif
                </div>
                
                <div class="col-md-3 text-end mt-3 mt-md-0">
                    <button type="submit" class="btn btn-primary fw-bold w-100">Guardar Datos</button>
                </div>
            </form>
            <small class="text-muted d-block mt-3"><i>Nota: Ambos campos deben estar llenos y en verde (Entregados y Precio > 0) para poder Completar la orden.</i></small>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold">Información del Cliente</div>
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
                        <span class="badge border border-dark text-dark fs-6 status-{{ strtolower(str_replace(' ', '-', $ticket->status)) }}">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <h3 class="h5 text-primary mb-3">🛠️ Fallas y Repuestos Solicitados</h3>
    
    <div class="table-responsive bg-white shadow-sm rounded border mb-4">
        <table class="table table-bordered mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th style="width: 15%;">Código</th>
                    <th style="width: 35%;">Descripción en Catálogo</th>
                    <th class="text-center" style="width: 10%;">Cant.</th>
                    <th style="width: 35%;">Observación / Falla</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticket->detalles as $index => $detalle)
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
                @endforeach
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