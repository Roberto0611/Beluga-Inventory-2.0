@extends('layouts.app')

@section('content')
    {{-- Main Banner --}}
    <div class="inventory-header">
        <h2>Inventario📦</h2>
        <p>Control de Productos</p>
    </div>

    {{-- SearchBar --}}
    <div class="search-bar">
        <input type="text" class="searchBarIndex" id="searchInput" placeholder="Buscar productos...">
    </div>

    {{-- Buttons --}}
<div class="d-flex justify-content-center gap-2 p-2">
    <button type="button" class="btn btn-success" id="allProductsButton">Todos los productos</button>
    <button type="button" id="expireButton" class="btn btn-primary">Prontos a caducar</button>
    <button type="button" id="expiredButton" class="btn btn-primary">Productos caducados</button>
</div>

    {{-- Modal button --}}
    <button class="floating-action-btn" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <span class="plus-icon"></span>
    </button>

    {{-- Modal --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar productos al inventario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form --}}
                    <form action="{{route('addProduct')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <select name="product_id" id="product_select" class="form-select"
                                aria-label="Default select example">
                                <option selected disabled>Busca el producto...</option>
                                @foreach ($catalog as $product)
                                    <option value="{{$product->ID}}">{{$product->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="caducidad" class="form-label">Fecha de caducidad</label>
                            <input type="date" class="form-control" id="" name="caducidad">
                        </div>
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="" name="cantidad">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Accordion with all the inventory items --}}
    <div class="container my-4">
        <div class="accordion custom-accordion" id="inventoryAccordion">

            @foreach ($stock as $catalogo_id => $items)
                @php
                    $catalogoNombre = $items->first()->catalogo->nombre;
                @endphp

                <div class="accordion-item custom-accordion-item mb-2 rounded">
                    <h2 class="accordion-header" id="heading{{ $catalogo_id }}">
                        <button class="accordion-button custom-accordion-button collapsed rounded" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $catalogo_id }}" aria-expanded="false"
                            aria-controls="collapse{{ $catalogo_id }}">
                            {{ $catalogoNombre }}
                        </button>
                    </h2>
                    <div id="collapse{{ $catalogo_id }}" class="accordion-collapse collapse"
                        aria-labelledby="heading{{ $catalogo_id }}" data-bs-parent="#inventoryAccordion">
                        <div class="accordion-body custom-accordion-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Unidades</th>
                                        <th>Fecha de caducidad</th>
                                        @if(Auth::user()->is_admin)
                                        <th>Acciones</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr data-inventory-id="{{ $item->id }}">
                                            <td>{{ $item->quantity }}</td>
                                            <td class="dateInfo">{{ $item->expiration_date }}</td>

                                            @if (Auth::user()->is_admin)
                                                <td>
                                                    <form action="{{route('reduceProduct')}}" method="POST" class="reduce-form">
                                                        @csrf
                                                        <input type="hidden" name="inventory_id" value="{{ $item->id }}">
                                                        <input type="hidden" name="current_quantity" value="{{ $item->quantity }}">
                                                        <input type="hidden" name="reduction_quantity" value="">
                                                        <button type="submit" class="btn btn-outline-danger">Reducir</button>                                                    
                                                    </form>
                                                </td>
                                            @endif
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Script to add colors to expire dates --}}
    <script src="{{ asset('js/expire_colors.js') }}"></script>
    {{-- Script to ask for reduce and also manage soon to expire --}}
    <script src="{{ asset('js/reduce-inventory.js') }}"></script>
    {{-- Script para búsqueda en tiempo real --}}
    <script src="{{ asset('js/inventory-search.js') }}"></script>
@endsection