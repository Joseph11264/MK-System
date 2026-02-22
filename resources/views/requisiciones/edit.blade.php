@extends('layouts.app')

@section('titulo', 'Modificar Requisición #' . $requisicion->id)

@section('content')
<section class="main-content-wrapper">

    <h2 class="text-primary">✍️ Modificar Encabezado Requisición #{{ $requisicion->id }}</h2>
    <p class="text-muted">Solo puedes modificar la información del técnico y el estado de la requisición.</p>
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

    <form action="{{ route('requisiciones.update', $requisicion->id) }}" method="POST" class="bg-body p-4 rounded shadow-sm border" id="formRequisicion">
        @csrf @method('PUT')

        @if($requisicion->status === 'En Curso')
            <div class="alert alert-warning">
                <strong>⚠️ Esta requisición está En Curso.</strong> Como administrador, tu única acción permitida aquí es cancelarla.
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">Acción de Emergencia:</label>
                <select name="status" class="form-select border-danger" required>
                    <option value="">-- Seleccione para cancelar --</option>
                    <option value="Cancelado">🚫 Cancelar Requisición</option>
                </select>
            </div>
            
        @elseif($requisicion->status === 'Pendiente')
            <fieldset class="border p-3 rounded mb-4">
                <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Información Principal</legend>
                
                <div class="mb-3 p-3 bg-body-secondary rounded border">
                    <label class="fw-bold me-3">Tipo de Operación:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo" id="tipoReq" value="Requisicion" {{ $requisicion->tipo == 'Requisicion' ? 'checked' : '' }}>
                        <label class="form-check-label text-primary fw-bold" for="tipoReq">⬇️ Requisición</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo" id="tipoDev" value="Devolucion" {{ $requisicion->tipo == 'Devolucion' ? 'checked' : '' }}>
                        <label class="form-check-label text-warning fw-bold" for="tipoDev">⬆️ Devolución</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Número de Técnico</label>
                        <input type="text" class="form-control" name="nro_tecnico" value="{{ $requisicion->nro_tecnico }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Nombre del Técnico</label>
                        <input type="text" class="form-control" name="nombre_tecnico" value="{{ $requisicion->nombre_tecnico }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select class="form-select" name="status" required>
                            <option value="Pendiente" selected>Pendiente</option>
                            <option value="En Curso">En Curso</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>
            </fieldset>
            
            <fieldset class="border p-3 rounded mb-4">
                <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Modificar Productos</legend>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width: 25%;">Código</th>
                                <th style="width: 15%;">Cantidad</th>
                                <th style="width: 50%;">Observación</th>
                                <th style="width: 10%;">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productosBody">
                            @foreach($requisicion->detalles as $index => $prod)
                            <tr class="product-row">
                                <td><input type="text" class="form-control" name="productos[{{$index}}][codigo]" value="{{ $prod->codigo_producto }}" list="listaProductos" required></td>
                                <td><input type="number" class="form-control" name="productos[{{$index}}][cantidad]" value="{{ $prod->cantidad }}" min="1" required></td>
                                <td><textarea class="form-control" name="productos[{{$index}}][observacion]" rows="1">{{ $prod->observacion }}</textarea></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="removeRow(this)">X</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" id="addRowBtn" class="btn btn-success fw-bold">+ Añadir producto</button>
            </fieldset>
        @endif

        <div class="text-end mt-4">
            <a href="{{ route('requisiciones.index') }}" class="btn btn-outline-secondary btn-lg px-4 me-2">Volver</a>
            <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">Guardar Cambios</button>
        </div>
    </form>

    <datalist id="listaProductos">
        @foreach(\App\Models\Producto::all() as $prod)
            <option value="{{ $prod->codigo_producto }}">{{ $prod->descripcion }}</option>
        @endforeach
    </datalist>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('productosBody');
            const addRowBtn = document.getElementById('addRowBtn');
            if(!addRowBtn) return; // Si no existe el botón (estado En Curso), no hacemos nada
            
            let rowIndex = {{ $requisicion->detalles->count() }}; 

            addRowBtn.addEventListener('click', function() {
                rowIndex++;
                const newRow = document.createElement('tr');
                newRow.classList.add('product-row');
                newRow.innerHTML = `
                    <td><input type="text" class="form-control" name="productos[${rowIndex}][codigo]" list="listaProductos" required></td>
                    <td><input type="number" class="form-control" name="productos[${rowIndex}][cantidad]" min="1" required></td>
                    <td><textarea class="form-control" name="productos[${rowIndex}][observacion]" rows="1"></textarea></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm fw-bold" onclick="removeRow(this)">X</button></td>
                `;
                tableBody.appendChild(newRow);
            });

            window.removeRow = function(buttonElement) {
                if (tableBody.children.length > 1) {
                    buttonElement.closest('.product-row').remove();
                } else {
                    alert("Debe quedar al menos un producto.");
                }
            };
        });
    </script>
    @endsection