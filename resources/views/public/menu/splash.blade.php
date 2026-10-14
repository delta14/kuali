<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $business->name }} - Menú</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fuente y RemixIcon (locales) --}}
    <link rel="stylesheet" href="{{ asset('vendor/fonts/poppins.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/remixicon/remixicon.css') }}">

    <link rel="stylesheet" href="{{ asset('css/pages/public-menu-splash.css') }}">

    <script>
        // Redirigir al menú en 1s, conservando ?table=...
        window.addEventListener('DOMContentLoaded', () => {
            const search = window.location.search; // ?table=9
            const nextUrl = "{{ route('public.menu.show', $business->slug) }}" + search;
            setTimeout(() => {
                window.location.href = nextUrl;
            }, 1000);
        });
    </script>
</head>
<body>
    <div class="splash-card">
        <div class="splash-logo">
            {{-- Si luego tienes logo, aquí puedes usar <img> --}}
            {{ mb_substr($business->name, 0, 1) }}
        </div>

        <div class="splash-title">Bienvenido(a)</div>
        <div class="splash-sub">{{ $business->name }}</div>

        @if($table)
            <div class="splash-table">
                <i class="ri-restaurant-line"></i>
                <span>Mesa {{ $table }}</span>
            </div>
        @endif

        <div class="splash-footer">
            Cargando tu menú digital…
            <div class="splash-spinner"></div>
        </div>
    </div>
</body>
</html>
