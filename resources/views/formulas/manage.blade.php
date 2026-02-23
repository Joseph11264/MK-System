@extends('layouts.app')

@section('titulo', 'Gestionar Fórmula')

@section('content')
<div class="container-fluid max-w-7xl mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="text-primary m-0">🛠️ Receta de Producción (BOM)</h2>
            <p class="text-muted fs-5 mb-0">
                Producto Final: <strong class="text-dark">{{ $producto->codigo_producto }} - {{ $producto->descripcion }}</strong>
            </p>
        </div>
        <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-outline-secondary fw-bold">Volver al Producto</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0 border-start border-5 border-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white fw-bold">
                    ➕ Agregar Material
                </div>
                <div class="card-body bg-body-tertiary">
                    <form action="{{ route('formulas.add', $producto->id) }}" method="POST" id="form_agregar_material">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Buscar Componente </label>
                            
                            <input type="text" id="buscador_ingrediente" class="form-control shadow-sm border-secondary" list="lista_productos" placeholder="Escribe código o nombre..." autocomplete="off" required>
                            
                            <datalist id="lista_productos">
                                @foreach($disponibles as $disp)
                                    <option data-id="{{ $disp->id }}" value="{{ $disp->codigo_producto }} - {{ $disp->descripcion }}"></option>
                                @endforeach
                            </datalist>

                            <input type="hidden" name="ingrediente_id" id="ingrediente_id_hidden">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Cantidad requerida </label>
                            <input type="number" step="any" min="0.0001" name="cantidad" class="form-control shadow-sm border-secondary" placeholder="Ej: 2 o 0.25" required>
                            <small class="text-muted">¿Cuánto material se necesita para fabricar 1 unidad?</small>
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm">Agregar a la Receta</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    📋 Materiales Requeridos para 1 {{ $producto->unidad_medida }}
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Descripción del Material</th>
                                <th class="text-center" style="min-width: 150px;">Cantidad</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($producto->ingredientesFormula as $item)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $item->ingrediente->codigo_producto }}</td>
                                    <td>{{ $item->ingrediente->descripcion }}</td>
                                    
                                    <td class="text-center">
                                        <form action="{{ route('formulas.update', $item->id) }}" method="POST" class="d-flex align-items-center justify-content-center gap-1 m-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" step="any" min="0.0001" name="cantidad" value="{{ floatval($item->cantidad) }}" class="form-control form-control-sm text-center fw-bold text-primary" style="width: 80px;" required>
                                            <span class="fs-6 text-dark me-1" style="width: 35px; text-align: left;">{{ $item->ingrediente->unidad_medida }}</span>
                                            <button type="submit" class="btn btn-sm btn-outline-primary shadow-sm" title="Guardar cambio de cantidad">💾</button>
                                        </form>
                                    </td>
                                    
                                    <td class="text-center">
                                        <form action="{{ route('formulas.remove', $item->id) }}" method="POST" onsubmit="return confirm('¿Quitar este material de la receta?');" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="Quitar de la fórmula">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <span class="fs-1 d-block mb-2">🤷‍♂️</span>
                                        Este producto aún no tiene una fórmula definida.<br>Agrega materiales usando el buscador.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscador = document.getElementById('buscador_ingrediente');
        const hiddenId = document.getElementById('ingrediente_id_hidden');
        const form = document.getElementById('form_agregar_material');

        // Capturar el ID cuando el usuario seleccione una opción
        buscador.addEventListener('input', function() {
            let val = this.value;
            let option = document.querySelector('#lista_productos option[value="' + val + '"]');
            
            if(option) {
                hiddenId.value = option.getAttribute('data-id');
                buscador.classList.remove('is-invalid');
                buscador.classList.add('is-valid');
            } else {
                hiddenId.value = '';
                buscador.classList.remove('is-valid');
            }
        });

        // Evitar que el formulario se envíe si no seleccionó un producto válido
        form.addEventListener('submit', function(e) {
            if (hiddenId.value === '') {
                e.preventDefault(); // Detiene el envío
                buscador.classList.add('is-invalid');
                alert('⚠️ Por favor, selecciona un componente válido de la lista desplegable.');
            }
        });
    });
</script>
@endsection