@extends('layouts.app')

@section('titulo', 'Gestionar Familias')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">🏷️ Gestionar Familias y Categorías</h2>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 border-top border-4 border-success">
                <div class="card-header bg-body text-body fw-bold fs-5">
                    ➕ Nueva Familia
                </div>
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm py-2">
                            <ul class="mb-0 px-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('familias.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de la Familia</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Fuentes" value="{{ old('nombre') }}" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Rango Inicio</label>
                                <input type="text" name="rango_inicio" class="form-control" placeholder="120000" pattern="\d{6}" title="Debe tener 6 números" value="{{ old('rango_inicio') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Rango Fin</label>
                                <input type="text" name="rango_fin" class="form-control" placeholder="129999" pattern="\d{6}" title="Debe tener 6 números" value="{{ old('rango_fin') }}" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 fw-bold">💾 Guardar Familia</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre de la Familia</th>
                                <th class="text-center">Rango de Códigos</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($familias as $fam)
                            <tr>
                                <td class="fw-bold text-primary fs-5">{{ $fam->nombre }}</td>
                                <td class="text-center fw-bold text-secondary">
                                    {{ $fam->rango_inicio }} <i class="text-muted fw-normal px-1">hasta</i> {{ $fam->rango_fin }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('familias.destroy', $fam->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta familia?');" class="m-0">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Aún no hay familias registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection