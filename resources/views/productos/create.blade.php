@extends('layouts.app')

@section('titulo', 'Añadir Producto')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">➕ Registrar Nuevo Producto</h2>
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary fw-bold">Volver al catálogo</a>
    </div>

    <div class="card shadow-sm border-0" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-bold">Código del Producto </label>
                        <input type="text" name="codigo_producto" class="form-control @error('codigo_producto') is-invalid @enderror" value="{{ old('codigo_producto') }}" placeholder="Ej: 100020" required>
                        @error('codigo_producto')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8 mb-4">
                        <label class="form-label fw-bold">Nombre / Descripción </label>
                        <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}" placeholder="Ej: Pantalla Original OLED..." required>

                        <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-primary">Unidad de Medida </label>
                        <select name="unidad_medida" class="form-select bg-body-secondary border-secondary shadow-sm" required>
                            <option value="Und" {{ old('unidad_medida', $producto->unidad_medida ?? 'Und') == 'Und' ? 'selected' : '' }}>Unidades (Und)</option>
                            <option value="Mts" {{ old('unidad_medida', $producto->unidad_medida ?? '') == 'Mts' ? 'selected' : '' }}>Metros (Mts)</option>
                            <option value="Cm" {{ old('unidad_medida', $producto->unidad_medida ?? '') == 'Cm' ? 'selected' : '' }}>Centímetros (Cm)</option>
                            <option value="Lts" {{ old('unidad_medida', $producto->unidad_medida ?? '') == 'Lts' ? 'selected' : '' }}>Litros (Lts)</option>
                            <option value="Kgs" {{ old('unidad_medida', $producto->unidad_medida ?? '') == 'Kgs' ? 'selected' : '' }}>Kilogramos (Kgs)</option>
                        </select>
                        <small class="text-muted">¿Cómo se consume este material en las fórmulas?</small>
                    </div>

                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Familia / Categoría</label>
                        <select name="familia_id" class="form-select">
                            <option value="">-- Sin Familia --</option>
                            @foreach($familias as $fam)
                                <option value="{{ $fam->id }}">{{ $fam->nombre }}</option>
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
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-5">💾 Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection