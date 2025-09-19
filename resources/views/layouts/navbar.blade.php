<nav class="navbar navbar-expand-lg navbar-dark bg-beluga-orange shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{route('index')}}">
            <span class="fw-bold">Beluga Pet Shed</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center" href="{{route('inventory')}}">
                        Inventario 📦
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center" href="{{route('catalog')}}">
                        Catálogo 📚
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center" href="{{route('plan')}}">
                        Plan 🗓️
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Bienvenido @auth {{Auth::user()->name}} @endauth 👋
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser" style="background-color: #393E46; border: none;">
                        <li>
                                <a href="{{route('logout')}}" class="dropdown-item text-white" style="background-color: transparent;">
                                    Cerrar Sesión 🚪</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
