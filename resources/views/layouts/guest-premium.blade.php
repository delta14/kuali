<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kuali - Acceso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/kuali-base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth-premium.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-premium-page">

    <div class="auth-premium-card">

        {{-- IZQUIERDA --}}
        <div class="auth-premium-form">
            @yield('content')
        </div>

        {{-- DERECHA --}}
        <div class="auth-premium-image">
            <div class="auth-premium-footer">
                © {{ date('Y') }} Kuali — Soluciones para restaurantes
            </div>
        </div>

    </div>

</div>

</body>
</html>
