@extends('layouts.appAuth')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center"
        style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <div class="card shadow-lg border-0"
                        style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(30, 30, 30, 0.95); border: 1px solid rgba(208, 122, 42, 0.2);">
                        <div class="card-body p-5">

                            <!-- Logo Section -->
                            <div class="text-center mb-4">
                                <div class="logo-container mb-3"
                                    style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #d07a2a, #e89650); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(208, 122, 42, 0.4);">
                                    <i class="" style="font-size: 32px; color: white;">🐳</i>
                                </div>
                                <h2 class="fw-bold mb-1" style="color: #ffffff;">Bienvenido a <br> Beluga Pet Shed</h2>
                                <p class="text-muted" style="color: #b0b0b0 !important;">Inicia sesión en tu cuenta</p>
                            </div>

                            <!-- Login Form -->
                            <form method="POST" action="{{route('inicia-sesion')}}">
                                @csrf

                                {{-- show errors --}}
                                @if ($errors->any())
                                    <div class="alert custom-error-alert mb-4" role="alert">
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li style="color: #d07a2a;">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- Email Field -->
                                <div class="mb-4">
                                    <label for="user" class="form-label fw-semibold" style="color: #e0e0e0;">Usuario </label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0"
                                            style="background: #3a3a3a; border-radius: 12px 0 0 12px;">
                                            <i class="" style="color: #d07a2a;">✉️</i>
                                        </span>
                                        <input id="user" type="text"
                                            class="form-control border-0" name="user"
                                            required autofocus
                                            style="background: #3a3a3a; border-radius: 0 12px 12px 0; padding: 12px 16px; font-size: 15px; color: #ffffff;"
                                            placeholder="Nombre de usuario">
                                    </div>
                                </div>

                                <!-- Password Field -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold"
                                        style="color: #e0e0e0;">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0"
                                            style="background: #3a3a3a; border-radius: 12px 0 0 12px;">
                                            <i class="" style="color: #d07a2a;">🔐</i>
                                        </span>
                                        <input id="password" type="password"
                                            class="form-control border-0"
                                            name="password" required
                                            style="background: #3a3a3a; border-radius: 0 12px 12px 0; padding: 12px 16px; font-size: 15px; color: #ffffff;"
                                            placeholder="Tu contraseña">
                                    </div>
                                </div>

                                <!-- Login Button -->
                                <div class="mb-4">
                                    <button type="submit" class="btn w-100 fw-semibold py-3" style="background: linear-gradient(135deg, #d07a2a, #e89650); 
                                                   border: none; 
                                                   border-radius: 12px; 
                                                   color: white; 
                                                   font-size: 16px; 
                                                   transition: all 0.3s ease;
                                                   box-shadow: 0 4px 15px rgba(208, 122, 42, 0.4);"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(208, 122, 42, 0.6)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(208, 122, 42, 0.4)';">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-4">
                        <p class="text-muted" style="font-size: 13px; color: #888888 !important;">
                            © {{ date('Y') }} Beluga Pet Shed. Todos los derechos reservados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection