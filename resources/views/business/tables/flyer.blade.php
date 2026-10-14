<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QR {{ $table->name }} – {{ $business->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fuentes locales para diseño editorial premium --}}
    <link rel="stylesheet" href="{{ asset('vendor/fonts/outfit.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fonts/playfair-display.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fonts/plus-jakarta-sans.css') }}">

    {{-- Remix Icons para íconos elegantes (local) --}}
    <link rel="stylesheet" href="{{ asset('vendor/remixicon/remixicon.css') }}">

    <style>
        :root {
            --flyer-bg-image: url('{{ asset('images/flyer/delifondo01.jpg') }}');
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/pages/flyer.css') }}">
</head>
<body>

    {{-- BARRA DE PERSONALIZACIÓN (NO SE IMPRIME) --}}
    <div class="customizer">
        <div class="customizer-section">
            <span class="customizer-label">Orientación</span>
            <div class="btn-toggle-group">
                <button class="btn-toggle" data-orientation="horizontal">
                    <i class="ri-layout-row-line"></i> Horizontal
                </button>
                <button class="btn-toggle active" data-orientation="vertical">
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
        <div class="flyer flyer--vertical" id="flyer-card">
            
            {{-- ENCABEZADO --}}
            <div class="flyer-header">
                <div class="brand-section">
                    <div class="brand-logo-ring">
                        @if ($business->logo_path)
                            <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Logo {{ $business->name }}">
                        @else
                            <i class="ri-restaurant-line"></i>
                        @endif
                    </div>
                </div>
                <div class="table-badge">
                    <i class="ri-map-pin-line"></i> Mesa <span>{{ $table->name }}</span>
                </div>
            </div>

            {{-- CUERPO --}}
            <div class="flyer-content">
                
                {{-- LADO IZQUIERDO: QR --}}
                <div class="flyer-left">
                    <div class="qr-panel-logo">
                        @if ($business->logo_path)
                            <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Logo {{ $business->name }}">
                        @else
                            <i class="ri-restaurant-line"></i>
                        @endif
                    </div>

                    <div class="qr-feature">
                        <svg class="curve-text-svg" viewBox="0 0 270 56">
                            <path id="curveTextPath" d="M 15 54 A 120 120 0 0 1 255 54" fill="transparent"/>
                            <text>
                                <textPath href="#curveTextPath" startOffset="50%" text-anchor="middle">
                                    Escanea para ver el menú
                                </textPath>
                            </text>
                        </svg>

                        <div class="qr-feature-circle">
                            <div class="qr-feature-inner">
                                @if ($table->qr_path)
                                    <img src="{{ asset('storage/' . $table->qr_path) }}" alt="QR Mesa {{ $table->name }}">
                                @else
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #6b7280; font-size: 0.75rem; text-align:center;">
                                        <i class="ri-qr-code-line" style="font-size: 2.2rem; margin-bottom: 6px;"></i>
                                        QR no generado
                                    </div>
                                @endif
                            </div>

                            <div class="qr-feature-logo-badge">
                                @if ($business->logo_path)
                                    <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Logo {{ $business->name }}">
                                @else
                                    <i class="ri-restaurant-line"></i>
                                @endif
                            </div>
                        </div>

                        <div class="qr-caption">
                            <i class="ri-scan-2-line"></i> Escanear aquí
                        </div>
                    </div>

                    <div class="qr-panel-footer">Kuali · Desarrollado por Koali Soft</div>
                </div>

                {{-- LÍNEA DE DOBLEZ (solo horizontal) --}}
                

                {{-- LADO DERECHO: TEXTO E INSTRUCCIONES --}}
                <div class="flyer-right">
                    {{-- Encabezado duplicado, solo visible en orientación horizontal --}}
                    <div class="panel-header">
                        <div class="brand-section">
                            <div class="brand-logo-ring">
                                @if ($business->logo_path)
                                    <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Logo {{ $business->name }}">
                                @else
                                    <i class="ri-restaurant-line"></i>
                                @endif
                            </div>
                        </div>
                        <div class="table-badge">
                            <i class="ri-map-pin-line"></i><span>{{ $table->name }}</span>
                        </div>
                    </div>

                    <h2 class="flyer-description">Ordena y paga <span>desde tu celular</span></h2>
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
                <span class="footer-link">Kuali · Desarrollado por Koali Soft</span>
            </div>

        </div>
    </div>

    {{-- LÓGICA DE CONTROL DEL PERSONALIZADOR --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flyerCard = document.getElementById('flyer-card');

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
