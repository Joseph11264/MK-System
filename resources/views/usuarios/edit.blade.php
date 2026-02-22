@extends('layouts.app')

@section('titulo', 'Editar Usuario')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">✏️ Editar Usuario: {{ $usuario->nombre }}</h2>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
    </div>
    <hr>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" class="bg-white p-4 rounded shadow-sm border" style="max-width: 600px; margin: 0 auto;">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label fw-bold">Nombre Completo</label>
            <input type="text" class="form-control" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label fw-bold">Nombre de Usuario (Login)</label>
            <input type="text" class="form-control" name="username" value="{{ old('username', $usuario->username) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Nueva Contraseña (Opcional)</label>
            <input type="password" class="form-control" name="password" minlength="6">
            <small class="text-muted">Déjalo en blanco si no deseas cambiar la contraseña actual.</small>
        </div>

        <div class="mb-4">
            <label for="rol" class="form-label fw-bold">Rol del Usuario</label>
            <select class="form-select" name="rol" required>
                @foreach (['SuperAdmin', 'Administracion', 'ServicioTecnico', 'Almacen', 'Produccion'] as $rol)
                    <option value="{{ $rol }}" {{ old('rol', $usuario->rol) == $rol ? 'selected' : '' }}>
                        {{ $rol }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">Guardar Cambios</button>
        </div>
    </form>
</section>
@endsection