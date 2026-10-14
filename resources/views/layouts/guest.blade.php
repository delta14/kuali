<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Kuali') }} - @yield('title', 'Acceso')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/kuali-base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/fonts/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fonts/poppins.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fonts/manrope.css') }}">

</head>
<body>
<div class="auth-page">
    <div class="auth-left">
        <div class="auth-left-inner">
            <div class="auth-logo">
                <div class="auth-logo-mark">K</div>
                <div class="auth-logo-text">KUALI</div>
            </div>

            @yield('content')
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-right-copy">
            <h2 class="auth-right-title">Digitaliza tu sabor</h2>
            <p class="auth-right-text">
                Convierte tu menú en una experiencia digital: actualiza precios,
                fotos y categorías en segundos, sin depender de impresión ni diseño.
            </p>
        </div>
    </div>
</div>
</body>
</html>
