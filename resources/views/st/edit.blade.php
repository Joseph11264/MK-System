@extends('layouts.app')

@section('titulo', 'Gestionar Ticket ST')

@section('content')
<section class="main-content-wrapper">
    <h2 class="text-primary m-0">✏️ Gestionar Ticket ST #{{ $ticket->id }}</h2>
    <hr>

    <form action="{{ route('st.update', $ticket->id) }}" method="POST" class="bg-white p-4 rounded shadow-sm border" id="formST">
        @csrf @method('PUT')

        @if($ticket->status === 'En Curso')
            <div class="alert alert-info">
                <strong>ℹ️ El equipo está en revisión/reparación.</strong> Desde aquí puedes finalizar el servicio o cancelarlo si no tiene reparación.
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">Acción a realizar:</label>
                    <select name="status" class="form-select border-primary" required>
                        <option value="">-- Seleccione el resultado --</option>
                        <option value="Completado">✅ Marcar como Completado (Reparado/Entregado)</option>
                        <option value="Cancelado">🚫 Cancelar Servicio (Sin solución/Rechazado)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">Técnico que finaliza (Opcional):</label>
                    <select class="form-select" name="tecnico_asignado_id">
                        <option value="">-- Mantener Técnico Actual --</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}" {{ $ticket->tecnico_asignado_id == $tecnico->id ? 'selected' : '' }}>
                                {{ $tecnico->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

        @elseif($ticket->status === 'Pendiente')
            <fieldset class="border p-3 rounded mb-4">
                <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Datos del Ticket</legend>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Nro. Orden ST</label>
                        <input type="text" class="form-control text-center fw-bold bg-light text-success fs-5" value="{{ $ticket->nro_orden_st }}" disabled>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">Cliente</label>
                        <input type="text" class="form-control" name="cliente" value="{{ $ticket->cliente }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Código/Serial Equipo</label>
                        <input type="text" class="form-control" name="codigo_equipo" value="{{ $ticket->codigo_equipo }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" class="form-control" name="telefono_cliente" value="{{ $ticket->telefono_cliente }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Estado del Ticket</label>
                        <select class="form-select" name="status" required>
                            <option value="Pendiente" {{ $ticket->status == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="Completado" {{ $ticket->status == 'Completado' ? 'selected' : '' }}>Completado</option>
                            <option value="Cancelado" {{ $ticket->status == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Materiales</label>
                        <select class="form-select" name="materiales_entregados" required>
                            <option value="0" {{ !$ticket->materiales_entregados ? 'selected' : '' }}>❌ No Entregados</option>
                            <option value="1" {{ $ticket->materiales_entregados ? 'selected' : '' }}>✅ Entregados</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Técnico Asignado</label>
                        <select class="form-select" name="tecnico_asignado_id">
                            <option value="">-- Sin Asignar --</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}" {{ $ticket->tecnico_asignado_id == $tecnico->id ? 'selected' : '' }}>
                                    {{ $tecnico->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>
            
            <fieldset class="border p-3 rounded mb-4">
                <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Fallas o Repuestos</legend>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Código</th>
                                <th style="width: 15%;">Cantidad</th>
                                <th style="width: 50%;">Observación</th>
                                <th style="width: 10%;">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productosBody">
                            @foreach($ticket->detalles as $index => $prod)
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
                <button type="button" id="addRowBtn" class="btn btn-success fw-bold">+ Añadir repuesto</button>
            </fieldset>
        @endif

        <div class="text-end mt-4">
            <a href="{{ route('st.index') }}" class="btn btn-outline-secondary btn-lg px-4 me-2">Volver</a>
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
            if(!addRowBtn) return;
            
            let rowIndex = {{ $ticket->detalles->count() }}; 

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
                    alert("Debe quedar al menos una línea.");
                }
            };
        });
    </script>
</section>
@endsection