<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Menú digital' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fuente + iconos --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    <link rel="stylesheet" href="{{ asset('css/public-menu.css') }}">
</head>
<body class="pm-body">

<div class="pm-app">
    @yield('content')
</div>

@stack('scripts')
</body>
</html>
