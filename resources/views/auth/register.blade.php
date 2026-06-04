@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
    <h1 class="auth-heading">Crea una cuenta</h1>
    <p class="auth-subheading">
        Regístrate y aprovecha 30 días gratis para probar Kuali.
    </p>

    @if ($errors->any())
        <div class="auth-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-field">
            <label for="name" class="auth-label">Nombre completo</label>
            <input id="name"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="auth-input"
                   placeholder="Nombre completo"
                   required
                   autofocus>
        </div>

        <div class="auth-field">
            <label for="email" class="auth-label">Correo electrónico</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="auth-input"
                   placeholder="Correo electrónico"
                   required>
        </div>

        <div class="auth-field">
            <label for="password" class="auth-label">Contraseña</label>
            <input id="password"
                   type="password"
                   name="password"
                   class="auth-input"
                   placeholder="Contraseña"
                   required>
        </div>

        <div class="auth-field">
            <label for="password_confirmation" class="auth-label">Confirmar contraseña</label>
            <input id="password_confirmation"
                   type="password"
                   name="password_confirmation"
                   class="auth-input"
                   placeholder="Confirmar contraseña"
                   required>
        </div>

        <button type="submit" class="auth-primary-btn">
            Registrarse
        </button>
    </form>

    <p class="auth-bottom-text">
        ¿Ya tienes una cuenta?
        <a href="{{ route('login') }}">Inicia sesión</a>
    </p>
@endsection
