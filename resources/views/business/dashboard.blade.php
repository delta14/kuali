{{-- resources/views/business/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="k-main-inner k-premium-container">
    
    {{-- ENCABEZADO PRINCIPAL DE NIVEL COMERCIAL --}}
    <header class="k-premium-header">
        <div class="k-premium-title-wrap">
            <h1>{{ $business->name }}</h1>
            <p class="k-premium-subtitle">
                Panel administrativo de tu menú digital, mesas y pedidos en tiempo real.
            </p>
        </div>
        
        <div class="k-premium-header-actions">
            {{-- Filtro de período decorativo premium --}}
            <div class="k-premium-date-pill">
                <i class="ri-calendar-todo-line" style="color: #4f46e5;"></i>
                <span>Hoy: {{ now()->format('d M, Y') }}</span>
                <i class="ri-arrow-down-s-line" style="color: #9ca3af; font-size: 0.8rem;"></i>
            </div>
            
            <a href="{{ route('orders.pos') }}" class="k-main-cta" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.3rem;">
                <i class="ri-add-circle-line" style="font-size: 1.1rem;"></i>
                <span>Tomar Pedido POS</span>
            </a>
        </div>
    </header>

    {{-- FILA DE METRICAS PREMIUM (KPIs) --}}
    <section class="k-premium-kpis-grid">
        
        {{-- KPI 1: Ventas Totales --}}
        <article class="k-premium-kpi-card">
            <div class="k-premium-kpi-top">
                <span class="k-premium-kpi-label">Ventas Totales</span>
                <div class="k-premium-kpi-icon-wrap emerald">
                    <i class="ri-money-dollar-circle-line"></i>
                </div>
            </div>
            <div>
                <h3 class="k-premium-kpi-value">${{ number_format($totalSales, 2) }}</h3>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem;">
                    <span class="k-premium-kpi-trend up">
                        <i class="ri-arrow-up-line"></i> +14.2%
                    </span>
                    {{-- Mini gráfico barritas simulado --}}
                    <div class="k-premium-sparkline-row">
                        <div class="k-premium-spark-bar" style="height: 12px;"></div>
                        <div class="k-premium-spark-bar" style="height: 18px;"></div>
                        <div class="k-premium-spark-bar" style="height: 14px;"></div>
                        <div class="k-premium-spark-bar" style="height: 22px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 24px;"></div>
                    </div>
                </div>
            </div>
            <p class="k-premium-kpi-helper">Ventas por pedidos completados.</p>
        </article>

        {{-- KPI 2: Pedidos Activos --}}
        <article class="k-premium-kpi-card">
            <div class="k-premium-kpi-top">
                <span class="k-premium-kpi-label">Pedidos Totales</span>
                <div class="k-premium-kpi-icon-wrap purple">
                    <i class="ri-receipt-line"></i>
                </div>
            </div>
            <div>
                <h3 class="k-premium-kpi-value">{{ $ordersCount }}</h3>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem;">
                    <span class="k-premium-kpi-trend up">
                        {{ $pendingOrdersCount }} activos / prep.
                    </span>
                    <div class="k-premium-sparkline-row">
                        <div class="k-premium-spark-bar" style="height: 8px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 20px;"></div>
                        <div class="k-premium-spark-bar" style="height: 12px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 16px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 22px;"></div>
                    </div>
                </div>
            </div>
            <p class="k-premium-kpi-helper">Pedidos totales registrados por QR o POS.</p>
        </article>

        {{-- KPI 3: Catálogo Activo --}}
        <article class="k-premium-kpi-card">
            <div class="k-premium-kpi-top">
                <span class="k-premium-kpi-label">Platillos / Menú</span>
                <div class="k-premium-kpi-icon-wrap orange">
                    <i class="ri-restaurant-line"></i>
                </div>
            </div>
            <div>
                <h3 class="k-premium-kpi-value">{{ $productsCount }}</h3>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem;">
                    <span style="font-size: 0.8rem; font-weight: 500; color: #4b5563;">
                        {{ $categoriesCount }} Categorías
                    </span>
                    <div class="k-premium-sparkline-row">
                        <div class="k-premium-spark-bar active" style="height: 14px;"></div>
                        <div class="k-premium-spark-bar" style="height: 14px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 14px;"></div>
                        <div class="k-premium-spark-bar" style="height: 14px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 14px;"></div>
                    </div>
                </div>
            </div>
            <p class="k-premium-kpi-helper">Productos y servicios publicados.</p>
        </article>

        {{-- KPI 4: Mesas del Local --}}
        <article class="k-premium-kpi-card">
            <div class="k-premium-kpi-top">
                <span class="k-premium-kpi-label">Mesas / QRs</span>
                <div class="k-premium-kpi-icon-wrap pink">
                    <i class="ri-layout-grid-line"></i>
                </div>
            </div>
            <div>
                <h3 class="k-premium-kpi-value">{{ $tablesCount }}</h3>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem;">
                    <span style="font-size: 0.8rem; font-weight: 500; color: #db2777;">
                        {{ $business->is_active ? 'Comercio Activo' : 'Comercio Inactivo' }}
                    </span>
                    <div class="k-premium-sparkline-row">
                        <div class="k-premium-spark-bar active" style="height: 18px;"></div>
                        <div class="k-premium-spark-bar" style="height: 6px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 14px;"></div>
                        <div class="k-premium-spark-bar" style="height: 10px;"></div>
                        <div class="k-premium-spark-bar active" style="height: 20px;"></div>
                    </div>
                </div>
            </div>
            <p class="k-premium-kpi-helper">Códigos QR activos para auto-pedido.</p>
        </article>

    </section>

    {{-- GRID PRINCIPAL DE CONTENIDO A 2 COLUMNAS --}}
    <div class="k-premium-main-grid">
        
        {{-- COLUMNA IZQUIERDA: ACCIONES Y PLATILLOS POPULARES --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            {{-- SECCIÓN: HUB DE ACCIONES RÁPIDAS --}}
            <section class="k-premium-card">
                <div class="k-premium-card-header">
                    <h2 class="k-premium-card-title">
                        <i class="ri-rocket-line"></i>
                        <span>Acciones de Configuración</span>
                    </h2>
                    <span style="font-size: 0.8rem; color: #6b7280; font-weight: 500;">Enlaces rápidos</span>
                </div>
                
                <div class="k-premium-actions-hub">
                    
                    {{-- Acción 1: Categorías --}}
                    <a href="{{ route('categories.index') }}" class="k-premium-action-item">
                        <div class="k-premium-action-avatar">
                            <i class="ri-list-check-3"></i>
                        </div>
                        <div class="k-premium-action-meta">
                            <h3>Categorías</h3>
                            <p>Organiza tu menú por secciones (Entradas, Postres, Bebidas).</p>
                        </div>
                    </a>

                    {{-- Acción 2: Productos --}}
                    <a href="{{ route('products.index') }}" class="k-premium-action-item">
                        <div class="k-premium-action-avatar">
                            <i class="ri-bowl-line"></i>
                        </div>
                        <div class="k-premium-action-meta">
                            <h3>Productos</h3>
                            <p>Sube platillos, fija precios, descripciones y fotos deliciosas.</p>
                        </div>
                    </a>

                    {{-- Acción 3: Mesas y QRs --}}
                    <a href="{{ route('tables.index') }}" class="k-premium-action-item">
                        <div class="k-premium-action-avatar">
                            <i class="ri-qr-code-line"></i>
                        </div>
                        <div class="k-premium-action-meta">
                            <h3>Mesas y QR</h3>
                            <p>Genera y descarga códigos QR y flyers para tus mesas.</p>
                        </div>
                    </a>

                    {{-- Acción 4: Reportes --}}
                    <a href="{{ route('reports.sales') }}" class="k-premium-action-item">
                        <div class="k-premium-action-avatar">
                            <i class="ri-bar-chart-box-line"></i>
                        </div>
                        <div class="k-premium-action-meta">
                            <h3>Métricas de Venta</h3>
                            <p>Analiza ingresos diarios, semanales y volumen de comandas.</p>
                        </div>
                    </a>

                </div>
            </section>

            {{-- SECCIÓN: PLATILLOS MÁS VENDIDOS --}}
            <section class="k-premium-card">
                <div class="k-premium-card-header">
                    <h2 class="k-premium-card-title">
                        <i class="ri-fire-line" style="color: #f97316;"></i>
                        <span>Los 5 Platillos más Pedidos</span>
                    </h2>
                    <a href="{{ route('products.index') }}" class="k-link-sm" style="font-weight: 600;">Ver menú completo</a>
                </div>

                @if($popularDishes->count() > 0)
                    <div style="display: flex; flex-direction: column;">
                        @foreach($popularDishes as $index => $dish)
                            @if($dish->product)
                                <div class="k-premium-dish-row">
                                    {{-- Miniatura foto o inicial --}}
                                    @if($dish->product->image_path)
                                        <img src="{{ asset('storage/' . $dish->product->image_path) }}" class="k-premium-dish-img" alt="{{ $dish->product->name }}">
                                    @else
                                        <div class="k-premium-dish-fallback">
                                            {{ mb_substr($dish->product->name, 0, 1) }}
                                        </div>
                                    @endif
                                    
                                    <div class="k-premium-dish-meta">
                                        <div class="k-premium-dish-name">{{ $dish->product->name }}</div>
                                        <div class="k-premium-dish-cat">{{ $dish->product->category->name ?? 'Sin categoría' }}</div>
                                    </div>
                                    
                                    {{-- Barra de progreso proporcional --}}
                                    <div class="k-premium-dish-progress-bar">
                                        @php
                                            $maxQty = $popularDishes->first()->qty_sum ?: 1;
                                            $percentage = min(100, max(15, ($dish->qty_sum / $maxQty) * 100));
                                        @endphp
                                        <div class="k-premium-dish-progress-inner" style="width: {{ $percentage }}%;"></div>
                                    </div>
                                    
                                    <div class="k-premium-dish-stats">
                                        <div class="k-premium-dish-sales">${{ number_format($dish->total_sales, 2) }}</div>
                                        <div class="k-premium-dish-qty">{{ $dish->qty_sum }} pedidos</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    {{-- Estado vacío elegante --}}
                    <div style="text-align: center; padding: 2.5rem 1rem; color: #6b7280;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #e5e7eb;">
                            <i class="ri-restaurant-line"></i>
                        </div>
                        <h4 style="font-size: 0.95rem; font-weight: 600; color: #374151; margin: 0 0 0.25rem;">Sin comandas registradas aún</h4>
                        <p style="font-size: 0.8rem; max-width: 320px; margin: 0 auto; line-height: 1.45;">
                            Una vez que tus clientes realicen pedidos desde su mesa, verás los platillos estrella aquí.
                        </p>
                    </div>
                @endif
            </section>

        </div>

        {{-- COLUMNA DERECHA: PEDIDOS EN TIEMPO REAL & PROMO QR --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            {{-- SECCIÓN: PEDIDOS EN TIEMPO REAL --}}
            <section class="k-premium-card">
                <div class="k-premium-card-header">
                    <h2 class="k-premium-card-title">
                        <i class="ri-pulse-fill" style="color: #4f46e5; animation: pulse 2s infinite;"></i>
                        <span>Últimas Comandas</span>
                    </h2>
                    <a href="{{ route('orders.index') }}" class="k-link-sm" style="font-weight: 600;">Administrar todos</a>
                </div>

                @if($recentOrders->count() > 0)
                    <div style="display: flex; flex-direction: column;">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('orders.show', $order->id) }}" class="k-premium-order-row">
                                <div class="k-premium-order-left">
                                    <div class="k-premium-order-badge">
                                        #{{ $order->id }}
                                    </div>
                                    <div class="k-premium-order-meta">
                                        <h4>
                                            {{ $order->table ? ($order->table->label ?? $order->table->name) : 'Mostrador' }}
                                        </h4>
                                        <p>
                                            {{ $order->customer_name ?: 'Cliente anónimo' }} • {{ $order->placed_at ? $order->placed_at->diffForHumans() : 'Hace poco' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="k-premium-order-right">
                                    <span class="k-premium-order-total">${{ number_format($order->total, 2) }}</span>
                                    <span class="k-premium-badge {{ $order->status }}">
                                        {{ $order->status === 'in_progress' ? 'Preparando' : ($order->status === 'ready' ? 'Listo' : ($order->status === 'delivered' ? 'Entregado' : ($order->status === 'completed' ? 'Completado' : ($order->status === 'cancelled' ? 'Cancelado' : 'Pendiente')))) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- Estado vacío de comandas --}}
                    <div style="text-align: center; padding: 3rem 1rem; color: #6b7280;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #e5e7eb; animation: float 3s ease-in-out infinite;">
                            <i class="ri-bell-line"></i>
                        </div>
                        <h4 style="font-size: 0.95rem; font-weight: 600; color: #374151; margin: 0 0 0.25rem;">Bandeja limpia</h4>
                        <p style="font-size: 0.8rem; max-width: 260px; margin: 0 auto; line-height: 1.4;">
                            No hay pedidos activos por preparar hoy. ¡Buen trabajo!
                        </p>
                    </div>
                @endif
            </section>

            {{-- SECCIÓN: TARJETA PROMO CANAL QR --}}
            <article class="k-premium-promo-card">
                <div class="k-premium-promo-card-content">
                    <span class="k-premium-promo-tag">Canal QR Activo</span>
                    <h3>Atrae auto-pedidos directamente a la cocina</h3>
                    <p>
                        Imprime los códigos QR exclusivos de tus mesas para que tus comensales escaneen, vean tu menú e ingresen pedidos al instante sin esperas.
                    </p>
                    <a href="{{ route('tables.index') }}" class="k-premium-btn-white">
                        <i class="ri-printer-line"></i>
                        <span>Imprimir Códigos QR</span>
                    </a>
                </div>
            </article>

        </div>

    </div>

</div>

{{-- ESTILO DE ANIMACION DE PULSACION Y FLOTACIÓN DEL ICONO EN TIEMPO REAL --}}
<style>
    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.85; }
        50% { transform: scale(1.15); opacity: 1; filter: drop-shadow(0 0 4px rgba(79, 70, 229, 0.6)); }
        100% { transform: scale(1); opacity: 0.85; }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-6px); }
        100% { transform: translateY(0px); }
    }
</style>
@endsection
