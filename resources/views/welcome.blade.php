@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex flex-column min-vh-100 bg-light">

    <!-- Main content -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="text-center bg-white rounded shadow-lg pt-5 pb-4" style="max-width: 500px; width: 100%;">
            <h1 class="mb-3" style="color: #E68A2E;">Bienvenido a Beluga Pet Shed</h1>
            <p class="lead mb-4 text-secondary">
                Selecciona una opción para comenzar tu jornada laboral.
            </p>
            <div class="d-grid gap-3 px-4"> 
                <a href="{{ route('inventory') }}" class="btn btn-beluga-primary btn-lg rounded-pill shadow-sm py-3">
                    Inventario 📦
                </a>
                <a href="{{ route('sells') }}" class="btn btn-beluga-secondary btn-lg rounded-pill shadow-sm py-3">
                    Punto de Venta 🛒
                </a>
            </div>
        </div>
    </main>
    
    <footer class="footer mt-auto py-3 bg-light text-center text-muted">
        <div class="container">
            <span>&copy; {{ date('Y') }} Beluga Pet Shed. Todos los derechos reservados.</span>
        </div>
    </footer>
</div>
@endsection