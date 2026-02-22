@extends('layouts.app')

@section('titulo', 'Revisión Técnica ST')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🛠️ Revisión y Repuestos (ST #{{ $ticket->nro_orden_st }})</h2>
        <a href="{{ route('st.show', $ticket->id) }}" class="btn btn-outline-secondary">Cancelar</a>
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

    <form action="{{ route('st.update', $ticket->id) }}" method="POST" class="bg-body p-4 rounded shadow-sm border" id="formST">
        @csrf @method('PUT')
        <input type="hidden" name="actualizar_ticket" value="1">

        <fieldset class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Datos Generales</legend>
            <div class="row">
                <div class="col-md-5 mb-3 position-relative">
                    <label class="form-label fw-bold">Cliente</label>
                    <input type="text" id="cliente_nombre" name="cliente" class="form-control" value="{{ old('cliente', $ticket->cliente) }}" autocomplete="off" onkeyup="buscarCliente(this.value)" required>
                    <ul id="lista_clientes" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></ul>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Teléfono</label>
                    <input type="text" class="form-control" name="telefono_cliente" value="{{ old('telefono_cliente', $ticket->telefono_cliente) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Correo</label>
                    <input type="email" class="form-control" name="correo_cliente" value="{{ old('correo_cliente', $ticket->correo_cliente) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Código/Serial Equipo</label>
                    <input type="text" class="form-control" name="codigo_equipo" value="{{ old('codigo_equipo', $ticket->codigo_equipo) }}" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Técnico Asignado</label>
                    <select class="form-select border-primary" name="tecnico_asignado_id">
                        <option value="">-- Sin Asignar --</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}" {{ old('tecnico_asignado_id', $ticket->tecnico_asignado_id) == $tecnico->id ? 'selected' : '' }}>
                                {{ $tecnico->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold text-primary">Estado de Revisión</label>
                    <select class="form-select border-primary fw-bold" name="status" required>
                        <option value="Pendiente" {{ $ticket->status == 'Pendiente' ? 'selected' : '' }}>🔴 Pendiente (Revisando/Reparando)</option>
                        
                        @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                            <option value="Completado" {{ $ticket->status == 'Completado' ? 'selected' : '' }}>✅ Completado (Listo para entregar)</option>
                        @endif
                        
                        <option value="Cancelado" {{ $ticket->status == 'Cancelado' ? 'selected' : '' }}>🚫 Cancelado (Sin solución)</option>
                    </select>
                    
                    @if(!in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                        <small class="text-muted d-block mt-1">Solo Administración puede marcar el ticket como Completado.</small>
                    @endif
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold text-danger">Falla Reportada Inicialmente</label>
                    <textarea class="form-control border-danger" name="falla_reportada" rows="2" required>{{ old('falla_reportada', $ticket->falla_reportada) }}</textarea>
                </div>
            </div>
        </fieldset>
            
        <fieldset class="border p-3 rounded mb-4 bg-body-secondary">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">🛠️ Repuestos Necesarios</legend>
            <p class="text-muted small mb-2">Añade aquí los repuestos o componentes que utilizarás. Si solo es revisión sin repuestos, puedes dejar esto vacío.</p>
            
            <div class="table-responsive">
                <table class="table table-bordered align-middle bg-body">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width: 25%;">Código de Producto</th>
                            <th style="width: 15%;">Cantidad</th>
                            <th style="width: 50%;">Observación</th>
                            <th style="width: 10%;" class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="productosBody">
                        @forelse($ticket->detalles as $index => $prod)
                        <tr class="product-row">
                            <td><input type="text" class="form-control" name="productos[{{$index}}][codigo]" value="{{ $prod->codigo_producto }}" list="listaProductos" required></td>
                            <td><input type="number" class="form-control" name="productos[{{$index}}][cantidad]" value="{{ $prod->cantidad }}" min="1" required></td>
                            <td><textarea class="form-control" name="productos[{{$index}}][observacion]" rows="1">{{ $prod->observacion }}</textarea></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm fw-bold remove-row-btn" onclick="removeRow(this)">X</button>
                            </td>
                        </tr>
                        @empty
                        <tr class="product-row">
                            <td><input type="text" class="form-control" name="productos[0][codigo]" list="listaProductos"></td>
                            <td><input type="number" class="form-control" name="productos[0][cantidad]" min="1" value="1"></td>
                            <td><textarea class="form-control" name="productos[0][observacion]" rows="1"></textarea></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm fw-bold remove-row-btn" onclick="removeRow(this)">X</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <button type="button" id="addRowBtn" class="btn btn-success fw-bold">+ Añadir repuesto</button>
        </fieldset>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-warning btn-lg px-5 fw-bold text-dark">💾 Guardar Revisión y Repuestos</button>
        </div>
    </form>

    <datalist id="listaProductos">
        @foreach(\App\Models\Producto::all() as $prod)
            <option value="{{ $prod->codigo_producto }}">{{ $prod->descripcion }}</option>
        @endforeach
    </datalist>

    <script>
        // LÓGICA DE AÑADIR FILAS
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('productosBody');
            const addRowBtn = document.getElementById('addRowBtn');
            if(!addRowBtn) return;
            
            let rowIndex = {{ max($ticket->detalles->count(), 1) }}; 

            addRowBtn.addEventListener('click', function() {
                rowIndex++;
                const newRow = document.createElement('tr');
                newRow.classList.add('product-row');
                newRow.innerHTML = `
                    <td><input type="text" class="form-control" name="productos[${rowIndex}][codigo]" list="listaProductos" required></td>
                    <td><input type="number" class="form-control" name="productos[${rowIndex}][cantidad]" min="1" value="1" required></td>
                    <td><textarea class="form-control" name="productos[${rowIndex}][observacion]" rows="1"></textarea></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm fw-bold remove-row-btn" onclick="removeRow(this)">X</button></td>
                `;
                tableBody.appendChild(newRow);
            });

            window.removeRow = function(buttonElement) {
                buttonElement.closest('.product-row').remove();
            };
        });

        // LÓGICA DE AUTOCOMPLETADO
        function buscarCliente(termino) {
            let lista = document.getElementById('lista_clientes');
            if (termino.length < 2) {
                lista.classList.add('d-none');
                return;
            }
            fetch(`/api/clientes/buscar?q=${termino}`)
                .then(res => res.json())
                .then(data => {
                    lista.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(cliente => {
                            let li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action border-bottom';
                            li.style.cursor = 'pointer';
                            li.innerHTML = `<strong>${cliente.nombre}</strong> <br><small class="text-muted">${cliente.telefono || 'Sin tlf'} | ${cliente.correo || 'Sin correo'}</small>`;
                            li.onclick = () => seleccionarCliente(cliente);
                            lista.appendChild(li);
                        });
                        lista.classList.remove('d-none');
                    } else {
                        lista.classList.add('d-none');
                    }
                });
        }

        function seleccionarCliente(cliente) {
            document.getElementById('cliente_nombre').value = cliente.nombre;
            document.querySelector('input[name="telefono_cliente"]').value = cliente.telefono || '';
            document.querySelector('input[name="correo_cliente"]').value = cliente.correo || '';
            document.getElementById('lista_clientes').classList.add('d-none');
        }

        document.addEventListener('click', function(e) {
            if (!document.getElementById('cliente_nombre').contains(e.target)) {
                let lista = document.getElementById('lista_clientes');
                if(lista) lista.classList.add('d-none');
            }
        });
    </script>
</section>
@endsection