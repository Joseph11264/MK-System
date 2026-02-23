@extends('layouts.app')

@section('titulo', 'Nueva Orden de Producción')

@section('content')
<div class="container-fluid max-w-7xl mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="text-primary m-0">Crear Orden de Producción</h2>
        <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary fw-bold">Volver al Listado</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0 border-start border-5 border-danger fs-5">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-dark text-white fw-bold">
            Detalles de Fabricación
        </div>
        <div class="card-body bg-body-tertiary">
            <form action="{{ route('ordenes.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Producto a Fabricar</label>
                    <select name="producto_id" class="form-select shadow-sm border-secondary" required>
                        <option value="">-- Selecciona un producto con receta --</option>
                        @foreach($productosFabricables as $prod)
                            <option value="{{ $prod->id }}">
                                {{ $prod->codigo_producto }} - {{ $prod->descripcion }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Solo aparecen productos que ya tienen una fórmula (BOM) configurada.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Cantidad a Fabricar </label>
                    <input type="number" min="1" step="1" name="cantidad" class="form-control shadow-sm border-secondary" placeholder="Ej: 50" required>
                    <small class="text-primary fw-bold">El sistema verificará si hay material suficiente para esta cantidad antes de crear la orden.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Técnico Asignado </label>
                    <select name="tecnico_id" class="form-select shadow-sm border-secondary" required>
                        <option value="">-- Selecciona quién hará el trabajo --</option>
                        @foreach($tecnicos as $tec)
                            <option value="{{ $tec->id }}">{{ $tec->nombre }} ({{ $tec->rol }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Notas / Instrucciones (Opcional)</label>
                    <textarea name="notas" rows="3" class="form-control shadow-sm border-secondary" placeholder="Ej: Prioridad alta, usar cables azules..."></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">🚀 Procesar Orden y Apartar Material</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection