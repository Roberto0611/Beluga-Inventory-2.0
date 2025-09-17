@extends('layouts.app')

@section('content')
    {{-- Main Banner --}}
    <div class="inventory-header">
        <h2>Plan🛒</h2>
        <p>Guia de compra, lista de productos faltantes.</p>
    </div>

    {{-- SearchBar --}}
    <div class="search-bar">
        <input type="text" class="searchBarIndex" id="searchInput" placeholder="Buscar productos...">
    </div>

    {{-- Product List --}}
    <div class="container my-4">
        <div class="d-grid gap-3">

            @foreach ($catalog as $item)

                <div class="product-item d-flex align-items-center bg-white rounded shadow-sm p-3 tableElements">
                    <div class="product-image me-3">
                        <img src="{{$item->imagenURL}}" alt="Imagen del Producto" class="imgProduct">
                    </div>
                    <div class="product-details flex-grow-1 text-center">
                        <h5 class="mb-1 product-name">{{$item->nombre}}</h5>
                        <p class="mb-0 text-muted">Ideal en Pet Shed: {{$item->IdealPetShed}}</p>
                        <p class="mb-0 text-muted">Cantidad actual: {{$item->current_stock}}</p>
                        <p class="mb-0 text-muted faltante">Cantidad Faltante: {{$item->missing_stock}}</p>
                    </div>
                </div>

            @endforeach

        </div>
    </div>

    
    {{-- Script for searching --}}
    <script src="{{ asset('js/catalog-search.js') }}"></script>
    {{-- Script for colors --}}
    <script src="{{ asset('js/idealColors.js') }}"></script>
@endsection 