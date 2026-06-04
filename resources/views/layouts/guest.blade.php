<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Kuali') }} - @yield('title', 'Acceso')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/kuali-base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet"/>

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
