@extends('layouts.app')

@section('titulo', 'Consultar Requisiciones')

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">📄 Consulta y Filtrado de Requisiciones</h2>
        <a href="{{ route('requisiciones.create') }}" class="btn btn-success fw-bold shadow-sm">➕ Nueva Requisición</a>
    </div>
    <hr>
    
    <section class="quick-filters text-center mb-4">
        <p class="fw-bold mb-2">Filtro Rápido por Estado:</p>
        <a href="{{ route('requisiciones.index', ['status' => 'Pendiente']) }}" class="btn btn-danger shadow-sm">🔴 Pendientes</a>
        <a href="{{ route('requisiciones.index', ['status' => 'En Curso']) }}" class="btn btn-warning shadow-sm">🟠 En Curso</a>
        <a href="{{ route('requisiciones.index', ['status' => 'Completado']) }}" class="btn btn-success shadow-sm">🟢 Completado</a>
    </section>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-body text-primary fw-bold border-bottom-0 pt-3 pb-0">
            Filtros Personalizados
        </div>
        <div class="card-body bg-body-secondary rounded">
            <form action="{{ route('requisiciones.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Nro Req.</label>
                        <input type="text" name="id" class="form-control form-control-sm" placeholder="Ej: 5" value="{{ request('id') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Técnico</label>
                        <input type="text" name="tecnico" class="form-control form-control-sm" placeholder="ID o Nombre" value="{{ request('tecnico') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Tipo</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="Requisicion" {{ request('tipo') == 'Requisicion' ? 'selected' : '' }}>Requisición</option>
                            <option value="Devolucion" {{ request('tipo') == 'Devolucion' ? 'selected' : '' }}>Devolución</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Estado</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="En Curso" {{ request('status') == 'En Curso' ? 'selected' : '' }}>En Curso</option>
                            <option value="Completado" {{ request('status') == 'Completado' ? 'selected' : '' }}>Completado</option>
                            <option value="Cancelado" {{ request('status') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6 text-primary" style="font-size: 0.85rem !important;">📅 Desde</label>
                        <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6 text-primary" style="font-size: 0.85rem !important;">📅 Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3 border-top pt-3 gap-3">
                    
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-bold text-muted w-100 w-sm-auto mb-1 mb-sm-0">
                            <i class="text-primary fs-5">🔃</i> Ordenar por:
                        </span>
                        <select name="sort_by" class="form-select form-select-sm shadow-sm flex-grow-1 flex-sm-grow-0" style="min-width: 140px; width: auto;">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Fecha de Solicitud</option>
                            <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Nro de Requisición</option>
                            <option value="tipo" {{ request('sort_by') == 'tipo' ? 'selected' : '' }}>Tipo (Req/Dev)</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Estado</option>
                        </select>
                        <select name="sort_dir" class="form-select form-select-sm shadow-sm flex-grow-1 flex-sm-grow-0" style="min-width: 140px; width: auto;">
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Descendente (Z-A)</option>
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Ascendente (A-Z)</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <a href="{{ route('requisiciones.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1 flex-md-grow-0">Limpiar Todo</a>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 shadow-sm flex-grow-1 flex-md-grow-0">🔍 Aplicar Filtros</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <h3 class="text-primary h5 mb-3">Resultados (Total: {{ $requisiciones->total() }})</h3>
    
    <div class="table-responsive bg-body-secondary shadow-sm rounded border border-secondary border-opacity-25">
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
                        <td class="text-center align-middle">
                            @php
                                $badgeColor = 'bg-secondary text-white'; // Gris por defecto para Cancelado
                                if($req->status === 'Pendiente') {
                                    $badgeColor = 'bg-danger text-white'; // Rojo
                                } elseif($req->status === 'En Curso') {
                                    $badgeColor = 'bg-warning text-dark'; // Amarillo
                                } elseif(in_array($req->status, ['Completado', 'Entregado'])) {
                                    $badgeColor = 'bg-success text-white'; // Verde
                                }
                            @endphp
                            <span class="badge {{ $badgeColor }} px-3 py-2 fs-6 shadow-sm border border-light border-opacity-10">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td>
                            @php
                                $puedeAvanzar = false;
                                if (auth()->user()->rol !== 'Produccion') {
                                    if ($req->status === 'Pendiente') {
                                        $puedeAvanzar = true; // Todos (menos Prod) pueden avanzar de Pendiente a En Curso
                                    } elseif ($req->status === 'En Curso' && auth()->user()->rol !== 'Almacen') {
                                        $puedeAvanzar = true; // Almacén NO puede avanzar si ya está En Curso
                                    }
                                }
                            @endphp

                            @if($puedeAvanzar)
                                @php
                                    $nextStatus = $req->status === 'Pendiente' ? 'En Curso' : 'Completado';
                                @endphp
                                <form action="{{ route('requisiciones.avanzar', $req->id) }}" method="POST" class="m-0 p-0 mb-1" onsubmit="return confirm('¿Avanzar requisición a {{ $nextStatus }}?');">
                                    @csrf @method('PATCH')
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
                        <td colspan="9" class="p-4 text-muted fs-5">No se encontraron requisiciones con estos filtros.</td>
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