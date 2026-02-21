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

    <form action="{{ route('requisiciones.update', $requisicion->id) }}" method="POST" class="bg-white p-4 rounded shadow-sm border">
        @csrf
        @method('PUT') <fieldset class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-primary fs-5">Información Principal</legend>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nro_tecnico" class="form-label fw-bold">Número de Técnico</label>
                    <input type="text" class="form-control" id="nro_tecnico" name="nro_tecnico" 
                           value="{{ old('nro_tecnico', $requisicion->nro_tecnico) }}" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="nombre_tecnico" class="form-label fw-bold">Nombre del Técnico</label>
                    <input type="text" class="form-control" id="nombre_tecnico" name="nombre_tecnico" 
                           value="{{ old('nombre_tecnico', $requisicion->nombre_tecnico) }}" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label fw-bold text-danger">Estado (*)</label>
                    <select class="form-select border-danger" id="status" name="status" required>
                        @foreach (['Pendiente', 'En Curso', 'Completado', 'Cancelado'] as $estado)
                            <option value="{{ $estado }}" 
                                {{ (old('status', $requisicion->status) == $estado) ? 'selected' : '' }}>
                                {{ $estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </fieldset>
        
        <fieldset class="border p-3 rounded bg-light mb-4">
            <legend class="float-none w-auto px-2 fw-bold text-secondary fs-6">Detalle de Productos (Solo Lectura)</legend>
            
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 bg-white">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width: 25%;">Código de Producto</th>
                            <th style="width: 15%; text-align: center;">Cantidad</th>
                            <th style="width: 60%;">Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requisicion->detalles as $producto)
                            <tr>
                                <td class="fw-bold text-muted">{{ $producto->codigo_producto }}</td>
                                <td class="text-center text-muted">{{ $producto->cantidad }}</td>
                                <td class="text-muted">{{ $producto->observacion ?: 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </fieldset>

        <div class="d-flex justify-content-between">
            <a href="{{ route('requisiciones.index') }}" class="btn btn-outline-secondary btn-lg px-4">Cancelar</a>
            <button type="submit" class="btn btn-success btn-lg px-5 fw-bold">Guardar Cambios</button>
        </div>
    </form>

</section>
@endsection