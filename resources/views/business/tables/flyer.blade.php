<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QR {{ $table->name }} – {{ $business->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fuente --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary:#8B5CF6;
            --primary-dark:#6D28D9;
            --accent:#FF6B6B;
            --bg:#FFF7FB;
            --text:#1F1235;
            --muted:#6B6B80;
        }
        *{ box-sizing:border-box; }
        html,body{
            margin:0;
            padding:0;
            background:radial-gradient(circle at top,#ffe5f1 0,#fff 40%,#f6f3ff 100%);
            font-family:'Poppins',system-ui,-apple-system,BlinkMacSystemFont,sans-serif;
            color:var(--text);
        }
        body{
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            padding:24px;
        }
        .flyer{
            width:780px;
            max-width:100%;
            background:#fff;
            border-radius:32px;
            box-shadow:0 24px 60px rgba(31,18,53,.18);
            overflow:hidden;
        }
        .flyer-header{
            padding:24px 32px 20px;
            background:linear-gradient(135deg, var(--primary-dark), var(--primary));
            color:#fff;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .flyer-title-main{
            font-size:1.9rem;
            font-weight:600;
            letter-spacing:.02em;
        }
        .flyer-subtitle{
            font-size:.9rem;
            opacity:.9;
        }
        .flyer-pill{
            background:rgba(255,255,255,.16);
            border-radius:999px;
            padding:6px 14px;
            font-size:.8rem;
        }
        .flyer-body{
            display:flex;
            padding:32px;
            gap:32px;
        }
        .flyer-left{
            flex:1;
            background:linear-gradient(145deg,#faf5ff,#ffe9f0);
            border-radius:28px;
            padding:24px 20px;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            text-align:center;
        }
        .flyer-left h3{
            font-size:.9rem;
            letter-spacing:.16em;
            text-transform:uppercase;
            color:var(--muted);
            margin:0 0 18px;
        }
        .flyer-qr-frame{
            background:#fff;
            padding:22px;
            border-radius:24px;
            box-shadow:0 18px 45px rgba(148,118,255,.25);
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:230px;
            min-width:230px;
        }
        .flyer-qr-frame img{
            display:block;
            width:210px;
            height:210px;
            object-fit:contain;
        }
        .flyer-left p{
            margin:20px 0 0;
            font-size:.9rem;
            color:var(--muted);
        }
        .flyer-right{
            flex:1.2;
            padding-top:6px;
        }
        .flyer-right-title{
            font-size:1.4rem;
            font-weight:600;
        }
        .flyer-right-title span{
            color:var(--accent);
        }
        .flyer-right-sub{
            margin-top:10px;
            font-size:.92rem;
            color:var(--muted);
            line-height:1.6;
        }
        .flyer-steps{
            margin-top:20px;
            padding-left:18px;
            font-size:.9rem;
            color:var(--muted);
        }
        .flyer-steps li{
            margin-bottom:10px;
        }
        .flyer-footer{
            padding:16px 32px 22px;
            border-top:1px solid #f1e9ff;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-size:.78rem;
            color:var(--muted);
        }
        .flyer-brand{
            font-weight:500;
        }
        .flyer-url{
            font-weight:500;
            color:var(--primary-dark);
        }

        @media print{
            body{
                padding:0;
                background:#fff;
            }
            .flyer{
                box-shadow:none;
                border-radius:0;
                width:100%;
            }
        }
    </style>
</head>
<body>
<div class="flyer">
    {{-- HEADER --}}
    <div class="flyer-header">
        <div>
            <div class="flyer-title-main">{{ $business->name }}</div>
            <div class="flyer-subtitle">Menú digital para tus clientes</div>
        </div>
        <div class="flyer-pill">
            Mesa: <strong>{{ $table->name }}</strong>
        </div>
    </div>

    {{-- CUERPO --}}
    <div class="flyer-body">
        {{-- LADO IZQUIERDO: QR --}}
        <div class="flyer-left">
            <h3>ESCANEA PARA VER EL MENÚ</h3>

            <div class="flyer-qr-frame">
                @if ($table->qr_path)
                    <img src="{{ asset('storage/' . $table->qr_path) }}"
                         alt="QR {{ $table->name }}">
                @else
                    <span style="font-size:.85rem; color:var(--muted);">
                        QR no disponible
                    </span>
                @endif
            </div>

            <p>
                Muestra este código al cliente para que <strong>vea el menú</strong>  
                y envíe su pedido desde el celular.
            </p>
        </div>

        {{-- LADO DERECHO: TEXTO TIPO POSTER --}}
        <div class="flyer-right">
            <div class="flyer-right-title">
                Ordena y paga <span>desde tu celular</span>
            </div>
            <p class="flyer-right-sub">
                Los clientes solo necesitan abrir la cámara de su teléfono,
                escanear el código y podrán ver el menú de <strong>{{ $business->name }}</strong>,
                elegir sus platillos y enviar su pedido al mesero o barra.
            </p>

            <ol class="flyer-steps">
                <li>Abre la cámara o lector de códigos QR de tu teléfono.</li>
                <li>Apunta al código hasta que aparezca la notificación.</li>
                <li>Toca la notificación para abrir el menú digital.</li>
                <li>Elige tus platillos, confirma el pedido… ¡y listo!</li>
            </ol>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="flyer-footer">
        <span class="flyer-brand">Kuali · Menú digital QR</span>
        <span class="flyer-url">{{ request()->getHost() }}</span>
    </div>
</div>
</body>
</html>
