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
                            <a href="show.php" class="text-primary text-decoration-none d-block fw-bold mb-1">🔎 Ver</a>
                            @if($ticket->status === 'Pendiente')
                                <a href="#" class="text-danger text-decoration-none d-block mt-1" style="font-size: 0.9em;">✏️ Editar</a>
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