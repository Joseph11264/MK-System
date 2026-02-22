@extends('layouts.app')

@section('titulo', 'Gestión de Usuarios')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">👤 Gestión de Usuarios del Sistema</h2>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary fw-bold">➕ Nuevo Usuario</a>
    </div>
    <hr>

    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-bordered table-hover text-center mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Username</th>
                    <th>Rol en el Sistema</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td class="fw-bold">{{ $user->nombre }}</td>
                    <td>{{ $user->username }}</td>
                    <td>
                        @php
                            $badgeColor = match($user->rol) {
                                'SuperAdmin' => 'bg-danger',
                                'Administracion' => 'bg-primary',
                                'ServicioTecnico' => 'bg-warning text-dark',
                                'Almacen' => 'bg-info text-dark',
                                'Produccion' => 'bg-success',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeColor }} fs-6">{{ $user->rol }}</span>
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-sm btn-outline-primary fw-bold">✏️ Editar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection