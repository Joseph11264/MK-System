@extends('layouts.app')

@section('titulo', 'Historial del Producto')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔎 Historial de Movimientos</h2>
        @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Produccion']))
            <a href="{{ route('formulas.manage', $producto->id) }}" class="btn btn-info fw-bold text-dark shadow-sm">🛠️ Gestionar Fórmula (BOM)</a>
         @endif
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary fw-bold">Volver al catálogo</a>
    </div>

    <div class="card shadow-sm border-0 mb-4 border-start border-5 border-primary">
        <div class="card-body d-flex align-items-center gap-4">
            @if($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Img" class="rounded shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
            @else
                <div class="bg-secondary rounded text-white d-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">Sin Imagen</div>
            @endif
            
            <div>
                <h3 class="mb-1">{{ $producto->descripcion }}</h3>
                <p class="mb-0 fs-5"><strong>Código:</strong> <span class="text-primary">{{ $producto->codigo_producto }}</span></p>
                <p class="mb-0 text-muted"><strong>Familia:</strong> {{ $producto->familia->nombre ?? 'Sin familia asignada' }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning text-body fw-bold">
                    🔧 Usado en Servicio Técnico
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>Orden ST</th>
                                <th>Fecha</th>
                                <th class="text-center">Cant.</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salidasST as $salida)
                                <tr>
                                    <td class="fw-bold"><a href="{{ route('st.show', $salida->requisicionSt->id) }}" target="_blank">#{{ $salida->requisicionSt->nro_orden_st }}</a></td>
                                    <td class="align-middle text-center">
                                        @if($salida->requisicionSt)
                                            {{ $salida->requisicionSt->created_at->format('d/m/Y - h:i A') }}
                                        @else
                                            <span class="text-muted">Fecha desconocida</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-danger">-{{ $salida->cantidad }}</td>
                                    <td>
                                        <span class="badge bg-{{ $salida->requisicionSt->status == 'Completado' ? 'success' : ($salida->requisicionSt->status == 'Cancelado' ? 'danger' : 'secondary') }}">
                                            {{ $salida->requisicionSt->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted">No se ha usado en reparaciones.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    📦 Usado en Requisiciones Generales
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>Requisición</th>
                                <th>Fecha</th>
                                <th class="text-center">Cant.</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salidasAlmacen as $salida)
                                <tr>
                                    <td class="fw-bold"><a href="{{ route('requisiciones.show', $salida->requisicion->id) }}" target="_blank">#{{ $salida->requisicion->id }}</a></td>
                                    <td class="align-middle text-center">
                                        @if($salida->requisicion)
                                            {{ $salida->requisicion->created_at->format('d/m/Y - h:i A') }}
                                        @else
                                            <span class="text-muted">Fecha desconocida</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-danger">-{{ $salida->cantidad }}</td>
                                    <td>
                                        <span class="badge bg-{{ $salida->requisicion->status == 'Entregado' ? 'success' : ($salida->requisicion->status == 'Cancelado' ? 'danger' : 'secondary') }}">
                                            {{ $salida->requisicion->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted">No se ha usado en requisiciones.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection