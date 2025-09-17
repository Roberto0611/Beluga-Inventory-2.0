@extends('layouts.app')

@section('content')
    {{-- Main Banner --}}
    <div class="inventory-header">
        <h2>Catalogo📚</h2>
        <p>Control de Productos en el catalogo</p>
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
                        <p class="mb-0 product-price"> Precio: ${{$item->Precio}} </p>
                    </div>
                    <div class="product-actions d-flex flex-column align-items-end gap-2">

                        {{-- Form to delete --}}
                        <form action="/deleteCatalog/{{$item->ID}}" method="POST" onsubmit="return confirmDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar⚠️</button>
                        </form>
                        {{-- Button with all the data in case of editing --}}
                        <button data-bs-toggle="modal" data-bs-target="#editModal" class="btn btn-primary edit-btn"
                            data-id="{{$item->ID}}" data-name="{{$item->nombre}}" data-price="{{$item->Precio}}"
                            data-imgurl="{{$item->imagenURL}}"
                            data-ideal="{{$item->IdealPetShed}}">Editar✍️</button>
                    </div>
                </div>

            @endforeach

        </div>
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar productos al Catalogo📚</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form --}}
                    <form action="{{route('addCatalog')}}" method="POST">
                        @csrf

                        <div class="mb-3" >
                            <label for="name" class="form-label">Nombre del producto</label>
                            <input required type="text" class="form-control" id="name" name="name">
                        </div>

                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input required type="number" class="form-control" id="precio" name="precio">
                        </div>

                        <div class="mb-3">
                            <label for="imgUrl" class="form-label">URL de la imagen</label>
                            <input type="text" class="form-control" id="imgUrl" name="imgUrl">
                        </div>

                        <div class="mb-3">
                            <label for="ideal" class="form-label">Ideal Pet Shed </label>
                            <input type="number" class="form-control" id="ideal" name="ideal">
                        </div>


                        <button type="submit" class="btn btn-primary">Registrar producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Editar producto✍️</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Nombre del producto</label>
                            <input type="text" class="form-control" id="edit-name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="edit-precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="edit-precio" name="precio">
                        </div>
                        <div class="mb-3">
                            <label for="edit-imgUrl" class="form-label">URL de la imagen</label>
                            <input type="text" class="form-control" id="edit-imgUrl" name="imgUrl">
                        </div>
                        <div class="mb-3">
                            <label for="edit-ideal" class="form-label">Ideal Pet Shed</label>
                            <input type="number" class="form-control" id="edit-ideal" name="ideal">
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script for searching --}}
    <script src="{{ asset('js/catalog-search.js') }}"></script>
    {{-- Script to fill the edit modal --}}
    <script src="{{ asset('js/edit-catalog.js') }}"></script>
    {{-- Script to ask for delete --}}
    <script src="{{ asset('js/confirm-delete.js') }}"></script>
@endsection 