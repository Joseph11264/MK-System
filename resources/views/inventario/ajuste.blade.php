@extends('layouts.app')

@section('titulo', 'Ajuste de Inventario')

@section('content')
<div class="container-fluid max-w-7xl mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary m-0">⚖️ Ajuste Manual de Inventario</h2>
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Volver al Catálogo</a>
    </div>

    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-dark text-white fw-bold">
            Registrar Entrada o Salida
        </div>
        <div class="card-body bg-body-tertiary">
            <form action="{{ route('inventario.registrar_ajuste') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Producto a Ajustar </label>
                    <select name="producto_id" class="form-select shadow-sm border-secondary" required>
                        <option value="">-- Selecciona un Producto --</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}">
                                {{ $prod->codigo_producto }} - {{ $prod->descripcion }} ({{ $prod->stock_format }} disponibles)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tipo de Movimiento </label>
                        <select name="tipo_movimiento" class="form-select shadow-sm border-secondary" required>
                            <option value="Entrada" class="text-success fw-bold">➕ ENTRADA (Sumar Stock)</option>
                            <option value="Salida" class="text-danger fw-bold">➖ SALIDA (Restar Stock)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Cantidad </label>
                        <input type="number" step="any" min="0.01" name="cantidad" class="form-control shadow-sm border-secondary" placeholder="Ej: 10 o 0.5" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Motivo del Ajuste </label>
                    <input type="text" name="motivo" class="form-control shadow-sm border-secondary" placeholder="Ej: Inventario Inicial, Merma, Reposición..." required>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">💾 Registrar Movimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection