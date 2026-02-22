@extends('layouts.app')

@section('titulo', 'Consultar Tickets ST')

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔧 Consulta de Tickets (Servicio Técnico)</h2>
        <a href="{{ route('st.create') }}" class="btn btn-primary fw-bold">➕ Nuevo Ticket</a>
    </div>
    <hr>
    
    <div class="table-responsive bg-white shadow-sm rounded">
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
                        <td><span class="badge border border-dark text-dark fs-6">{{ $ticket->status }}</span></td>
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
                        @if($ticket->status === 'Pendiente')
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