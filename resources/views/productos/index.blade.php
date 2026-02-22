@extends('layouts.app')

@section('titulo', 'Catálogo de Productos')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">📦 Catálogo de Productos</h2>
        <a href="{{ route('productos.create') }}" class="btn btn-success fw-bold shadow-sm">➕ Nuevo Producto</a>
    </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light rounded">
            <form action="{{ route('productos.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Buscar por Código</label>
                    <input type="text" name="codigo" class="form-control" placeholder="Ej: 100020" value="{{ request('codigo') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Buscar por Nombre/Descripción</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Pantalla OLED..." value="{{ request('nombre') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Filtrar por Familia</label>
                    <select name="familia_id" class="form-select">
                        <option value="">-- Todas --</option>
                        @foreach($familias as $fam)
                            <option value="{{ $fam->id }}" {{ request('familia_id') == $fam->id ? 'selected' : '' }}>{{ $fam->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-primary fw-bold w-100 mb-1">🔍 Filtrar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 80px;">Imagen</th>
                        <th>Código</th>
                        <th>Descripción del Producto</th>
                        <th>Familia</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr>
                        <td class="text-center">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Img" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded text-white d-flex justify-content-center align-items-center mx-auto" style="width: 50px; height: 50px; font-size: 10px;">Sin Img</div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $producto->codigo_producto }}</td>
                        <td>{{ $producto->descripcion }}</td>
                        <td>
                            @if($producto->familia)
                                <span class="badge bg-info text-dark">{{ $producto->familia->nombre ?? '-' }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No se encontraron productos con estos filtros.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        {{ $productos->links() }}
    </div>
</section>
@endsection