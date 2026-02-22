@extends('layouts.app')

@section('titulo', 'Consultar Requisiciones')

@section('content')
<section class="main-content-wrapper">

    <h2 class="text-primary">📄 Consulta y Filtrado de Requisiciones</h2>
    <hr>
    
    <section class="quick-filters text-center mb-4">
        <p class="fw-bold mb-2">Filtro Rápido por Estado:</p>
        <a href="{{ route('requisiciones.index', ['status' => 'Pendiente']) }}" class="btn btn-danger">🔴 Pendientes</a>
        <a href="{{ route('requisiciones.index', ['status' => 'En Curso']) }}" class="btn btn-warning">🟠 En Curso</a>
        <a href="{{ route('requisiciones.index', ['status' => 'Completado']) }}" class="btn btn-success">🟢 Completado</a>
        <a href="{{ route('requisiciones.index') }}" class="btn btn-outline-info">🧹 Mostrar Todos</a>
    </section>

    <h3 class="text-primary h5">Filtros Personalizados</h3>
    <form action="{{ route('requisiciones.index') }}" method="GET" class="filter-form bg-light p-3 rounded mb-4 d-flex flex-wrap gap-2 align-items-end border">
        
        <div class="form-group-filter">
            <label class="fw-bold form-label mb-1">ID Requisición:</label>
            <input type="text" name="id" class="form-control form-control-sm" value="{{ request('id') }}" style="width: 80px;">
        </div>

        <div class="form-group-filter">
            <label class="fw-bold form-label mb-1">Tipo:</label>
            <select name="tipo" class="form-select form-select-sm" style="width: 120px;">
                <option value=""> Todos </option>
                <option value="Requisicion" {{ request('tipo') == 'Requisicion' ? 'selected' : '' }}>Requisición</option>
                <option value="Devolucion" {{ request('tipo') == 'Devolucion' ? 'selected' : '' }}>Devolución</option>
            </select>
        </div>

        <div class="form-group-filter">
            <label class="fw-bold form-label mb-1">Nro Técnico:</label>
            <input type="text" name="nro_tecnico" class="form-control form-control-sm" value="{{ request('nro_tecnico') }}" style="width: 120px;">
        </div>

        <div class="form-group-filter">
            <label class="fw-bold form-label mb-1">Estado:</label>
            <select name="status" class="form-select form-select-sm" style="width: 130px;">
                <option value=""> Todos </option>
                <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="En Curso" {{ request('status') == 'En Curso' ? 'selected' : '' }}>En Curso</option>
                <option value="Completado" {{ request('status') == 'Completado' ? 'selected' : '' }}>Completado</option>
            </select>
        </div>
    
        <div class="form-group-filter">
            <label class="fw-bold form-label mb-1">Fecha Desde:</label>
            <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ request('fecha_inicio') }}">
        </div>

        <div class="form-group-filter">
            <label class="fw-bold form-label mb-1">Fecha Hasta:</label>
            <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ request('fecha_fin') }}">
        </div>
        
        <button type="submit" class="btn btn-outline-primary btn-sm mb-1">🔍 Aplicar</button>
        <a href="{{ route('requisiciones.index') }}" class="btn btn-outline-secondary btn-sm mb-1">🧹 Limpiar</a>
    </form>

    <h3 class="text-primary h5">Resultados (Total: {{ $requisiciones->total() }})</h3>
    
    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-bordered table-hover text-center mb-0 align-middle">
            <thead class="table-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Nro Técnico</th>
                    <th>Técnico</th>
                    <th>Cód. Producto</th>
                    <th>Cantidades</th> 
                    <th>Fecha Creación</th>
                    <th>Status</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisiciones as $req)
                    @php
                        $statusClass = 'status-' . strtolower(str_replace(' ', '-', $req->status));
                    @endphp
                    <tr class="{{ $statusClass }}">
                        <td class="fw-bold">{{ $req->id }}</td>
                        <td class="fw-bold">
                        @if($req->tipo === 'Devolucion')
                            <span class="badge bg-warning text-dark" style="font-size: 0.75em;">Devolución</span>
                        @else
                            <span class="badge bg-primary" style="font-size: 0.75em;">Requisición</span>
                        @endif
                        </td>
                        <td>{{ $req->nro_tecnico }}</td>
                        <td>{{ $req->nombre_tecnico }}</td>
                        <td>{{ $req->detalles->pluck('codigo_producto')->join(', ') }}</td>
                        <td>{{ $req->detalles->pluck('cantidad')->join(', ') }}</td>
                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
                        <td><span class="badge bg-secondary">{{ $req->status }}</span></td>
                        <td>
                            @if(in_array($req->status, ['Pendiente', 'En Curso']))
                                @php
                                    $nextStatus = $req->status === 'Pendiente' ? 'En Curso' : 'Completado';
                                @endphp
                                <form action="{{ route('requisiciones.avanzar', $req->id) }}" method="POST" class="m-0 p-0 mb-1" onsubmit="return confirm('¿Seguro que deseas avanzar esta requisición a {{ $nextStatus }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="new_status" value="{{ $nextStatus }}">
                                    <button type="submit" class="btn btn-link text-success text-decoration-none fw-bold p-0" style="font-size: 0.9em; box-shadow: none;">
                                        📝 Avanzar
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('requisiciones.show', $req->id) }}" class="text-primary text-decoration-none d-block" style="font-size: 0.9em;">
                                🔎 Ver Detalle
                            </a>
                            
                            @if($req->status === 'Pendiente')
                                <a href="{{ route('requisiciones.edit', $req->id) }}" class="text-danger text-decoration-none fw-bold d-block mt-1" style="font-size: 0.9em;">
                                    ✏️ Modificar
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-muted">No se encontraron requisiciones.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $requisiciones->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

</section>
@endsection