@extends('layouts.app')

@section('titulo', 'Catálogo de Productos')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary m-0">📦 Catálogo de Productos</h2>
        <a href="{{ route('productos.create') }}" class="btn btn-primary fw-bold">➕ Nuevo Producto</a>
    </div>
    <hr>

    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-bordered table-hover text-center mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td class="fw-bold">{{ $producto->codigo_producto }}</td>
                    <td class="text-start">{{ $producto->descripcion }}</td>
                    <td>
                        <span class="badge {{ $producto->activo ? 'bg-success' : 'bg-danger' }}">
                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>{{ $producto->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection