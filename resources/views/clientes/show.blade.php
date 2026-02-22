@extends('layouts.app')
@section('titulo', 'Historial del Cliente')
@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔎 Historial de Servicio Técnico</h2>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary fw-bold">Volver al Directorio</a>
    </div>

    <div class="card shadow-sm border-0 mb-4 border-start border-5 border-primary bg-body">
        <div class="card-body">
            <h3 class="mb-1 text-primary">{{ $cliente->nombre }}</h3>
            <p class="mb-0 fs-5"><strong>📞 Teléfono:</strong> {{ $cliente->telefono ?: 'N/A' }} <span class="mx-2">|</span> <strong>📧 Correo:</strong> {{ $cliente->correo ?: 'N/A' }}</p>
        </div>
    </div>

    <h4 class="text-muted mb-3">Equipos ingresados por este cliente:</h4>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Nro. Orden</th>
                        <th>Fecha de Ingreso</th>
                        <th>Equipo / Serial</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Técnico Asignado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="fw-bold fs-5">#{{ $ticket->nro_orden_st }}</td>
                        <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                        <td class="fw-bold text-info">{{ $ticket->codigo_equipo }}</td>
                        <td>
                            <span class="badge bg-{{ $ticket->tipo_st === 'Garantia' ? 'warning text-dark' : 'primary' }}">
                                {{ $ticket->tipo_st === 'Garantia' ? 'Garantía' : 'Reparación' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge border border-dark text-dark status-{{ strtolower(str_replace(' ', '-', $ticket->status)) }}">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td>{{ $ticket->tecnico->nombre ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('st.show', $ticket->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">Ver Ticket</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 text-muted fs-5">Este cliente no tiene reparaciones registradas aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $tickets->links() }}</div>
</section>
@endsection