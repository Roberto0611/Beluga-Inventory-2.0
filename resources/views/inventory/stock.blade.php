@extends('layouts.app')

@section('content')
    {{-- Main Banner --}}
    <div class="inventory-header">
        <h2>Inventario📦</h2>
        <p>Control de Productos</p>
    </div>

    <!-- Alerts -->
    @include('layouts.alerts') 

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
                        <div class="mb-3">
                            <label for="location_id" class="form-label">Ubicación</label>
                            <select name="location_id" id="location_select" class="form-select"
                                aria-label="Default select example" required>
                                <option selected disabled>Selecciona una ubicación...</option>
                                @foreach ($locations as $location)
                                    <option value="{{$location->id}}">{{$location->name}}</option>
                                @endforeach
                            </select>
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
                    // Agrupar por ubicación (puede ser null)
                    $groupedByLocation = $items->groupBy(function($item){
                        return optional($item->location)->name ?? 'Sin ubicación';
                    });
                    $uniqueLocations = $groupedByLocation->keys();
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
                            @php
                                $totalUnits = $items->sum('quantity');
                                $totalLocations = $groupedByLocation->count();
                                $earliest = $items->pluck('expiration_date')->filter()->sort()->first();
                                $statusLabel = null; $statusClass = null;
                                if($earliest){
                                    $diffDays = \Carbon\Carbon::parse($earliest)->diffInDays(now(), false);
                                    // diff negative => future date (days until), positive => already past
                                    if($diffDays > 0){
                                        $statusLabel = 'Caducado'; $statusClass='inv-badge-expired';
                                    } elseif($diffDays >= -15){
                                        $statusLabel = 'Pronto a caducar'; $statusClass='inv-badge-soon';
                                    } else {
                                        $statusLabel = 'Fresco'; $statusClass='inv-badge-fresh';
                                    }
                                }
                            @endphp
                            <div class="inventory-summary-bar d-flex flex-wrap align-items-center gap-3 mb-3">
                                <div class="summary-chip"><span class="chip-label">Unidades</span><span class="chip-value">{{ $totalUnits }}</span></div>
                                <div class="summary-chip"><span class="chip-label">Ubicaciones</span><span class="chip-value">{{ $totalLocations }}</span></div>
                                @if($earliest)
                                    <div class="summary-chip"><span class="chip-label">Primera caducidad</span><span class="chip-value">{{ $earliest }}</span></div>
                                    <div class="summary-status {{ $statusClass }}">{{ $statusLabel }}</div>
                                @endif
                            </div>
                            {{-- Selector de ubicación para filtrar --}}
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <label class="fw-semibold mb-0">Seleccionar ubicación:</label>
                                <select class="form-select form-select-sm product-location-filter" data-product-id="{{ $catalogo_id }}" style="max-width: 260px;">
                                    <option value="" selected>Todas las ubicaciones ({{ $totalLocations }})</option>
                                    @foreach ($groupedByLocation as $locName => $locItems)
                                        <option value="{{ $locName }}">{{ $locName }} ({{ $locItems->sum('quantity') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            @foreach ($groupedByLocation as $locationName => $locationItems)
                                @php
                                    $icon = '📦';
                                    $lower = Str::lower($locationName);
                                    if(Str::contains($lower, ['auto','carro','camion','van'])) $icon='🚚';
                                    elseif(Str::contains($lower, ['consultorio','tienda','store','depósito','deposito'])) $icon='🏬';
                                    elseif(Str::contains($lower, ['mostrador','caja','front'])) $icon='🛒';
                                    elseif(Str::contains($lower, ['sin ubicación','sin ubicacion'])) $icon='❔';
                                @endphp
                                <div class="location-block" data-location-block="{{ $locationName }}" data-product-id="{{ $catalogo_id }}">
                                    @php
                                        $locUnits = $locationItems->sum('quantity');
                                        $locEarliest = $locationItems->pluck('expiration_date')->filter()->sort()->first();
                                        $locStatusClass = null; $locStatusIcon='';
                                        if($locEarliest){
                                            $locDiff = \Carbon\Carbon::parse($locEarliest)->diffInDays(now(), false);
                                            if($locDiff > 0){ $locStatusClass='badge-expired'; $locStatusIcon='⚠️'; }
                                            elseif($locDiff >= -15){ $locStatusClass='badge-soon'; $locStatusIcon='⏳'; }
                                            else { $locStatusClass='badge-fresh'; $locStatusIcon='✅'; }
                                        }
                                    @endphp
                                    <div class="location-block-header d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <span class="location-header-icon">{{ $icon }}</span>
                                        <h5 class="m-0 location-header-text">{{ Str::upper($locationName) }}</h5>
                                        <span class="loc-metric badge bg-light text-dark border">{{ $locUnits }} uds</span>
                                        @if($locEarliest)
                                            <span class="loc-exp badge {{ $locStatusClass }}">{{ $locStatusIcon }} {{ $locEarliest }}</span>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto toggle-loc" data-target="loc-{{ $catalogo_id }}-{{ Str::slug($locationName,'-') }}">Ocultar</button>
                                    </div>
                                    <div id="loc-{{ $catalogo_id }}-{{ Str::slug($locationName,'-') }}" class="location-table-wrapper">
                                    <table class="table table-sm align-middle mb-4">
                                        <thead>
                                            <tr>
                                                <th>Unidades</th>
                                                <th>Fecha de caducidad</th>
                                                    <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($locationItems as $item)
                                                <tr data-inventory-id="{{ $item->id }}" data-location="{{ $locationName }}" data-product-id="{{ $catalogo_id }}" class="inventory-row">
                                                    <td>{{ $item->quantity }}</td>
                                                    <td class="dateInfo">{{ $item->expiration_date ?? 'No asignada' }}</td>
                                                        <td>
                                                            <form action="{{route('reduceProduct')}}" method="POST" class="reduce-form">
                                                                @csrf
                                                                <input type="hidden" name="inventory_id" value="{{ $item->id }}">
                                                                <input type="hidden" name="current_quantity" value="{{ $item->quantity }}">
                                                                <input type="hidden" name="reduction_quantity" value="">
                                                                <button type="submit" class="btn btn-outline-danger btn-sm">Reducir</button>
                                                            </form>
                                                        </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            @endforeach
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
    {{-- Script para filtrar por ubicación dentro de cada producto --}}
    <script src="{{ asset('js/location-filter.js') }}"></script>
    {{-- Script UI adicional para colapsar bloques y colorear badges --}}
    <script src="{{ asset('js/location-ui.js') }}"></script>
@endsection