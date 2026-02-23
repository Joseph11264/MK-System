@extends('layouts.app')

@section('titulo', 'Órdenes de Producción')

@section('content')
<div class="container-fluid max-w-7xl mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary m-0">Control de Producción</h2>
        @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Produccion']))
            <a href="{{ route('ordenes.create') }}" class="btn btn-success fw-bold shadow-sm">➕ Nueva Orden</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 border-start border-5 border-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Orden</th>
                        <th>Producto a Fabricar</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-center">Estado</th>
                        <th>Solicitado por</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordenes as $orden)
                        @php
                            $badgeColor = 'bg-secondary';
                            if($orden->status === 'Pendiente') $badgeColor = 'bg-danger text-white';
                            if($orden->status === 'En Curso') $badgeColor = 'bg-warning text-dark';
                            if($orden->status === 'Completado') $badgeColor = 'bg-success text-white';
                        @endphp
                        <tr>
                            <td class="fw-bold text-primary">{{ $orden->codigo_orden }}</td>
                            <td>
                                <span class="fw-bold">{{ $orden->producto->codigo_producto }}</span><br>
                                <small class="text-muted">{{ $orden->producto->descripcion }}</small>
                            </td>
                            <td class="text-center fw-bold fs-5">{{ $orden->cantidad }}</td>
                            <td class="text-center">
                                <span class="badge {{ $badgeColor }} fs-6 px-3 py-2 shadow-sm border border-light border-opacity-10">
                                    {{ $orden->status }}
                                </span>
                            </td>
                            <td>{{ $orden->tecnico->nombre ?? 'Sin asignar' }}</td>
                            <td>{{ $orden->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if(!in_array($orden->status, ['Completado', 'Cancelado']))
                                    <form action="{{ route('ordenes.update_status', $orden->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        
                                        @if($orden->status === 'Pendiente')
                                            <input type="hidden" name="status" value="En Curso">
                                            <button type="submit" class="btn btn-sm btn-warning fw-bold text-dark shadow-sm" onclick="return confirm('¿Iniciar producción?');">▶️ Iniciar</button>
                                        @elseif($orden->status === 'En Curso')
                                            <input type="hidden" name="status" value="Completado">
                                            <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm" onclick="return confirm('¿Marcar como terminado? Se inyectarán los productos al Kardex.');">✅ Terminar</button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-muted small">Cerrada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <span class="fs-1 d-block mb-2"></span>
                                No hay órdenes de producción registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $ordenes->links() }}
    </div>
</div>
@endsection