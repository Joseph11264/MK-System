@extends('layouts.app')
@section('titulo', 'Directorio de Clientes')
@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">👥 Directorio de Clientes</h2>
        <a href="{{ route('clientes.create') }}" class="btn btn-success fw-bold shadow-sm">➕ Nuevo Cliente</a>
    </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-body-secondary rounded">
            <form action="{{ route('clientes.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, teléfono o correo..." value="{{ request('buscar') }}">
                <button type="submit" class="btn btn-primary fw-bold px-4">🔍 Buscar</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre / Razón Social</th>
                        <th>Teléfono</th>
                        <th>Correo Electrónico</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cli)
                    <tr>
                        <td class="fw-bold fs-5 text-primary">{{ $cli->nombre }}</td>
                        <td>{{ $cli->telefono ?: '---' }}</td>
                        <td>{{ $cli->correo ?: '---' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('clientes.show', $cli->id) }}" class="btn btn-sm btn-outline-primary" title="Historial de ST">🔎 Historial</a>
                                <a href="{{ route('clientes.edit', $cli->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">✏️</a>
                                <form action="{{ route('clientes.destroy', $cli->id) }}" method="POST" onsubmit="return confirm('¿Eliminar a este cliente?');" class="m-0">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No se encontraron clientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $clientes->links() }}</div>
</section>
@endsection