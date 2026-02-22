@extends('layouts.app')

@section('titulo', 'Crear Nueva Requisición')

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">➕ Crear Nueva Requisición</h2>
        <a href="{{ route('requisiciones.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
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

    <form action="{{ route('requisiciones.store') }}" method="POST" id="formRequisicion" class="bg-body p-4 rounded shadow-sm border">
        @csrf <fieldset class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Información del Técnico</legend>

            <div class="mb-4 p-3 bg-body-secondary rounded border">
                <label class="fw-bold me-3">Tipo de Operación (*):</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo" id="tipoReq" value="Requisicion" checked>
                    <label class="form-check-label text-primary fw-bold" for="tipoReq">⬇️ Requisición (Salida)</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo" id="tipoDev" value="Devolucion">
                    <label class="form-check-label text-warning fw-bold" for="tipoDev">⬆️ Devolución (Entrada)</label>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nro_tecnico" class="form-label fw-bold">Número de Técnico</label>
                    <input type="text" class="form-control" id="nro_tecnico" name="nro_tecnico" value="{{ old('nro_tecnico') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nombre_tecnico" class="form-label fw-bold">Nombre del Técnico</label>
                    <input type="text" class="form-control" id="nombre_tecnico" name="nombre_tecnico" value="{{ old('nombre_tecnico') }}" required>
                </div>
            </div>
        </fieldset>
        
        <fieldset class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Detalle de Productos</legend>
            
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="productosTable">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width: 25%;">Código de Producto (*)</th>
                            <th style="width: 15%;">Cantidad (*)</th>
                            <th style="width: 50%;">Observación (Opcional)</th>
                            <th style="width: 10%; text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="productosBody">
                        <tr class="product-row">
                            <td><input type="text" class="form-control" name="productos[0][codigo]" required></td>
                            <td><input type="number" class="form-control" name="productos[0][cantidad]" min="1" required></td>
                            <td><textarea class="form-control" name="productos[0][observacion]" rows="1"></textarea></td>
                            <td class="text-center"></td> </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" id="addRowBtn" class="btn btn-success fw-bold d-inline-flex align-items-center gap-2 mt-2">
                <span class="fs-4 lh-1">+</span> Añadir otro producto
            </button>
        </fieldset>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg px-5">Guardar Requisición</button>
        </div>
    </form>

</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('productosBody');
        const addRowBtn = document.getElementById('addRowBtn');
        let rowIndex = 0; // Usamos un contador simple en lugar de calcular el MaxIndex

        addRowBtn.addEventListener('click', function() {
            rowIndex++;
            
            const newRow = document.createElement('tr');
            newRow.classList.add('product-row');
            
            newRow.innerHTML = `
                <td><input type="text" class="form-control" name="productos[${rowIndex}][codigo]" required></td>
                <td><input type="number" class="form-control" name="productos[${rowIndex}][cantidad]" min="1" required></td>
                <td><textarea class="form-control" name="productos[${rowIndex}][observacion]" rows="1"></textarea></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm fw-bold remove-row-btn" onclick="removeRow(this)">X</button>
                </td>
            `;

            tableBody.appendChild(newRow);
        });

        // Eliminar fila
        window.removeRow = function(buttonElement) {
            if (tableBody.children.length > 1) {
                buttonElement.closest('.product-row').remove();
            } else {
                alert("Debe haber al menos un producto en la requisición.");
            }
        };
    });
</script>
@endsection