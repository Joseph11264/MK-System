@extends('layouts.app')

@section('titulo', 'Recepción de Equipo ST')

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🔧 Recepción de Equipo (Nuevo ST)</h2>
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

    <form action="{{ route('st.store') }}" method="POST" id="formST" class="bg-body p-4 rounded shadow-sm border">
        @csrf

        <fieldset class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Datos Iniciales de Recepción</legend>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold"> Nro. Orden ST</label>
                    <input type="text" class="form-control text-center fw-bold text-success fs-5" value="{{ $proximoNro ?? 'Auto' }}" disabled>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Tipo de Servicio </label>
                    <div class="p-2 border rounded bg-body-secondary">
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

                <div class="col-md-5 mb-3 position-relative">
                    <label for="cliente_nombre" class="form-label fw-bold">Nombre del Cliente </label>
                    <input type="text" id="cliente_nombre" name="cliente" class="form-control" value="{{ old('cliente') }}" placeholder="Escribe para buscar o registra uno nuevo" autocomplete="off" onkeyup="buscarCliente(this.value)" required>
                    <ul id="lista_clientes" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></ul>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="codigo_equipo" class="form-label fw-bold">Código/Serial del Equipo </label>
                    <input type="text" class="form-control" name="codigo_equipo" value="{{ old('codigo_equipo') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="telefono_cliente" class="form-label fw-bold">Teléfono </label>
                    <input type="text" class="form-control" name="telefono_cliente" value="{{ old('telefono_cliente') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="correo_cliente" class="form-label fw-bold">Correo Electrónico </label>
                    <input type="email" class="form-control" name="correo_cliente" value="{{ old('correo_cliente') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="tecnico_asignado_id" class="form-label fw-bold">Técnico (Opcional en recepción)</label>
                    <select class="form-select" name="tecnico_asignado_id">
                        <option value="">-- Asignar luego --</option>
                        @foreach($tecnicos ?? [] as $tecnico)
                            <option value="{{ $tecnico->id }}" {{ old('tecnico_asignado_id') == $tecnico->id ? 'selected' : '' }}>
                                {{ $tecnico->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold text-danger">Falla Reportada</label>
                    <textarea class="form-control border-danger" name="falla_reportada" rows="2" placeholder="Ej: El cliente indica que el equipo no enciende desde ayer..." required>{{ old('falla_reportada') }}</textarea>
                </div>
            </div>
        </fieldset>
        
        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg px-5">💾 Generar Ticket de Ingreso</button>
        </div>
    </form>
</section>

<script>
    // LÓGICA DE AUTOCOMPLETADO DE CLIENTES
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
@endsection