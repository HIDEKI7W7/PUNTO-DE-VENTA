@extends('layouts.app')

@section('title','Productos - El Punto')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Importamos Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Variables Premium UI */
    :root {
        --primary-blue: #4F46E5;
        --primary-blue-hover: #4338CA;
        --bg-soft: #F8FAFC;
        --card-bg: #FFFFFF;
        --text-main: #1E293B;
        --text-muted: #64748B;
        --success-color: #10B981;
        --danger-color: #EF4444;
        --warning-color: #F59E0B;
        --border-soft: #E2E8F0;
    }
    
    body {
        background-color: var(--bg-soft);
    }

    .page-title-box {
        margin-bottom: 2rem;
    }

    .page-title {
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.025em;
    }

    /* KPI Cards */
    .kpi-card {
        background: var(--card-bg);
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        font-size: 1.5rem;
    }

    .icon-blue { background: #EEF2FF; color: var(--primary-blue); }
    .icon-green { background: #D1FAE5; color: var(--success-color); }
    .icon-orange { background: #FEF3C7; color: var(--warning-color); }

    /* Button Primary Premium */
    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-blue), #3B82F6);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, var(--primary-blue-hover), #2563EB);
        transform: translateY(-1px);
        color: white;
        box-shadow: 0 6px 8px -1px rgba(79, 70, 229, 0.4);
    }

    /* Table Card */
    .table-card {
        background: var(--card-bg);
        border-radius: 1rem;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-card-header {
        background-color: var(--card-bg);
        border-bottom: 1px solid var(--border-soft);
        padding: 1.25rem 1.5rem;
        font-weight: 700;
        color: var(--text-main);
    }

    /* Custom Table Styling */
    .custom-table {
        margin-bottom: 0;
    }

    .custom-table thead th {
        background-color: #F1F5F9;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-soft);
    }

    .custom-table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        color: var(--text-main);
        border-bottom: 1px solid var(--border-soft);
        transition: background-color 0.15s ease;
    }

    .custom-table tbody tr:hover td {
        background-color: #F8FAFC;
    }

    /* Badges */
    .badge-custom {
        padding: 0.4em 0.8em;
        font-weight: 600;
        letter-spacing: 0.025em;
        border-radius: 9999px;
    }

    .badge-success-soft {
        background-color: #D1FAE5;
        color: #065F46;
    }

    .badge-danger-soft {
        background-color: #FEE2E2;
        color: #991B1B;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center page-title-box">
        <div>
            <h1 class="page-title h3 mb-1">Inventario de Productos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Productos</li>
                </ol>
            </nav>
        </div>
        @can('crear-producto')
        <div>
            <a href="{{route('productos.create')}}" class="btn btn-primary-custom d-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i> Añadir nuevo registro
            </a>
        </div>
        @endcan
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Total de Productos -->
        <div class="col-xl-4 col-md-6">
            <div class="kpi-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-semibold" style="font-size: 0.875rem;">Total de Productos</p>
                        <h3 class="mb-0 fw-bold">{{ $productos->count() }}</h3>
                    </div>
                    <div class="kpi-icon icon-blue">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Valor del Inventario -->
        <div class="col-xl-4 col-md-6">
            <div class="kpi-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-semibold" style="font-size: 0.875rem;">Valor del Inventario</p>
                        <h3 class="mb-0 fw-bold">$45,230 <span style="font-size: 0.75rem; color: var(--text-muted); font-weight:normal;">(Estimado)</span></h3>
                    </div>
                    <div class="kpi-icon icon-green">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Productos Agotados -->
        <div class="col-xl-4 col-md-12">
            <div class="kpi-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-semibold" style="font-size: 0.875rem;">Productos con Bajo Stock</p>
                        <h3 class="mb-0 fw-bold">12</h3>
                    </div>
                    <div class="kpi-icon icon-orange">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Row (Data Visualization) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="table-card p-4">
                <h5 class="fw-bold mb-4" style="color: var(--text-main);">Distribución de Productos por Categoría</h5>
                <!-- Chart Container -->
                <div style="height: 300px; width: 100%;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-card">
        <div class="table-card-header d-flex align-items-center gap-2">
            <i class="fas fa-list text-muted"></i>
            Listado General
        </div>
        <div class="card-body p-0">
            <table id="datatablesSimple" class="table custom-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Marca</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $item)
                    <tr>
                        <td class="fw-semibold text-dark">
                            {{$item->nombreCompleto}}
                        </td>
                        <td>
                            <span class="text-success fw-bold">{{$item->precio ? '$'.$item->precio : 'No aperturado'}}</span>
                        </td>
                        <td>
                            {{$item->marca->caracteristica->nombre ?? 'Sin marca'}}
                        </td>
                        <td>
                            {{$item->categoria->caracteristica->nombre ?? 'Sin categoría'}}
                        </td>
                        <td>
                            @if($item->estado)
                                <span class="badge badge-custom badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-custom badge-danger-soft">Agotado</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <div class="dropdown">
                                    <button title="Opciones"
                                        class="btn btn-sm btn-light border-0 shadow-sm rounded-circle"
                                        style="width: 32px; height: 32px; display:flex; align-items:center; justify-content:center;"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 0.875rem; border-radius: 0.5rem;">
                                        @can('editar-producto')
                                        <li><a class="dropdown-item py-2" href="{{route('productos.edit',['producto' => $item])}}">
                                            <i class="fa-solid fa-pen-to-square text-muted me-2"></i> Editar</a>
                                        </li>
                                        @endcan
                                        @can('ver-producto')
                                        <li>
                                            <a class="dropdown-item py-2" role="button" data-bs-toggle="modal" data-bs-target="#verModal-{{$item->id}}">
                                            <i class="fa-solid fa-eye text-muted me-2"></i> Ver Detalles</a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                                
                                @can('crear-inventario')
                                <div class="vr mx-1" style="height: 24px;"></div>
                                <form action="{{route('inventario.create')}}" method="get" class="m-0">
                                    <input type="hidden" name="producto_id" value="{{$item->id}}">
                                    <button title="Inicializar"
                                        class="btn btn-sm btn-light border-0 shadow-sm rounded-circle text-primary"
                                        style="width: 32px; height: 32px; display:flex; align-items:center; justify-content:center; background:#EEF2FF;"
                                        type="submit">
                                        <i class="fa-solid fa-rotate"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Detalle Modernizado -->
                    <div class="modal fade" id="verModal-{{$item->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold" style="color: var(--text-main);">Detalles del Producto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-4 mt-2">
                                        @if (!empty($item->img_path))
                                            <img src="{{ asset($item->img_path) }}" alt="{{ $item->nombre }}" class="img-fluid rounded shadow-sm" style="max-height: 200px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto" style="height: 150px; width: 150px;">
                                                <i class="fa-solid fa-image text-muted fa-3x"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="px-3 bg-light rounded p-3">
                                        <p class="mb-1"><span class="text-muted fw-semibold d-block" style="font-size: 0.75rem; text-transform:uppercase;">Descripción General</span>
                                        <span class="text-dark">{{$item->descripcion ?? 'No se ha proporcionado una descripción para este producto.'}}</span></p>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pt-0">
                                    <button type="button" class="btn btn-light" style="border-radius: 0.5rem;" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>

<!-- Script para Inicializar el Gráfico (Mock Data) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar Chart.js
    const ctx = document.getElementById('categoryChart');
    if(ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Electrónica', 'Ropa', 'Alimentos', 'Ferretería', 'Limpieza', 'Otros'],
                datasets: [{
                    label: 'Cantidad de Productos',
                    data: [120, 190, 300, 50, 20, 30],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',  // Primary blue
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)', // Success green
                        'rgba(245, 158, 11, 0.8)', // Warning orange
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(100, 116, 139, 0.8)'
                    ],
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E2E8F0',
                            drawBorder: false,
                        },
                        ticks: { color: '#64748B' }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: { color: '#64748B' }
                    }
                }
            }
        });
    }
});
</script>
@endpush