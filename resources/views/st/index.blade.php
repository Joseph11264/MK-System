@extends('layouts.app')

@section('titulo', 'Consultar Tickets ST')

@section('content')
<section class="main-content-wrapper">
    @if(!in_array(auth()->user()->rol, ['Almacen', 'Produccion']))
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔧 Consulta de Tickets (Servicio Técnico)</h2>
        <a href="{{ route('st.create') }}" class="btn btn-primary fw-bold">➕ Nuevo Ticket</a>
    </div>
    @endif

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-body-secondary rounded">
            <form action="{{ route('st.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-1">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Orden</label>
                        <input type="text" name="orden" class="form-control form-control-sm" value="{{ request('orden') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Cliente</label>
                        <input type="text" name="cliente" class="form-control form-control-sm" value="{{ request('cliente') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Equipo/Serial</label>
                        <input type="text" name="equipo" class="form-control form-control-sm" value="{{ request('equipo') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Tipo</label>
                        <select name="tipo_st" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="Reparacion" {{ request('tipo_st') == 'Reparacion' ? 'selected' : '' }}> Reparación</option>
                            <option value="Garantia" {{ request('tipo_st') == 'Garantia' ? 'selected' : '' }}> Garantía</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold fs-6" style="font-size: 0.85rem !important;">Estado</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="Completado" {{ request('status') == 'Completado' ? 'selected' : '' }}>Completado</option>
                        </select>
                    </div>
                    <div class="col-md-1">
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
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Fecha de Ingreso</option>
                            <option value="nro_orden_st" {{ request('sort_by') == 'nro_orden_st' ? 'selected' : '' }}>Número de Orden</option>
                            <option value="cliente" {{ request('sort_by') == 'cliente' ? 'selected' : '' }}>Nombre del Cliente</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Estado del Ticket</option>
                            <option value="tipo_st" {{ request('sort_by') == 'tipo_st' ? 'selected' : '' }}>Tipo de Servicio</option>
                        </select>
                        <select name="sort_dir" class="form-select form-select-sm shadow-sm flex-grow-1 flex-sm-grow-0" style="min-width: 140px; width: auto;">
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Descendente (Más reciente / Z-A)</option>
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Ascendente (Más antiguo / A-Z)</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <a href="{{ route('st.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1 flex-md-grow-0">Limpiar Todo</a>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 shadow-sm flex-grow-1 flex-md-grow-0">🔍 Aplicar Búsqueda y Orden</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <hr>
    
    <div class="table-responsive bg-body shadow-sm rounded">
        <table class="table table-bordered table-hover text-center mb-0 align-middle">
            <thead class="table-dark text-white">
                <tr>
                    <th>ID</th>
                    <th>Nro Orden</th>
                    <th>Tipo</th>
                    <th>Cliente</th>
                    <th>Equipo</th>
                    <th>Técnico Asignado</th>
                    <th>Fecha</th>
                    <th>Status</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    @php
                        $statusClass = 'status-' . strtolower(str_replace(' ', '-', $ticket->status));
                    @endphp
                    <tr class="{{ $statusClass }}">
                        <td class="fw-bold">{{ $ticket->id }}</td>
                        <td class="fw-bold text-primary">{{ $ticket->nro_orden_st }}</td>

                        <td class="fw-bold fs-6">
                            @if($ticket->tipo_st === 'Garantia')
                                <span class="badge bg-warning text-dark" style="font-size: 0.75em;"> Garantía</span>
                            @else
                                <span class="badge bg-primary" style="font-size: 0.75em;"> Reparación</span>
                            @endif
                        </td>

                        <td>
                            {{ $ticket->cliente }}<br>
                            @if($ticket->telefono_cliente)
                                <small class="text-muted">📞 {{ $ticket->telefono_cliente }}</small>
                            @endif
                        </td>
                        <td>{{ $ticket->codigo_equipo }}</td>
                        <td>
                            @if($ticket->tecnico)
                                {{ $ticket->tecnico->nombre }}
                            @else
                                <span class="badge bg-warning text-dark">Sin Asignar</span>
                            @endif
                        </td>
                        <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
                        <td class="text-center align-middle">
                            @php
                                $badgeColor = 'bg-secondary text-white'; // Gris por defecto para Cancelado
                                if($ticket->status === 'Pendiente') {
                                    $badgeColor = 'bg-danger text-white'; // Rojo
                                } elseif($ticket->status === 'En Curso') {
                                    $badgeColor = 'bg-warning text-dark'; // Amarillo
                                } elseif($ticket->status === 'Completado') {
                                    $badgeColor = 'bg-success text-white'; // Verde
                                }
                            @endphp
                            <span class="badge {{ $badgeColor }} px-3 py-2 fs-6 shadow-sm border border-light border-opacity-10">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td>
                        @if($ticket->status === 'Pendiente')
                            @if($ticket->materiales_entregados && $ticket->precio_reparacion > 0)
                                <form action="{{ route('st.avanzar', $ticket->id) }}" method="POST" class="m-0 p-0 mb-1" onsubmit="return confirm('¿Seguro que deseas marcar como Completado?');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-link text-success text-decoration-none fw-bold p-0" style="font-size: 0.9em; box-shadow: none;">
                                        📝 Completar ST
                                    </button>
                                </form>
                            @else
                                <span class="d-block mb-1 text-muted" style="font-size: 0.8em; cursor: not-allowed;" title="Entrega los materiales en 'Ver' para completar">
                                    🔒 Completar (Falta material)
                                </span>
                            @endif
                        @endif

                        <a href="{{ route('st.show', $ticket->id) }}" class="text-primary text-decoration-none d-block fw-bold mb-1" style="font-size: 0.9em;">🔎 Ver</a>
                        @if($ticket->status === 'Pendiente' && !in_array(auth()->user()->rol, ['Almacen', 'Produccion']))
                            <a href="{{ route('st.edit', $ticket->id) }}" class="text-danger text-decoration-none d-block mt-1" style="font-size: 0.9em;">✏️ Editar</a>
                        @endif
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-muted fs-5">No hay tickets de Servicio Técnico registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $tickets->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

</section>
@endsection

