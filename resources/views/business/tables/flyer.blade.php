<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QR {{ $table->name }} – {{ $business->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Fonts para diseño editorial premium --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Remix Icons para íconos elegantes --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    <style>
        :root {
            /* Tema por defecto: Classic Lavanda */
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #f43f5e;
            --bg-gradient: linear-gradient(135deg, #f5f3ff 0%, #fff 50%, #fdf2f8 100%);
            --card-bg: #ffffff;
            --text: #1e1b4b;
            --muted: #4f46e5;
            --font-title: 'Outfit', sans-serif;
            --qr-shadow: rgba(99, 102, 241, 0.15);
            --border-color: rgba(99, 102, 241, 0.1);
        }

        /* Temas definidos por clases */
        .theme-dark-luxury {
            --primary: #d4af37;
            --primary-dark: #b8901c;
            --accent: #f3e5ab;
            --bg-gradient: linear-gradient(135deg, #0f0a1c 0%, #07040d 100%);
            --card-bg: rgba(255, 255, 255, 0.03);
            --text: #ffffff;
            --muted: #9ca3af;
            --qr-shadow: rgba(212, 175, 55, 0.2);
            --border-color: rgba(212, 175, 55, 0.25);
            --font-title: 'Playfair Display', serif;
        }

        .theme-terracotta {
            --primary: #c2410c;
            --primary-dark: #9a3412;
            --accent: #ea580c;
            --bg-gradient: linear-gradient(135deg, #fffbeb 0%, #fff 50%, #ffedd5 100%);
            --card-bg: #ffffff;
            --text: #431407;
            --muted: #9a3412;
            --qr-shadow: rgba(194, 65, 12, 0.15);
            --border-color: rgba(194, 65, 12, 0.12);
            --font-title: 'Outfit', sans-serif;
        }

        .theme-forest {
            --primary: #059669;
            --primary-dark: #047857;
            --accent: #10b981;
            --bg-gradient: linear-gradient(135deg, #f0fdf4 0%, #fff 50%, #ecfdf5 100%);
            --card-bg: #ffffff;
            --text: #064e3b;
            --muted: #047857;
            --qr-shadow: rgba(5, 150, 105, 0.15);
            --border-color: rgba(5, 150, 105, 0.12);
            --font-title: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f1f3f7;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
            transition: background 0.3s ease;
        }

        /* Barra de herramientas / Personalizador (No se imprime) */
        .customizer {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            padding: 14px 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
            justify-content: space-between;
            width: 800px;
            max-width: 100%;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            z-index: 100;
        }

        .customizer-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .customizer-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .theme-swatches {
            display: flex;
            gap: 8px;
        }

        .swatch {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .swatch:hover {
            transform: scale(1.15);
        }

        .swatch.active {
            border-color: #000;
            transform: scale(1.1);
        }

        .swatch-classic { background: linear-gradient(135deg, #6366f1, #f43f5e); }
        .swatch-dark { background: linear-gradient(135deg, #0f0a1c, #d4af37); }
        .swatch-terracotta { background: linear-gradient(135deg, #c2410c, #ea580c); }
        .swatch-forest { background: linear-gradient(135deg, #059669, #10b981); }

        .btn-toggle-group {
            display: flex;
            background: #e5e7eb;
            padding: 3px;
            border-radius: 10px;
        }

        .btn-toggle {
            background: transparent;
            border: none;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4b5563;
            border-radius: 7px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-toggle.active {
            background: #ffffff;
            color: #111827;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .btn-print {
            background: #111827;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(17, 24, 39, 0.2);
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: #1f2937;
            transform: translateY(-1px);
        }

        /* Contenedor del Flyer */
        .flyer-container {
            width: 800px;
            max-width: 100%;
            perspective: 1000px;
        }

        .flyer {
            background: var(--bg-gradient);
            border-radius: 36px;
            box-shadow: 0 30px 70px rgba(30, 27, 75, 0.15);
            padding: 40px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 520px;
            border: 1px solid var(--border-color);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Elemento Decorativo de Fondo */
        .flyer::before {
            content: "";
            position: absolute;
            top: -20%;
            right: -20%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
            border-radius: 50%;
        }

        /* ORIENTACIÓN VERTICAL (TENT CARD) */
        .flyer--vertical {
            width: 480px;
            margin: 0 auto;
            min-height: 680px;
            text-align: center;
        }

        .flyer--vertical .flyer-content {
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .flyer--vertical .flyer-right {
            align-items: center;
            text-align: center;
        }

        .flyer--vertical .steps-list {
            grid-template-columns: 1fr;
            gap: 15px;
            text-align: left;
            max-width: 320px;
            margin: 0 auto;
        }

        /* HEADER DEL FLYER */
        .flyer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }

        .flyer--vertical .flyer-header {
            flex-direction: column;
            gap: 18px;
            margin-bottom: 25px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo-ring {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--card-bg);
            border: 1.5px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
        }

        .brand-logo-ring i {
            font-size: 1.4rem;
            color: var(--primary);
        }

        .brand-name {
            font-family: var(--font-title);
            font-size: 1.7rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
        }

        .table-badge {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 8px 18px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.03);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text);
        }

        .table-badge span {
            color: var(--primary);
            font-size: 1.1rem;
        }

        /* CONTENIDO PRINCIPAL */
        .flyer-content {
            display: flex;
            gap: 40px;
            align-items: center;
            flex: 1;
            position: relative;
            z-index: 2;
        }

        /* LADO IZQUIERDO: QR */
        .flyer-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .qr-wrapper {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 28px;
            box-shadow: 0 20px 45px var(--qr-shadow);
            border: 1px solid var(--border-color);
            position: relative;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .qr-wrapper:hover {
            transform: translateY(-4px);
        }

        .qr-image-container {
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 18px;
            padding: 10px;
        }

        .qr-image-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .qr-caption {
            margin-top: 14px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* LADO DERECHO: TEXTO / INSTRUCCIONES */
        .flyer-right {
            flex: 1.2;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .flyer-headline {
            font-family: var(--font-title);
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--text);
        }

        .flyer-headline span {
            color: var(--primary);
            position: relative;
            display: inline-block;
        }

        .flyer-headline span::after {
            content: "";
            position: absolute;
            bottom: 4px;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent);
            border-radius: 2px;
            opacity: 0.4;
        }

        .flyer-description {
            font-size: 0.95rem;
            line-height: 1.5;
            color: var(--text);
            opacity: 0.85;
        }

        /* LISTA DE PASOS CON ICONOS */
        .steps-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            width: 100%;
            margin-top: 5px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .step-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--primary);
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }

        .step-text {
            display: flex;
            flex-direction: column;
        }

        .step-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text);
        }

        .step-desc {
            font-size: 0.78rem;
            color: var(--text);
            opacity: 0.75;
            line-height: 1.4;
        }

        /* FOOTER DEL FLYER */
        .flyer-footer {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--text);
            opacity: 0.7;
            position: relative;
            z-index: 2;
        }

        .flyer--vertical .flyer-footer {
            margin-top: 30px;
            flex-direction: column;
            gap: 10px;
        }

        .footer-brand {
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .footer-link {
            font-weight: 600;
            color: var(--primary-dark);
            text-decoration: none;
        }

        /* AJUSTES PARA IMPRESIÓN */
        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }
            .customizer {
                display: none !important;
            }
            .flyer {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                min-height: auto !important;
                padding: 0 !important;
            }
            .flyer--vertical {
                width: 100% !important;
                max-width: 480px !important;
                margin: 0 auto !important;
            }
            .qr-wrapper {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
</head>
<body>

    {{-- BARRA DE PERSONALIZACIÓN (NO SE IMPRIME) --}}
    <div class="customizer">
        <div class="customizer-section">
            <span class="customizer-label">Estilo / Tema</span>
            <div class="theme-swatches">
                <div class="swatch swatch-classic active" data-theme="classic" title="Classic Lavanda"></div>
                <div class="swatch swatch-dark" data-theme="dark" title="Luxury Dark"></div>
                <div class="swatch swatch-terracotta" data-theme="terracotta" title="Warm Terracotta"></div>
                <div class="swatch swatch-forest" data-theme="forest" title="Emerald Garden"></div>
            </div>
        </div>

        <div class="customizer-section">
            <span class="customizer-label">Orientación</span>
            <div class="btn-toggle-group">
                <button class="btn-toggle active" data-orientation="horizontal">
                    <i class="ri-layout-row-line"></i> Horizontal
                </button>
                <button class="btn-toggle" data-orientation="vertical">
                    <i class="ri-layout-col-line"></i> Vertical (Mesa)
                </button>
            </div>
        </div>

        <div>
            <button class="btn-print" onclick="window.print()">
                <i class="ri-printer-line"></i> Imprimir Flyer
            </button>
        </div>
    </div>

    {{-- CONTENEDOR DEL FLYER --}}
    <div class="flyer-container">
        <div class="flyer flyer--horizontal" id="flyer-card">
            
            {{-- ENCABEZADO --}}
            <div class="flyer-header">
                <div class="brand-section">
                    <div class="brand-logo-ring">
                        <i class="ri-restaurant-line"></i>
                    </div>
                    <span class="brand-name">{{ $business->name }}</span>
                </div>
                <div class="table-badge">
                    <i class="ri-map-pin-line"></i> Mesa <span>{{ $table->name }}</span>
                </div>
            </div>

            {{-- CUERPO --}}
            <div class="flyer-content">
                
                {{-- LADO IZQUIERDO: QR --}}
                <div class="flyer-left">
                    <div class="qr-wrapper">
                        <div class="qr-image-container">
                            @if ($table->qr_path)
                                <img src="{{ asset('storage/' . $table->qr_path) }}" alt="QR Mesa {{ $table->name }}">
                            @else
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #6b7280; font-size: 0.85rem;">
                                    <i class="ri-qr-code-line" style="font-size: 3rem; margin-bottom: 10px;"></i>
                                    QR no generado
                                </div>
                            @endif
                        </div>
                        <div class="qr-caption">
                            <i class="ri-scan-2-line"></i> Escanear menú
                        </div>
                    </div>
                </div>

                {{-- LADO DERECHO: TEXTO E INSTRUCCIONES --}}
                <div class="flyer-right">
                    <h2 class="flyer-headline">Ordena y paga <span>desde tu celular</span></h2>
                    <p class="flyer-description">
                        Sin esperar al mesero. Escanea el código QR de tu mesa para ver la carta digital, armar tu pedido en tiempo real y enviarlo directo a cocina.
                    </p>

                    <div class="steps-list">
                        <div class="step-item">
                            <div class="step-icon-box">
                                <i class="ri-camera-lens-line"></i>
                            </div>
                            <div class="step-text">
                                <span class="step-title">1. Escanea</span>
                                <span class="step-desc">Apunta la cámara de tu celular al código QR.</span>
                            </div>
                        </div>

                        <div class="step-item">
                            <div class="step-icon-box">
                                <i class="ri-pages-line"></i>
                            </div>
                            <div class="step-text">
                                <span class="step-title">2. Elige</span>
                                <span class="step-desc">Navega por las categorías y selecciona tus platillos.</span>
                            </div>
                        </div>

                        <div class="step-item">
                            <div class="step-icon-box">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                            <div class="step-text">
                                <span class="step-title">3. Confirma</span>
                                <span class="step-desc">Envía tu orden y el mesero la llevará a tu mesa.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flyer-footer">
                <span class="footer-brand">Kuali · Menú Digital QR</span>
                <span class="footer-link">{{ request()->getHost() }}</span>
            </div>

        </div>
    </div>

    {{-- LÓGICA DE CONTROL DEL PERSONALIZADOR --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flyerCard = document.getElementById('flyer-card');
            
            // Selector de temas
            const swatches = document.querySelectorAll('.swatch');
            swatches.forEach(swatch => {
                swatch.addEventListener('click', () => {
                    // Quitar active de los swatches
                    swatches.forEach(s => s.classList.remove('active'));
                    swatch.classList.add('active');

                    // Cambiar clase en la tarjeta
                    flyerCard.className = flyerCard.className.replace(/\btheme-\S+/g, '');
                    
                    const theme = swatch.getAttribute('data-theme');
                    if (theme !== 'classic') {
                        flyerCard.classList.add(`theme-${theme}`);
                    }
                });
            });

            // Selector de orientación
            const toggleBtns = document.querySelectorAll('.btn-toggle');
            toggleBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    toggleBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    const orientation = btn.getAttribute('data-orientation');
                    if (orientation === 'vertical') {
                        flyerCard.classList.remove('flyer--horizontal');
                        flyerCard.classList.add('flyer--vertical');
                    } else {
                        flyerCard.classList.remove('flyer--vertical');
                        flyerCard.classList.add('flyer--horizontal');
                    }
                });
            });
        });
    </script>
</body>
</html>
