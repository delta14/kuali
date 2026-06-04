{{-- resources/views/business/orders/pos.blade.php --}}
@extends('layouts.app')

@section('title', 'Tomar pedido en mostrador')

@section('content')
    {{-- CSS Scoped de Alta Fidelidad para POS (Toast/Shopify-style) --}}
    <style>
        :root {
            --pos-primary: #4f46e5;
            --pos-primary-glow: rgba(79, 70, 229, 0.15);
            --pos-accent: #f97316;
            --pos-success: #10b981;
            --pos-bg-card: #ffffff;
            --pos-bg-dark: #090d16;
            --pos-text-main: #1f1a3a;
            --pos-text-muted: #6b7280;
            --pos-border: #e5e7eb;
        }

        .k-pos-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            font-family: 'Poppins', sans-serif;
            color: var(--pos-text-main);
            padding: 0.5rem;
        }

        /* Header Premium */
        .k-pos-header-premium {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(79, 70, 229, 0.08);
            padding-bottom: 1.25rem;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .k-pos-title-wrap h1 {
            font-size: 1.85rem;
            font-weight: 800;
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 50%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            letter-spacing: -0.03em;
        }

        .k-pos-subtitle {
            font-size: 0.9rem;
            color: var(--pos-text-muted);
            margin-top: 0.25rem;
        }

        /* Layout Grid */
        .k-pos-layout-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(340px, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        /* Panel del Catálogo (Izquierda) */
        .k-pos-catalog-panel {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* Buscador Moderno */
        .k-pos-search-wrapper {
            position: relative;
            width: 100%;
        }

        .k-pos-search-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .k-pos-search-input {
            width: 100%;
            height: 48px;
            padding: 0 1.25rem 0 3rem;
            border-radius: 16px;
            border: 1px solid var(--pos-border);
            background: #ffffff;
            font-size: 0.92rem;
            font-family: inherit;
            color: var(--pos-text-main);
            outline: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .k-pos-search-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 4px var(--pos-primary-glow), 0 8px 20px rgba(79, 70, 229, 0.05);
        }

        .k-pos-search-input:focus + i {
            color: var(--pos-primary);
        }

        /* Carrusel Horizontal de Categorías */
        .k-pos-categories-carousel {
            display: flex;
            gap: 0.6rem;
            overflow-x: auto;
            padding: 0.25rem 0.25rem 0.65rem 0.25rem;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        .k-pos-categories-carousel::-webkit-scrollbar {
            height: 5px;
        }

        .k-pos-categories-carousel::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 99px;
        }

        .k-pos-categories-carousel::-webkit-scrollbar-thumb {
            background: rgba(79, 70, 229, 0.15);
            border-radius: 99px;
        }

        .k-pos-categories-carousel::-webkit-scrollbar-thumb:hover {
            background: var(--pos-primary);
        }

        .k-pos-cat-tab {
            flex: 0 0 auto;
            border: 1px solid var(--pos-border);
            background: #ffffff;
            color: #4b5563;
            padding: 0.55rem 1.15rem;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .k-pos-cat-tab:hover {
            border-color: var(--pos-primary);
            color: var(--pos-primary);
            background: rgba(79, 70, 229, 0.02);
            transform: translateY(-1px);
        }

        .k-pos-cat-tab.is-active {
            background: linear-gradient(135deg, var(--pos-primary) 0%, #6366f1 100%);
            border-color: transparent;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.25);
        }

        .k-pos-cat-tab i {
            font-size: 1rem;
        }

        /* Grid de Productos */
        .k-pos-products-layout {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.15rem;
        }

        .k-pos-dish-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(229, 231, 235, 0.8);
            padding: 0.9rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.015);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 290px;
        }

        .k-pos-dish-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.08);
            border-color: rgba(79, 70, 229, 0.2);
        }

        /* Foto de Platillo */
        .k-pos-dish-photo {
            width: 100%;
            height: 125px;
            border-radius: 14px;
            overflow: hidden;
            background: #f3f4f6;
            margin-bottom: 0.75rem;
            position: relative;
        }

        .k-pos-dish-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .k-pos-dish-card:hover .k-pos-dish-photo img {
            transform: scale(1.08);
        }

        .k-pos-dish-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(99, 102, 241, 0.08) 100%);
            color: var(--pos-primary);
        }

        .k-pos-dish-placeholder i {
            font-size: 2.2rem;
            opacity: 0.85;
            transition: transform 0.3s ease;
        }

        .k-pos-dish-card:hover .k-pos-dish-placeholder i {
            transform: scale(1.15) rotate(5deg);
        }

        /* Detalle Platillo */
        .k-pos-dish-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-bottom: 0.75rem;
        }

        .k-pos-dish-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.3;
        }

        .k-pos-dish-description {
            font-size: 0.78rem;
            color: var(--pos-text-muted);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .k-pos-dish-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 0.5rem;
            border-top: 1px dashed rgba(229, 231, 235, 0.5);
        }

        .k-pos-dish-price {
            font-size: 1.05rem;
            font-weight: 750;
            color: var(--pos-primary);
            letter-spacing: -0.01em;
        }

        .k-pos-add-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.08);
            color: var(--pos-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .k-pos-dish-card:hover .k-pos-add-circle {
            background: var(--pos-primary);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
            transform: scale(1.08);
        }

        /* Estado Vacío */
        .k-pos-no-items {
            text-align: center;
            padding: 3rem 1.5rem;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--pos-border);
            color: var(--pos-text-muted);
            grid-column: 1 / -1;
        }

        .k-pos-no-items i {
            font-size: 3rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 1rem;
        }

        /* TICKET DIGITAL EN VIVO (Derecha) */
        .k-pos-ticket-sidebar {
            background: var(--pos-bg-dark);
            border-radius: 24px;
            color: #ffffff;
            padding: 1.5rem;
            box-shadow: 0 20px 45px rgba(9, 13, 22, 0.25);
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 140px);
            position: sticky;
            top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Estilo Cabecera Ticket */
        .k-pos-ticket-head {
            border-bottom: 1.5px dashed rgba(255, 255, 255, 0.15);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            position: relative;
        }

        /* Troquelado superior simulado en el CSS */
        .k-pos-ticket-head::before {
            content: '';
            position: absolute;
            top: -24px;
            left: 0;
            right: 0;
            height: 6px;
            background-image: radial-gradient(circle, transparent 4px, var(--pos-bg-dark) 4px);
            background-size: 12px 12px;
            background-repeat: repeat-x;
        }

        .k-pos-ticket-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .k-pos-ticket-title-row h2 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
            letter-spacing: -0.01em;
        }

        .k-pos-ticket-badge {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #e5e7eb;
        }

        .k-pos-ticket-badge span {
            color: #a855f7;
            margin-right: 0.15rem;
        }

        /* Lista de Productos del Carrito */
        .k-pos-ticket-items-list {
            flex: 1;
            overflow-y: auto;
            max-height: calc(100vh - 510px);
            min-height: 180px;
            padding-right: 0.25rem;
            margin-bottom: 1rem;
        }

        /* Custom Scrollbar Ticket */
        .k-pos-ticket-items-list::-webkit-scrollbar {
            width: 4px;
        }

        .k-pos-ticket-items-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 99px;
        }

        .k-pos-cart-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            gap: 0.75rem;
            animation: ticketFadeIn 0.25s ease;
        }

        @keyframes ticketFadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .k-pos-cart-row-details {
            flex-grow: 1;
            min-width: 0;
        }

        .k-pos-cart-row-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .k-pos-cart-row-price {
            font-size: 0.78rem;
            color: #9ca3af;
            margin-top: 0.1rem;
        }

        /* Controles Qty */
        .k-pos-ticket-qty-box {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 99px;
            padding: 0.15rem;
            gap: 0.15rem;
        }

        .k-pos-ticket-qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: background 0.15s ease;
        }

        .k-pos-ticket-qty-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .k-pos-ticket-qty-val {
            font-size: 0.8rem;
            font-weight: 700;
            min-width: 18px;
            text-align: center;
        }

        .k-pos-cart-row-total {
            font-size: 0.88rem;
            font-weight: 700;
            color: #ffffff;
            min-width: 65px;
            text-align: right;
        }

        /* Vacío Ticket */
        .k-pos-ticket-empty-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6b7280;
            text-align: center;
            padding: 2rem 1rem;
        }

        .k-pos-ticket-empty-wrap i {
            font-size: 2.2rem;
            color: #374151;
            margin-bottom: 0.75rem;
        }

        .k-pos-ticket-empty-wrap p {
            font-size: 0.82rem;
        }

        /* Sección de Sumario / Notas / Formulario en el Ticket */
        .k-pos-ticket-summary-box {
            border-top: 1.5px dashed rgba(255, 255, 255, 0.15);
            padding-top: 1rem;
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .k-pos-ticket-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .k-pos-ticket-summary-row span {
            font-size: 0.95rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .k-pos-ticket-summary-row strong {
            font-size: 1.45rem;
            color: #ffffff;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        /* Textarea de Nota de Preparación */
        .k-pos-ticket-note-container {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .k-pos-ticket-note-container label {
            font-size: 0.75rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .k-pos-ticket-textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            outline: none;
            resize: none;
            transition: all 0.2s ease;
        }

        .k-pos-ticket-textarea:focus {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
        }

        .k-pos-ticket-textarea::placeholder {
            color: #4b5563;
        }

        /* Formulario del pedido */
        .k-pos-ticket-fields-grid {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 0.6rem;
        }

        .k-pos-ticket-field {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .k-pos-ticket-field label {
            font-size: 0.75rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .k-pos-ticket-input {
            height: 38px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.82rem;
            padding: 0 0.75rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .k-pos-ticket-input:focus {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
        }

        .k-pos-ticket-select {
            height: 38px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.82rem;
            padding: 0 0.5rem;
            outline: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .k-pos-ticket-select option {
            background: var(--pos-bg-dark);
            color: #ffffff;
        }

        .k-pos-ticket-select:focus {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
        }

        /* Botón de Enviar Pedido */
        .k-pos-ticket-submit-btn {
            width: 100%;
            height: 48px;
            border-radius: 99px;
            border: none;
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            color: #ffffff;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .k-pos-ticket-submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(168, 85, 247, 0.45);
            background: linear-gradient(135deg, #b865ff 0%, #7376ff 100%);
        }

        .k-pos-ticket-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Alertas de Mensaje */
        .k-pos-feedback-msg {
            padding: 0.65rem 0.85rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            animation: ticketFadeIn 0.2s ease;
            display: none;
        }

        .k-pos-feedback-msg.success {
            display: block;
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .k-pos-feedback-msg.error {
            display: block;
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .k-pos-layout-grid {
                grid-template-columns: 1fr;
            }
            .k-pos-ticket-sidebar {
                position: static;
                min-height: auto;
            }
        }
    </style>

    <div class="k-pos-container">
        {{-- Header Premium --}}
        <header class="k-pos-header-premium">
            <div class="k-pos-title-wrap">
                <h1>Caja Registradora POS</h1>
                <p class="k-pos-subtitle">
                    Toma y crea pedidos rápidos para mostrador o salón desde esta consola unificada.
                </p>
            </div>
            <div>
                <a href="{{ route('orders.index') }}" class="k-premium-btn-white" style="text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1.2rem; border-radius:99px; font-weight:600; font-size:0.82rem;">
                    <i class="ri-arrow-left-line"></i>
                    <span>Volver a Pedidos</span>
                </a>
            </div>
        </header>

        <div class="k-pos-layout-grid">
            {{-- COLUMNA IZQUIERDA: CATÁLOGO --}}
            <section class="k-pos-catalog-panel">
                {{-- Buscador Premium --}}
                <div class="k-pos-search-wrapper">
                    <input type="text" id="kPosSearch" class="k-pos-search-input" placeholder="Buscar por platillo, ingrediente o código...">
                    <i class="ri-search-2-line"></i>
                </div>

                {{-- Carrusel de Categorías --}}
                <div class="k-pos-categories-carousel">
                    @foreach ($categories as $cat)
                        @php
                            // Iconos dinámicos basados en el nombre
                            $catLower = strtolower($cat->name);
                            $catIcon = 'ri-restaurant-2-line'; // fallback
                            if (str_contains($catLower, 'bebida') || str_contains($catLower, 'toma') || str_contains($catLower, 'refresco') || str_contains($catLower, 'jugo')) {
                                $catIcon = 'ri-cup-line';
                            } elseif (str_contains($catLower, 'postre') || str_contains($catLower, 'dulce') || str_contains($catLower, 'pastel') || str_contains($catLower, 'helado')) {
                                $catIcon = 'ri-cake-3-line';
                            } elseif (str_contains($catLower, 'entrada') || str_contains($catLower, 'botana') || str_contains($catLower, 'snack')) {
                                $catIcon = 'ri-goblet-line';
                            } elseif (str_contains($catLower, 'comida') || str_contains($catLower, 'plato') || str_contains($catLower, 'fuerte') || str_contains($catLower, 'especial')) {
                                $catIcon = 'ri-restaurant-fill';
                            } elseif (str_contains($catLower, 'desayuno') || str_contains($catLower, 'huevo') || str_contains($catLower, 'pan')) {
                                $catIcon = 'ri-bread-line';
                            } elseif (str_contains($catLower, 'ensalada') || str_contains($catLower, 'bowl') || str_contains($catLower, 'verde')) {
                                $catIcon = 'ri-leaf-line';
                            }
                        @endphp
                        <button type="button" class="k-pos-cat-tab k-pos-cat-pill" data-cat-id="{{ $cat->id }}">
                            <i class="{{ $catIcon }}"></i>
                            <span>{{ $cat->name }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Grid de Platillos --}}
                <div id="kPosProducts" class="k-pos-products-layout">
                    @foreach ($categories as $cat)
                        @foreach ($cat->products as $product)
                            <article class="k-pos-dish-card k-pos-product-card" data-cat-id="{{ $cat->id }}"
                                data-product-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}">

                                {{-- Imagen del producto --}}
                                <div class="k-pos-dish-photo k-pos-product-thumb">
                                    @if ($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="k-pos-dish-placeholder k-pos-product-thumb-placeholder">
                                            @php
                                                // Icono placeholder dinámico basado en la categoría
                                                $catName = strtolower($cat->name);
                                                $phIcon = 'ri-restaurant-line';
                                                if (str_contains($catName, 'bebida')) {
                                                    $phIcon = 'ri-cup-line';
                                                } elseif (str_contains($catName, 'postre')) {
                                                    $phIcon = 'ri-cake-3-line';
                                                }
                                            @endphp
                                            <i class="{{ $phIcon }}"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Datos Platillo --}}
                                <div class="k-pos-dish-info k-pos-product-main">
                                    <div class="k-pos-dish-title k-pos-product-name">{{ $product->name }}</div>
                                    @if ($product->description)
                                        <div class="k-pos-dish-description k-pos-product-desc">
                                            {{ \Illuminate\Support\Str::limit($product->description, 50) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="k-pos-dish-footer k-pos-product-footer">
                                    <span class="k-pos-dish-price k-pos-product-price">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    <div class="k-pos-add-circle k-pos-product-add-btn">
                                        <i class="ri-add-line"></i>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @endforeach

                    @if ($categories->sum(fn($c) => $c->products->count()) === 0)
                        <div class="k-pos-no-items k-pos-empty">
                            <i class="ri-restaurant-fill"></i>
                            <p>No tienes productos activos en tu menú de comercio.</p>
                            <a href="{{ route('products.index') }}" class="k-btn-primary" style="margin-top: 1rem; text-decoration: none; display: inline-flex;">
                                Crear mi Primer Platillo
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            {{-- COLUMNA DERECHA: TICKET DIGITAL EN VIVO --}}
            <aside class="k-pos-ticket-sidebar k-pos-cart">
                <div class="k-pos-ticket-head">
                    <div class="k-pos-ticket-title-row">
                        <h2>Detalle del Pedido</h2>
                        <div class="k-pos-ticket-badge">
                            <span id="kPosItemsCount">0</span> Artículos
                        </div>
                    </div>
                    <div style="font-size:0.75rem; color:#9ca3af; margin-top:0.25rem; display:flex; justify-content:space-between;">
                        <span>Kuali POS terminal</span>
                        <span>{{ now()->format('H:i') }}</span>
                    </div>
                </div>

                {{-- Contenedor de Items del Ticket --}}
                <div id="kPosCartList" class="k-pos-ticket-items-list k-pos-cart-list k-pos-cart-list--empty">
                    <div class="k-pos-ticket-empty-wrap">
                        <i class="ri-receipt-line"></i>
                        <p class="k-pos-cart-empty-text">El pedido está vacío.</p>
                        <p style="font-size:0.75rem; color:#4b5563; margin-top:0.2rem;">Haz clic en cualquier platillo del menú para agregarlo.</p>
                    </div>
                </div>

                {{-- Sumario de Totales y Campos --}}
                <div class="k-pos-ticket-summary-box k-pos-cart-summary">
                    <div class="k-pos-ticket-summary-row">
                        <span>Total a Pagar</span>
                        <strong id="kPosTotal">$0.00</strong>
                    </div>

                    {{-- Notas del Pedido --}}
                    <div class="k-pos-ticket-note-container k-pos-cart-note">
                        <label for="posOrderNote" class="k-pos-cart-note-label">Notas de Cocina</label>
                        <textarea id="posOrderNote" class="k-pos-ticket-textarea k-pos-cart-note-textarea" rows="2" placeholder="Ej: Término medio, sin cebolla, salsas aparte..."></textarea>
                    </div>

                    {{-- Datos del Pedido --}}
                    <div class="k-pos-ticket-fields-grid k-pos-cart-form">
                        <div class="k-pos-ticket-field k-pos-field">
                            <label for="kPosTable" class="k-pos-label">Mesa / Ref</label>
                            <input type="text" id="kPosTable" class="k-pos-ticket-input k-pos-input" placeholder="Mostrador" value="Mostrador">
                        </div>

                        <div class="k-pos-ticket-field k-pos-field">
                            <label for="kPosType" class="k-pos-label">Servicio</label>
                            <select id="kPosType" class="k-pos-ticket-select k-pos-input" name="order_type">
                                <option value="">Tipo de servicio</option>
                                <option value="dine_in">Para comer aquí</option>
                                <option value="take_away">Para llevar</option>
                            </select>
                        </div>
                    </div>

                    {{-- Botón Enviar --}}
                    <button type="button" id="kPosCreateOrder" class="k-pos-ticket-submit-btn k-pos-submit-btn">
                        <i class="ri-shield-check-line"></i>
                        <span>Generar Comanda</span>
                    </button>

                    {{-- Notificación / Mensaje --}}
                    <div id="kPosMessage" class="k-pos-feedback-msg k-pos-message" style="display:none;"></div>
                </div>
            </aside>
        </div>
    </div>

    {{-- Config para JS --}}
    <div id="kPosConfig" data-store-url="{{ route('orders.pos.store') }}" data-csrf="{{ csrf_token() }}"></div>
@endsection

@push('scripts')
    <script>
        (function() {
            const cfg = document.getElementById('kPosConfig');
            if (!cfg) return;

            const storeUrl = cfg.dataset.storeUrl;
            const csrfToken = cfg.dataset.csrf;

            const searchInput = document.getElementById('kPosSearch');
            const catPills = Array.from(document.querySelectorAll('.k-pos-cat-pill'));
            const productCards = Array.from(document.querySelectorAll('.k-pos-product-card'));

            const cartList = document.getElementById('kPosCartList');
            const itemsCountEl = document.getElementById('kPosItemsCount');
            const totalEl = document.getElementById('kPosTotal');
            const tableInput = document.getElementById('kPosTable');
            const typeSelect = document.getElementById('kPosType');
            const submitBtn = document.getElementById('kPosCreateOrder');
            const msgEl = document.getElementById('kPosMessage');
            const orderNoteInput = document.getElementById('posOrderNote');

            let activeCatId = null;
            let cart = []; // {product_id, name, price, qty}

            // Activar la primera categoría por defecto
            if (catPills.length) {
                const first = catPills[0];
                activeCatId = first.dataset.catId;
                first.classList.add('is-active');

                productCards.forEach(card => {
                    const cardCat = card.dataset.catId;
                    card.style.display = (cardCat === activeCatId) ? '' : 'none';
                });
            }

            function formatMoney(n) {
                return '$' + Number(n || 0).toFixed(2);
            }

            function renderCart() {
                cartList.innerHTML = '';
                if (!cart.length) {
                    cartList.classList.add('k-pos-cart-list--empty');
                    cartList.innerHTML = `
                        <div class="k-pos-ticket-empty-wrap">
                            <i class="ri-receipt-line"></i>
                            <p class="k-pos-cart-empty-text">El pedido está vacío.</p>
                            <p style="font-size:0.75rem; color:#4b5563; margin-top:0.2rem;">Haz clic en cualquier platillo del menú para agregarlo.</p>
                        </div>
                    `;
                } else {
                    cartList.classList.remove('k-pos-cart-list--empty');

                    cart.forEach(item => {
                        const row = document.createElement('div');
                        row.className = 'k-pos-cart-row k-pos-cart-item';
                        row.innerHTML = `
                            <div class="k-pos-cart-row-details k-pos-cart-item-main">
                                <div class="k-pos-cart-row-name k-pos-cart-item-name">${item.name}</div>
                                <div class="k-pos-cart-row-price k-pos-cart-item-price">${formatMoney(item.price)}</div>
                            </div>
                            <div class="k-pos-ticket-qty-box k-pos-cart-item-qty">
                                <button type="button" class="k-pos-ticket-qty-btn k-pos-qty-btn" data-action="minus">−</button>
                                <span class="k-pos-ticket-qty-val k-pos-qty-value">${item.qty}</span>
                                <button type="button" class="k-pos-ticket-qty-btn k-pos-qty-btn" data-action="plus">+</button>
                            </div>
                            <div class="k-pos-cart-row-total k-pos-cart-item-total">
                                ${formatMoney(item.price * item.qty)}
                            </div>
                        `;

                        row.querySelectorAll('.k-pos-qty-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const action = btn.dataset.action;
                                if (action === 'plus') {
                                    item.qty++;
                                } else if (action === 'minus') {
                                    item.qty--;
                                    if (item.qty <= 0) {
                                        cart = cart.filter(x => x.product_id !== item.product_id);
                                    }
                                }
                                renderCart();
                            });
                        });

                        cartList.appendChild(row);
                    });
                }

                const total = cart.reduce((sum, i) => sum + i.price * i.qty, 0);
                const itemsCount = cart.reduce((sum, i) => sum + i.qty, 0);

                itemsCountEl.textContent = itemsCount;
                totalEl.textContent = formatMoney(total);
            }

            function addToCart(card) {
                const id = Number(card.dataset.productId);
                const name = card.dataset.name;
                const price = Number(card.dataset.price);

                let existing = cart.find(i => i.product_id === id);
                if (!existing) {
                    existing = {
                        product_id: id,
                        name,
                        price,
                        qty: 0
                    };
                    cart.push(existing);
                }
                existing.qty++;
                renderCart();
            }

            // Click en productos
            productCards.forEach(card => {
                const btn = card.querySelector('.k-pos-product-add-btn');
                (btn || card).addEventListener('click', () => addToCart(card));
            });

            // Filtro por categoría
            catPills.forEach(pill => {
                pill.addEventListener('click', () => {
                    const id = pill.dataset.catId;

                    // Si vuelves a hacer clic en la misma, quitamos filtro
                    if (activeCatId === id) {
                        activeCatId = null;
                        catPills.forEach(p => p.classList.remove('is-active'));
                        productCards.forEach(card => {
                            card.style.display = '';
                        });
                        return;
                    }

                    activeCatId = id;
                    catPills.forEach(p => p.classList.toggle('is-active', p.dataset.catId === id));

                    productCards.forEach(card => {
                        const cardCat = card.dataset.catId;
                        card.style.display = (cardCat === id) ? '' : 'none';
                    });
                });
            });

            // Búsqueda
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    const q = searchInput.value.trim().toLowerCase();
                    productCards.forEach(card => {
                        const name = (card.dataset.name || '').toLowerCase();
                        card.style.display = name.includes(q) ? '' : 'none';
                    });
                });
            }

            // Enviar pedido
            submitBtn.addEventListener('click', async () => {
                msgEl.style.display = 'none';
                msgEl.className = 'k-pos-feedback-msg k-pos-message';
                msgEl.textContent = '';

                if (!cart.length) {
                    msgEl.textContent = 'Agrega al menos un producto al pedido.';
                    msgEl.classList.add('error');
                    return;
                }

                const payload = {
                    table_name: tableInput.value || 'Mostrador',
                    order_type: typeSelect.value || null,
                    order_note: orderNoteInput ? orderNoteInput.value.trim() : null,
                    items: cart.map(i => ({
                        product_id: i.product_id,
                        qty: i.qty,
                        price: i.price,
                        note: i.note || null,
                    })),
                };

                try {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> <span>Guardando Pedido...</span>';

                    const resp = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await resp.json();

                    if (!resp.ok || !data.ok) {
                        throw new Error(data.message || 'Error al crear el pedido');
                    }

                    msgEl.textContent = data.message || 'Pedido creado correctamente.';
                    msgEl.classList.add('success');

                    // Limpiar carrito
                    cart = [];
                    renderCart();

                    // Si quieres redirigir a la lista:
                    if (data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1200);
                    }
                } catch (e) {
                    console.error(e);
                    msgEl.textContent = e.message || 'No se pudo crear el pedido.';
                    msgEl.classList.add('error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="ri-shield-check-line"></i> <span>Generar Comanda</span>';
                }
            });
        })();
    </script>
@endpush
