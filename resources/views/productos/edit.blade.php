@extends('layouts.app')

@section('titulo', 'Editar Producto')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">✏️ Editar Producto: {{ $producto->codigo_producto }}</h2>
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary fw-bold">Volver al catálogo</a>
    </div>

    <div class="card shadow-sm border-0" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-bold">Código del Producto </label>
                        <input type="text" name="codigo_producto" class="form-control @error('codigo_producto') is-invalid @enderror" value="{{ old('codigo_producto', $producto->codigo_producto) }}" required>
                        @error('codigo_producto')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8 mb-4">
                        <label class="form-label fw-bold">Nombre / Descripción </label>
                        <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $producto->descripcion) }}" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Familia / Categoría</label>
                        <select name="familia_id" class="form-select">
                            <option value="">-- Sin Familia --</option>
                            @foreach($familias as $fam)
                                <option value="{{ $fam->id }}" {{ (old('familia_id', $producto->familia_id) == $fam->id) ? 'selected' : '' }}>
                                    {{ $fam->nombre }} ({{ $fam->rango_inicio }} al {{ $fam->rango_fin }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Fotografía del Producto (Opcional)</label>
                        <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                        
                        @error('imagen')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @else
                            <small class="text-muted">Formatos válidos: JPG, PNG, WEBP. Peso máximo: 2MB.</small>
                        @enderror
                    </div>
                </div>

                <div class="text-end border-top pt-3 mt-2">
                    <button type="submit" class="btn btn-warning btn-lg fw-bold px-5 text-dark">💾 Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection