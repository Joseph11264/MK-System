@extends('layouts.app')

@section('titulo', 'Detalle Ticket ST #' . $ticket->id)

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔎 Detalle Ticket ST #{{ $ticket->id }}</h2>
        <a href="{{ route('st.index') }}" class="btn btn-outline-danger fw-bold">Volver al listado</a>
    </div>
    <hr>
    
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
                        <strong>Estado Actual:</strong>
                        <span class="badge border border-dark text-dark fs-6 status-{{ strtolower(str_replace(' ', '-', $ticket->status)) }}">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <h3 class="h5 text-primary mb-3">🛠️ Fallas y Repuestos Solicitados</h3>
    
    <div class="table-responsive bg-white shadow-sm rounded border">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th style="width: 20%;">Cód. Producto</th>
                    <th class="text-center" style="width: 10%;">Cant.</th>
                    <th style="width: 65%;">Descripción / Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticket->detalles as $index => $detalle)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $detalle->codigo_producto }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-muted">{{ $detalle->observacion ?: 'Sin observaciones' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 d-flex gap-2">
        @if($ticket->status !== 'Completado')
            <form action="{{ route('st.update', $ticket->id) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="En Curso">
                <button type="submit" class="btn btn-warning fw-bold">🟠 Iniciar Reparación</button>
            </form>
        @endif
        
        <a href="#" class="btn btn-primary fw-bold">Imprimir Reporte 📄</a>
    </div>

</section>
@endsection