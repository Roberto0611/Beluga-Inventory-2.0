@extends('layouts.app')

@section('content')
    <div class="container mt-4" style="max-width: 800px;">
        <!-- Card container -->
        <div class="card border-0 shadow-sm" style="background-color: #ffffff; border-radius: 12px;">
            <div class="card-body p-4">
                <!-- Header -->
                <div class="header-section mb-4 p-4 rounded-3"
                    style="background-color: #e8f4f8; border-left: 4px solid #E68A2E;">
                    <h2 class="fw-bold mb-2" style="color: #2c5560;">Caja registradora Beluga</h2>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">
                        Todos los productos vendidos en esta sección serán descontados del sistema de inventario y quedará
                        un registro del usuario que ejecutó la venta
                    </p>
                </div>

                <!-- Agregar productos -->
                <div class="add-products-section mb-4">
                    <h4 class="fw-bold mb-3" style="color: #2c5560;">Agregar productos o servicios</h4>

                    <div class="mb-3">
                        <select name="sell_id" id="sell_select" class="form-select"
                            style="border: 1px solid #dee2e6; padding: 10px 15px; color: #6c757d;">
                            <option value="" selected disabled>Busca el producto...</option>
                            @foreach ($catalog as $product)
                                <option value="{{$product->ID}}" data-name="{{$product->nombre}}" data-price="
                                        {{$product->Precio}}" data-service="{{$product->service}}">{{$product->nombre}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label id="inventarioCantidad" class="form-label text-muted mb-1" style="font-size: 0.9rem;">Cantidad en inventario:
                                0</label>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <input id="itemQuantity" type="number" class="form-control" value="1" min="1"
                                style="border: 1px solid #dee2e6; padding: 8px 12px;">
                        </div>
                        <div class="col-8">
                            <button onclick="addItem()" class="btn text-white px-4 py-2 w-100" style="background-color: #E68A2E; border: none;">
                                Agregar Item
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de la caja -->
                <div class="cart-section mb-4">
                    <h4 class="fw-bold mb-3" style="color: #2c5560;">Caja</h4>

                    <div class="table-container bg-white rounded-3 overflow-hidden" style="border: 1px solid #dee2e6;">
                        <table class="table mb-0">
                            <thead style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                <tr>
                                    <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Item</th>
                                    <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Cantidad
                                    </th>
                                    <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Precio
                                        Unitario</th>
                                    <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Precio
                                        Total</th>
                                    <th class="px-3 py-3 fw-semibold" style="color: #495057; font-size: 0.9rem;">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="salesTable">
                                <!-- Elements will be added here by javascript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Descuento -->
                <div class="discount-section mb-3">
                    <label class="form-label fw-semibold mb-2" style="color: #E68A2E;">Descuento (%)</label>
                    <input type="number" oninput="updateTotals()" id="discount" class="form-control" value="0" min="0" max="100"
                        style="border: 1px solid #dee2e6; padding: 8px 12px;">
                </div>

                <!-- Métodos de pago -->
                <div class="payment-section mb-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-2" style="color: #E68A2E;">Método de pago 1</label>
                            <div class="row">
                                <div class="col-6">
                                    <select id="pagoMetodo1" class="form-select"
                                        style="border: 1px solid #dee2e6; padding: 8px 12px; font-size: 0.9rem;">
                                        <option>Efectivo</option>
                                        <option>Tarjeta</option>
                                        <option>Transferencia</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            style="background-color: #f8f9fa; border: 1px solid #dee2e6;">$</span>
                                        <input id="pagoMonto1" type="number" class="form-control" value="0" step="0.01"
                                            style="border: 1px solid #dee2e6;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold mb-2" style="color: #E68A2E;">Método de pago 2
                                (opcional)</label>
                            <div class="row">
                                <div class="col-6">
                                    <select id="pagoMetodo2" class="form-select"
                                        style="border: 1px solid #dee2e6; padding: 8px 12px; font-size: 0.9rem; color: #6c757d;">
                                        <option>Ninguno</option>
                                        <option>Efectivo</option>
                                        <option>Tarjeta</option>
                                        <option>Transferencia</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            style="background-color: #f8f9fa; border: 1px solid #dee2e6;">$</span>
                                        <input id="pagoMonto2" type="text" class="form-control" placeholder="Monto"
                                            style="border: 1px solid #dee2e6; color: #6c757d;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div class="notes-section mb-4">
                    <label class="form-label fw-semibold mb-2" style="color: #E68A2E;">Notas</label>
                    <textarea id="notes" class="form-control" rows="3" placeholder="Notas adicionales"
                        style="border: 1px solid #dee2e6; resize: vertical;"></textarea>
                </div>

                <!-- Totales -->
                <div class="totals-section mb-4">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="fw-bold" style="color: #2c5560;">Subtotal: <span id="subtotal"
                                    style="color: #E68A2E;">$0.00</span></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h4 class="fw-bold" style="color: #2c5560;">Total: <span id="total"
                                    style="color: #E68A2E;">$0.00</span></h4>
                        </div>
                    </div>
                </div>

                <!-- Botón de checkout -->
                <div class="checkout-section">
                    <button onclick="checkout()" class="btn btn-lg text-white px-5 py-3 fw-semibold"
                        style="background-color: #28a745; border: none; border-radius: 8px;">
                        Checkout
                    </button>
                </div>
            </div>
        </div>


        <!-- Hidden Form -->
        <form id="checkoutForm" action="{{route('storeSell')}}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="subtotal" id="formSubtotal"> <input type="hidden" name="total" id="formTotal">
            <input type="hidden" name="descuento" id="formDescuento">
            <input type="hidden" name="notas" id="formNotas">
            <input type="hidden" name="checkout" value="1"> <!-- A field to trigger form processing -->
            <input type="hidden" name="cartItems" id="formCartItems"> <!-- Hidden field for cart items -->
            <input type="hidden" name="pagoMetodo1" id="formPagoMetodo1">
            <input type="hidden" name="pagoMonto1" id="formPagoMonto1">
            <input type="hidden" name="pagoMetodo2" id="formPagoMetodo2">
            <input type="hidden" name="pagoMonto2" id="formPagoMonto2">
        </form>
    </div>
    <style>
        .card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #E68A2E;
            box-shadow: 0 0 0 0.2rem rgba(230, 138, 46, 0.25);
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .input-group-text {
            font-weight: 500;
        }

        .table tbody tr:hover {
            background-color: rgba(230, 138, 46, 0.05);
        }

        .header-section {
            border-left: 4px solid #E68A2E !important;
        }

        ::placeholder {
            color: #6c757d !important;
        }

        .form-select option:first-child {
            color: #6c757d;
        }
    </style>

    {{-- Script to mangae sells --}}
    <script src="{{ asset('js/newSellsController.js') }}"></script>
@endsection