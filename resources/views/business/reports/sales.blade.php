{{-- resources/views/business/reports/sales.blade.php --}}
@extends('layouts.app')

@section('title', 'Reportes de ventas')

@section('content')
    {{-- CSS Scoped de Alta Fidelidad para Reportes (Stripe-style) --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/business-reports-sales.css') }}">
    @endpush

    <div class="k-rep-container">
        {{-- Toolbar Premium --}}
        <header class="k-rep-header-premium">
            <div class="k-rep-title-wrap">
                <h1>Reportes y Rendimiento</h1>
                <p class="k-rep-subtitle">
                    Monitorea las métricas críticas y los platillos de alto impacto de {{ $business->name ?? 'tu negocio' }}.
                </p>
            </div>

            {{-- Formulario de Filtros --}}
            <form method="GET" action="{{ route('reports.sales') }}" class="k-rep-filters-form">
                <div class="k-rep-date-range-box">
                    <input type="date" name="from" class="k-rep-date-input" value="{{ $from }}">
                    <span class="k-rep-date-separator">a</span>
                    <input type="date" name="to" class="k-rep-date-input" value="{{ $to }}">
                </div>
                <button type="submit" class="k-rep-submit-btn">
                    <i class="ri-filter-2-line"></i>
                    <span>Filtrar Rango</span>
                </button>
            </form>
        </header>

        {{-- KPIs Premium --}}
        <section class="k-rep-kpis-grid">
            {{-- KPI 1: Ventas Totales --}}
            <article class="k-rep-kpi-card">
                <div class="k-rep-kpi-top">
                    <span class="k-rep-kpi-label">Ventas Totales</span>
                    <div class="k-rep-kpi-icon-wrap emerald">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                </div>
                <div>
                    <h3 class="k-rep-kpi-value">${{ number_format($totalSales, 2) }}</h3>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span class="k-rep-kpi-trend">
                            <i class="ri-arrow-up-line"></i> Ventas
                        </span>
                        {{-- Simulación sparkline --}}
                        <div class="k-rep-kpi-sparkline">
                            <div class="k-rep-spark-bar" style="height: 10px;"></div>
                            <div class="k-rep-spark-bar active" style="height: 15px;"></div>
                            <div class="k-rep-spark-bar" style="height: 12px;"></div>
                            <div class="k-rep-spark-bar active" style="height: 18px;"></div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- KPI 2: Pedidos Completados --}}
            <article class="k-rep-kpi-card">
                <div class="k-rep-kpi-top">
                    <span class="k-rep-kpi-label">Pedidos Completados</span>
                    <div class="k-rep-kpi-icon-wrap purple">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                </div>
                <div>
                    <h3 class="k-rep-kpi-value">{{ $totalOrders }}</h3>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span class="k-rep-kpi-trend" style="background: rgba(147,51,234,0.06); color:#7c3aed;">
                            Completados
                        </span>
                        <div class="k-rep-kpi-sparkline">
                            <div class="k-rep-spark-bar" style="height: 14px;"></div>
                            <div class="k-rep-spark-bar" style="height: 10px;"></div>
                            <div class="k-rep-spark-bar active" style="height: 18px;"></div>
                            <div class="k-rep-spark-bar active" style="height: 20px;"></div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- KPI 3: Ticket Promedio --}}
            <article class="k-rep-kpi-card">
                <div class="k-rep-kpi-top">
                    <span class="k-rep-kpi-label">Ticket Promedio</span>
                    <div class="k-rep-kpi-icon-wrap orange">
                        <i class="ri-ticket-2-line"></i>
                    </div>
                </div>
                <div>
                    <h3 class="k-rep-kpi-value">${{ number_format($avgTicket, 2) }}</h3>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span class="k-rep-kpi-trend" style="background: rgba(249,115,22,0.06); color:#ea580c;">
                            Por Comanda
                        </span>
                        <div class="k-rep-kpi-sparkline">
                            <div class="k-rep-spark-bar active" style="height: 16px;"></div>
                            <div class="k-rep-spark-bar" style="height: 12px;"></div>
                            <div class="k-rep-spark-bar active" style="height: 14px;"></div>
                            <div class="k-rep-spark-bar" style="height: 19px;"></div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        {{-- Grid de Tablas / Datos --}}
        <div class="k-rep-main-grid">
            {{-- Top Productos --}}
            <div class="k-rep-data-card">
                <div class="k-rep-card-header">
                    <h2>Top 5 Platillos Más Vendidos</h2>
                    <span class="k-rep-card-badge">Más Demandados</span>
                </div>

                @if ($topProducts->isEmpty())
                    <div class="k-rep-empty-state">
                        <i class="ri-archive-line"></i>
                        <h3>Sin datos registrados</h3>
                        <p>No se encontraron comandas o productos vendidos en este rango de fechas.</p>
                    </div>
                @else
                    <div class="k-rep-table-wrapper">
                        <table class="k-rep-table">
                            <thead>
                                <tr>
                                    <th>Platillo / Producto</th>
                                    <th style="text-align:right;">Cantidad Vendida</th>
                                    <th style="text-align:right;">Total Recaudado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topProducts as $prod)
                                    @php
                                        // Generar inicial del producto para avatar
                                        $initial = strtoupper(substr($prod->product_name ?? 'P', 0, 1));
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="k-rep-prod-cell">
                                                <div class="k-rep-prod-avatar">
                                                    {{ $initial }}
                                                </div>
                                                <span class="k-rep-prod-name">{{ $prod->product_name ?? 'Producto' }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align:right; font-weight: 600;">
                                            {{ $prod->qty }} uds
                                        </td>
                                        <td style="text-align:right; font-weight: 750; color: var(--rep-primary);">
                                            ${{ number_format($prod->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Breakdown por origen / tipo --}}
            <div class="k-rep-data-card">
                <div class="k-rep-card-header">
                    <h2>Resumen de Venta</h2>
                </div>

                {{-- Origen del Pedido --}}
                <div class="k-rep-section-title">
                    <span>Origen de Ordenes</span>
                </div>
                <div class="k-rep-table-wrapper" style="margin-bottom: 1.5rem;">
                    <table class="k-rep-table">
                        <thead>
                            <tr>
                                <th>Origen</th>
                                <th style="text-align:right;">Pedidos</th>
                                <th style="text-align:right;">Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bySource as $src => $row)
                                <tr>
                                    <td>
                                        @if ($src === 'qr')
                                            <span class="k-rep-origin-badge qr">
                                                <i class="ri-qr-code-line"></i> QR Salón
                                            </span>
                                        @elseif ($src === 'counter')
                                            <span class="k-rep-origin-badge counter">
                                                <i class="ri-store-line"></i> Caja POS
                                            </span>
                                        @else
                                            <span class="k-rep-origin-badge" style="background:#e5e7eb; color:#374151;">
                                                {{ ucfirst($src ?? 'N/D') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align:right; font-weight:600;">{{ $row['orders'] }}</td>
                                    <td style="text-align:right; font-weight:700; color: #111827;">
                                        ${{ number_format($row['total'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align:center; color:#9ca3af; font-size:0.82rem; padding: 1.5rem;">
                                        Sin datos de origen en este período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tipo de Servicio --}}
                <div class="k-rep-section-title">
                    <span>Canal de Consumo</span>
                </div>
                <div class="k-rep-table-wrapper">
                    <table class="k-rep-table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th style="text-align:right;">Pedidos</th>
                                <th style="text-align:right;">Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($byType as $type => $row)
                                <tr>
                                    <td>
                                        @if ($type === 'dine_in')
                                            <span class="k-rep-type-badge dine-in">
                                                <i class="ri-restaurant-line"></i> Comer Aquí
                                            </span>
                                        @elseif ($type === 'take_away')
                                            <span class="k-rep-type-badge take-away">
                                                <i class="ri-shopping-bag-line"></i> Para Llevar
                                            </span>
                                        @else
                                            <span class="k-rep-type-badge default">
                                                <i class="ri-map-pin-line"></i> {{ ucfirst($type ?? 'Sin Especificar') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align:right; font-weight:600;">{{ $row['orders'] }}</td>
                                    <td style="text-align:right; font-weight:700; color: #111827;">
                                        ${{ number_format($row['total'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align:center; color:#9ca3af; font-size:0.82rem; padding: 1.5rem;">
                                        Sin datos de canal en este período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
