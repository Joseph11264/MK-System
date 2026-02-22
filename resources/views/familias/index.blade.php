@extends('layouts.app')
@section('titulo', 'Gestionar Familias')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-bold">➕ Nueva Familia</div>
            <div class="card-body">
                <form action="{{ route('familias.store') }}" method="POST">
                    @csrf
                    <label class="form-label">Nombre de la Familia/Categoría</label>
                    <input type="text" name="nombre" class="form-control mb-3" placeholder="Ej: Pantallas" required>
                    <button type="submit" class="btn btn-success w-100 fw-bold">Guardar Familia</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Nombre de la Familia</th><th class="text-center">Acción</th></tr>
                    </thead>
                    <tbody>
                        @foreach($familias as $fam)
                        <tr>
                            <td>{{ $fam->id }}</td>
                            <td class="fw-bold text-primary">{{ $fam->nombre }}</td>
                            <td class="text-center">
                                <form action="{{ route('familias.destroy', $fam->id) }}" method="POST" onsubmit="return confirm('¿Eliminar?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection