@extends('layouts.app')

@section('content')
<div class="container mt-4" style="max-width: 900px;">

    @if ($sell->deleted == 1)
        <div class="alert alert-danger alert-dismissible fade show">
            Estas viendo un registro eliminado 🚨
        </div>
    @endif

    <!-- Card container -->
    <div class="card border-0 shadow-sm" style="background-color: #e8f4f8; border-radius: 12px;">
        <div class="card-body p-5">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="fw-bold mb-3" style="color: #2c5560; font-size: 2.5rem;">Detalles de la Venta</h1>
            </div>

            <!-- Información de la venta -->
            <div class="sale-info-section mb-5">
                <h3 class="fw-bold mb-4" style="color: #2c5560; font-size: 1.8rem;">Información de la Venta</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">ID Venta:</span>
                            <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">{{$sell->id}}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">Fecha:</span>
                            <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">{{$sell->created_at}}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">Subtotal:</span>
                            <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">${{$sell->subtotal}}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">Descuento:</span>
                            <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">{{$sell->discount}}%</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">Total:</span>
                            <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">${{$sell->total}}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">Cajero:</span>
                            <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">{{$sell->user_info->name}}</span>
                        </div>
                        
                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">Método de pago:</span>
                            @foreach ($sell->payments as $payment)
                                <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">{{$payment->method}} -- ${{$payment->amount}}</span>
                            @endforeach
                        </div>

                        <div class="info-item mb-3">
                            <span class="fw-bold" style="color: #2c5560; font-size: 1.1rem;">Notas:</span>
                            <span style="color: #2c5560; font-size: 1.1rem; margin-left: 8px;">{{$sell->notes}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos vendidos -->
            <div class="products-section mb-5">
                <h3 class="fw-bold mb-4" style="color: #2c5560; font-size: 1.8rem;">Productos Vendidos</h3>
                
                <div class="table-container bg-white rounded-3 overflow-hidden" style="border: 1px solid #dee2e6;">
                    <table class="table mb-0">
                        <thead style="background-color: rgba(255, 255, 255, 0.8); border-bottom: 1px solid #dee2e6;">
                            <tr>
                                <th class="px-4 py-3 fw-bold" style="color: #2c5560; font-size: 1rem;">Producto</th>
                                <th class="px-4 py-3 fw-bold" style="color: #2c5560; font-size: 1rem;">Cantidad</th>
                                <th class="px-4 py-3 fw-bold" style="color: #2c5560; font-size: 1rem;">Precio Unitario</th>
                                <th class="px-4 py-3 fw-bold" style="color: #2c5560; font-size: 1rem;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sell->details as $product)
                                <tr>
                                    <td class="px-4 py-3" style="color: #2c5560;">{{$product->catalogo->nombre}}</td>
                                    <td class="px-4 py-3" style="color: #2c5560;">{{$product->quantity}}</td>
                                    <td class="px-4 py-3" style="color: #2c5560;">${{$product->price}}</td>
                                    <td class="px-4 py-3" style="color: #2c5560;">${{$product->price * $product->quantity}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="actions-section">
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{route('sells')}}" class="btn btn-lg px-4 py-2 fw-semibold text-white" 
                            style="background-color: #6c757d; border: none; border-radius: 8px;">
                        Regresar
                    </a>
                    <button onclick="openTicket()" class="btn btn-lg px-4 py-2 fw-semibold text-white" 
                            style="background-color: #E68A2E; border: none; border-radius: 8px;">
                        Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    
    .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(230, 138, 46, 0.05);
    }
    
    .info-item {
        display: flex;
        align-items: center;
        padding: 2px 0;
    }
    
    .info-item span:first-child {
        min-width: 120px;
    }
    
    @media (max-width: 768px) {
        .info-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .info-item span:first-child {
            min-width: auto;
            margin-bottom: 2px;
        }
        
        .info-item span:last-child {
            margin-left: 0 !important;
            padding-left: 10px;
        }
    }
</style>

<script>
    function openTicket(){
        window.open('/ticket/' + {{ $sell->id }}, '_blank');
        history.replaceState({}, document.title, window.location.pathname)
    }
</script>
@endsection