@extends('layouts.app')

@section('titulo', 'Crear Ticket Servicio Técnico')

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔧 Crear Ticket de Servicio Técnico</h2>
        <a href="{{ route('st.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
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

    <form action="{{ route('st.store') }}" method="POST" id="formST" class="bg-white p-4 rounded shadow-sm border">
        @csrf

        <fieldset class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Datos del Cliente y Equipo</legend>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold"> Nro. Orden ST</label>
                    <input type="text" class="form-control text-center fw-bold text-success fs-5" value="{{ $proximoNro }}" disabled>
                </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tipo de Servicio </label>
                        <div class="p-2 border rounded bg-light">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_st" id="tipoRep" value="Reparacion" checked>
                                <label class="form-check-label text-primary fw-bold" for="tipoRep"> Reparación</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_st" id="tipoGar" value="Garantia">
                                <label class="form-check-label text-warning fw-bold" for="tipoGar"> Garantía</label>
                            </div>
                        </div>
                    </div>

                <div class="col-md-5 mb-3">
                    <label for="cliente" class="form-label fw-bold">Nombre del Cliente</label>
                    <input type="text" class="form-control" name="cliente" value="{{ old('cliente') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="codigo_equipo" class="form-label fw-bold">Código/Serial del Equipo</label>
                    <input type="text" class="form-control" name="codigo_equipo" value="{{ old('codigo_equipo') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="telefono_cliente" class="form-label fw-bold">Teléfono </label>
                    <input type="text" class="form-control" name="telefono_cliente" value="{{ old('telefono_cliente') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="correo_cliente" class="form-label fw-bold">Correo Electrónico </label>
                    <input type="email" class="form-control" name="correo_cliente" value="{{ old('correo_cliente') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="tecnico_asignado_id" class="form-label fw-bold">Técnico Asignado </label>
                    <select class="form-select" name="tecnico_asignado_id" required>
                        <option value="">-- Sin Asignar --</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}" {{ old('tecnico_asignado_id') == $tecnico->id ? 'selected' : '' }}>
                                {{ $tecnico->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </fieldset>
        
        <fieldset class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Fallas o Repuestos Necesarios</legend>
            
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="productosTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">Cód. Producto/Falla</th>
                            <th style="width: 15%;">Cantidad</th>
                            <th style="width: 55%;">Descripción de la Falla u Observación</th>
                            <th style="width: 10%; text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="productosBody">
                        <tr class="product-row">
                            <td><input type="text" class="form-control" name="productos[0][codigo]" list="listaProductos" required></td>
                            <td><input type="number" class="form-control" name="productos[0][cantidad]" min="1" value="1" required></td>
                            <td><textarea class="form-control" name="productos[0][observacion]" rows="1"></textarea></td>
                            <td class="text-center"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" id="addRowBtn" class="btn btn-success fw-bold d-inline-flex align-items-center gap-2 mt-2">
                <span class="fs-4 lh-1">+</span> Añadir otra línea
            </button>
        </fieldset>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg px-5">Guardar Ticket ST</button>
        </div>
    </form>
</section>

<datalist id="listaProductos">
    @foreach(\App\Models\Producto::all() as $prod)
        <option value="{{ $prod->codigo_producto }}">{{ $prod->descripcion }}</option>
    @endforeach
</datalist>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('productosBody');
        const addRowBtn = document.getElementById('addRowBtn');
        let rowIndex = 0;

        addRowBtn.addEventListener('click', function() {
            rowIndex++;
            const newRow = document.createElement('tr');
            newRow.classList.add('product-row');
            newRow.innerHTML = `
                <td><input type="text" class="form-control" name="productos[${rowIndex}][codigo]" required></td>
                <td><input type="number" class="form-control" name="productos[${rowIndex}][cantidad]" min="1" value="1" required></td>
                <td><textarea class="form-control" name="productos[${rowIndex}][observacion]" rows="1"></textarea></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm fw-bold remove-row-btn" onclick="removeRow(this)">X</button>
                </td>
            `;
            tableBody.appendChild(newRow);
        });

        window.removeRow = function(buttonElement) {
            if (tableBody.children.length > 1) {
                buttonElement.closest('.product-row').remove();
            } else {
                alert("Debe haber al menos una línea registrada.");
            }
        };
    });
</script>
@endsection