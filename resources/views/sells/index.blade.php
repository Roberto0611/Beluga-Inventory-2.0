@extends('layouts.app')

    @if(request()->has('ticketId'))
        <script>
            // Abrir el ticket en nueva pestaña
            window.open('/ticket/' + {{ request('ticketId') }}, '_blank');
            
            // Opcional: Limpiar el parámetro de la URL (sin recargar)
            history.replaceState({}, document.title, window.location.pathname);
        </script>
    @endif

@section('content')
    <div class="container mt-4" style="max-width: 1200px;">
        <!-- Bienvenida -->
        <div class="welcome-section mb-4 p-4 rounded-3" style="background-color: #f8f9fa; border-left: 4px solid #E68A2E;">
            <h2 class="fw-bold mb-2" style="color: #2c5560;">Bienvenido al Sistema de Ventas</h2>
            <p class="text-muted mb-0">Este es el sistema de ventas, donde se realizan la venta de productos.</p>
        </div>

        <!-- Botones principales -->
        <div class="actions-section mb-4">
            <div class="d-flex gap-3 flex-wrap">
                <a class="btn btn-lg px-4 py-2 fw-semibold rounded-2" href="{{route('newsell')}}"
                        style="background-color: #E68A2E; color: white; border: none;">
                    Registrar Venta
                </a>
                <a href="{{route('corte')}}" class="btn btn-lg px-4 py-2 fw-semibold rounded-2" 
                        style="background-color: #E68A2E; color: white; border: none;">
                    Corte de caja
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Buscar por ID -->
        <form id="searchForm">
            <div class="search-section mb-4 p-4 rounded-3" style="background-color: #f8f9fa;">
                <h4 class="fw-bold mb-3" style="color: #2c5560;">Buscar Venta por ID</h4>
                <div class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1" style="max-width: 400px;">
                        <input type="text" id="sellIdInput" class="form-control" placeholder="Ingrese el ID de la venta" 
                            style="border: 1px solid #dee2e6; padding: 8px 12px;">
                    </div>
                    <button type="submit" class="btn px-3 py-2" 
                            style="background-color: #6c757d; color: white; border: none;">
                        Buscar
                    </button>
                </div>
            </div>
        </form>

        <!-- Ventas recientes -->
        <div class="sales-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0" style="color: #2c5560;">Ventas recientes</h4>
            </div>

            <!-- Tabla -->
            <div class="table-container bg-white rounded-3 overflow-hidden" style="border: 1px solid #dee2e6;">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                        <tr>
                            <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">#</th>
                            <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Total</th>
                            <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Fecha</th>
                            <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Notas</th>
                            <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Estado</th>
                            @if (Auth::user()->is_admin)
                                <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lastSells as $sell)
                            <tr>
                                <td class="px-3 py-3">
                                    <a href="{{route('showSell', $sell->id)}}" class="text-decoration-none fw-semibold"
                                        style="color: #E68A2E;">{{$sell->id}}</a>
                                </td>
                                <td class="px-3 py-3">${{$sell->total}}</td>
                                <td class="px-3 py-3 text-muted" style="font-size: 0.9rem;">{{$sell->created_at}}</td>
                                <td class="px-3 py-3 text-muted">{{$sell->notes}}</td>
                                <td class="px-3 py-3">
                                    @if ($sell->deleted == 0)
                                        <span class="badge rounded-pill px-2 py-1"
                                        style="background-color: #28a745; color: white; font-size: 0.75rem;">
                                        Activo
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2 py-1"
                                        style="background-color: #dc3545; color: white; font-size: 0.75rem;">
                                        Eliminado
                                        </span>
                                    @endif
                                </td>
                                
                                @if (Auth::user()->is_admin)

                                    @if ($sell->deleted == 0)
                                        <td class="px-3 py-3">
                                            <button class="btn btn-sm px-2 py-1 text-white"
                                                style="background-color: #dc3545; border: none; font-size: 0.8rem;"
                                                onclick="confirmDelete({{ $sell->id }})">
                                                Eliminar registro
                                            </button>
                                        </td>
                                    @else
                                        <td class="px-3 py-3"></td>
                                    @endif
                                    
                                @endif
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const sellId = document.getElementById('sellIdInput').value;

            // Validate sellId
            if (!sellId || isNaN(sellId)) {
                alert('Por favor ingrese un ID de venta válido');
                return;
            }

            // Construct the URL using Laravel's route helper
            window.location.href = "{{ route('showSell', ['sellId' => ':id']) }}".replace(':id', sellId);});

        function confirmDelete(sellId) {
            if (confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                window.location.href = "{{ route('deleteSell', ['sellId' => ':id']) }}".replace(':id', sellId);
            }
        }
    </script>

    <style>
        .welcome-section {
            border-left: 4px solid #E68A2E !important;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(230, 138, 46, 0.05);
        }

        .form-control:focus {
            border-color: #E68A2E;
            box-shadow: 0 0 0 0.2rem rgba(230, 138, 46, 0.25);
        }

        .form-check-input:checked {
            background-color: #E68A2E;
            border-color: #E68A2E;
        }

        a:hover {
            color: #d4791f !important;
        }
    </style>

    <script>

    </script>
@endsection