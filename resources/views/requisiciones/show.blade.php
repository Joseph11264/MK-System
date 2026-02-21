@extends('layouts.app')

@section('titulo', 'Detalle de Requisición #' . $requisicion->id)

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔎 Detalle de Requisición #{{ $requisicion->id }}</h2>
        <a href="{{ route('requisiciones.index') }}" class="btn btn-outline-danger fw-bold">Volver a Consulta</a>
    </div>
    <hr>
    
    <div class="bg-light p-4 rounded mb-4 border-start border-5 border-primary shadow-sm">
        <h3 class="h5 border-bottom pb-2 mb-3 text-secondary">Información General</h3>
        
        <div class="row">
            <div class="col-md-6 mb-2">
                <p class="mb-1"><strong>Nro. Requisición:</strong> {{ $requisicion->id }}</p>
                <p class="mb-1"><strong>Nro. Técnico:</strong> {{ $requisicion->nro_tecnico }}</p>
                <p class="mb-1"><strong>Nombre Técnico:</strong> {{ $requisicion->nombre_tecnico }}</p>
            </div>
            
            <div class="col-md-6 mb-2">
                <p class="mb-1"><strong>Fecha Creación:</strong> {{ $requisicion->created_at->format('Y-m-d H:i') }}</p>
                <p class="mb-1 d-flex align-items-center gap-2">
                    <strong>Estado:</strong> 
                    @php
                        $statusClass = 'status-' . strtolower(str_replace(' ', '-', $requisicion->status));
                    @endphp
                    <span class="badge border text-dark {{ $statusClass }} px-3 py-2 fs-6">
                        {{ $requisicion->status }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    
    <h3 class="h5 text-primary mb-3">📦 Productos Solicitados</h3>
    
    @if($requisicion->detalles->count() > 0)
        <div class="table-responsive bg-white shadow-sm rounded border">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th>Código Producto</th>
                        <th class="text-center">Cantidad Requerida</th>
                        <th>Observación Producto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requisicion->detalles as $index => $detalle)
                    <tr>
                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td>{{ $detalle->codigo_producto }}</td>
                        <td class="text-center fw-bold text-primary">{{ $detalle->cantidad }}</td>
                        <td class="text-muted">{{ $detalle->observacion ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning shadow-sm border-warning">
            ⚠️ Esta requisición no tiene detalles de productos asociados.
        </div>
    @endif
    
    <div class="mt-4 d-flex gap-2">
        @if($requisicion->status !== 'Completado')
            <a href="{{ route('requisiciones.edit', $requisicion->id) }}" class="btn btn-success fw-bold">
                📝 Modificar / Avanzar
            </a>
        @endif
        
        <a href="#" target="_blank" class="btn btn-primary fw-bold">
            Generar PDF 📄
        </a>
    </div>

</section>
@endsection