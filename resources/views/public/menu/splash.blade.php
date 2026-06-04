<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $business->name }} - Menú</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fuente y RemixIcon --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    <style>
        :root {
            --dc-bg: #FFF9F5;
            --dc-primary: #5A3E2B;
            --dc-accent: #E87A4C;
        }
        *{ box-sizing:border-box; margin:0; padding:0; }
        body{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            font-family:"Poppins", system-ui, sans-serif;
            background: radial-gradient(circle at top, #F5E1D4, #FFF9F5);
            color:var(--dc-primary);
        }
        .splash-card{
            width:100%;
            max-width:420px;
            background:#fff;
            border-radius:28px;
            padding:2rem 2.2rem;
            box-shadow:0 24px 60px rgba(0,0,0,0.12);
            text-align:center;
        }
        .splash-logo{
            width:74px;
            height:74px;
            border-radius:24px;
            background:linear-gradient(135deg,#5A3E2B,#E87A4C);
            display:flex;
            align-items:center;
            justify-content:center;
            margin:0 auto 1rem;
            color:#fff;
            font-size:2.1rem;
            font-weight:700;
        }
        .splash-title{
            font-size:1.2rem;
            font-weight:600;
            margin-bottom:0.2rem;
        }
        .splash-sub{
            font-size:0.9rem;
            color:#8F7D6D;
            margin-bottom:0.6rem;
        }
        .splash-table{
            display:inline-flex;
            align-items:center;
            gap:0.35rem;
            padding:0.2rem 0.7rem;
            border-radius:999px;
            background:rgba(232,122,76,0.06);
            color:#C25A33;
            font-size:0.8rem;
            margin-bottom:1.2rem;
        }
        .splash-table i{ font-size:1rem; }
        .splash-footer{
            margin-top:0.6rem;
            font-size:0.78rem;
            color:#B09C8B;
        }
        .splash-spinner{
            margin-top:0.8rem;
            width:22px;
            height:22px;
            border-radius:50%;
            border:2px solid rgba(90,62,43,0.18);
            border-top-color:var(--dc-accent);
            animation:spin 0.8s linear infinite;
            margin-inline:auto;
        }
        @keyframes spin{
            to{ transform:rotate(360deg); }
        }
    </style>

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
