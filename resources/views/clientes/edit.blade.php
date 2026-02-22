@extends('layouts.app')
@section('titulo', 'Registrar Cliente')
@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">➕ Registrar Cliente Habitual</h2>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary fw-bold">Volver al Directorio</a>
    </div>

    <div class="card shadow-sm border-0" style="max-width: 600px;">
        <div class="card-body p-4">
            <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre Completo o Razón Social </label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ $cliente->nombre }}" required>
                    @error('nombre') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Teléfono de Contacto</label>
                    <input type="text" name="telefono" class="form-control" value="{{ $cliente->telefono }}">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" value="{{ $cliente->correo }}">
                </div>
                <div class="text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-5">💾 Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection