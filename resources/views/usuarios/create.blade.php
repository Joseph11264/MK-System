@extends('layouts.app')

@section('titulo', 'Registrar Usuario')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">➕ Registrar Nuevo Usuario</h2>
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

    <form action="{{ route('usuarios.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm border" style="max-width: 600px; margin: 0 auto;">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label fw-bold">Nombre Completo</label>
            <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label fw-bold">Nombre de Usuario (Login)</label>
            <input type="text" class="form-control" name="username" value="{{ old('username') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Contraseña</label>
            <input type="password" class="form-control" name="password" required minlength="6">
            <small class="text-muted">La contraseña será encriptada automáticamente.</small>
        </div>

        <div class="mb-4">
            <label for="rol" class="form-label fw-bold">Rol del Usuario</label>
            <select class="form-select" name="rol" required>
                <option value="">-- Seleccione un Rol --</option>
                <option value="SuperAdmin" {{ old('rol') == 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                <option value="Administracion" {{ old('rol') == 'Administracion' ? 'selected' : '' }}>Administración</option>
                <option value="ServicioTecnico" {{ old('rol') == 'ServicioTecnico' ? 'selected' : '' }}>Servicio Técnico</option>
                <option value="Almacen" {{ old('rol') == 'Almacen' ? 'selected' : '' }}>Almacén</option>
                <option value="Produccion" {{ old('rol') == 'Produccion' ? 'selected' : '' }}>Producción</option>
            </select>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg w-100">Registrar Usuario</button>
        </div>
    </form>
</section>
@endsection