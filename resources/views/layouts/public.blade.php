<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Menú digital' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fuente + iconos (locales) --}}
    <link rel="stylesheet" href="{{ asset('vendor/fonts/poppins.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/remixicon/remixicon.css') }}">

    <link rel="stylesheet" href="{{ asset('css/public-menu.css') }}">
</head>
<body class="pm-body">

<div class="pm-app">
    @yield('content')
</div>

@stack('scripts')
</body>
</html>
