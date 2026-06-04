@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <h1 class="auth-heading">Inicia sesión</h1>
    <p class="auth-subheading">
        Accede a tu panel para gestionar tu menú y pedidos.
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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label for="email" class="auth-label">Correo electrónico</label>
            <input id="email"
                   type="email"
                   name="email"
                   class="auth-input"
                   placeholder="Correo electrónico"
                   value="{{ old('email') }}"
                   required
                   autofocus>
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

        <button type="submit" class="auth-primary-btn">
            Iniciar sesión
        </button>
    </form>

    <p class="auth-bottom-text">
        Versión demo de Kuali
        <!--<a href="{{ route('register') }}">Crea una cuenta</a>-->
    </p>
@endsection
