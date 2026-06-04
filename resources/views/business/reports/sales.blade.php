{{-- resources/views/business/reports/sales.blade.php --}}
@extends('layouts.app')

@section('title', 'Reportes de ventas')

@section('content')
    {{-- CSS Scoped de Alta Fidelidad para Reportes (Stripe-style) --}}
    <style>
        :root {
            --rep-primary: #4f46e5;
            --rep-primary-glow: rgba(79, 70, 229, 0.1);
            --rep-secondary: #059669;
            --rep-accent: #f97316;
            --rep-bg-card: #ffffff;
            --rep-text-main: #1f1a3a;
            --rep-text-muted: #6b7280;
            --rep-border: #f3f4f6;
        }

        .k-rep-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            font-family: 'Poppins', sans-serif;
            color: var(--rep-text-main);
            padding: 0.5rem 0.5rem 3rem 0.5rem;
        }

        /* Toolbar Premium */
        .k-rep-header-premium {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(79, 70, 229, 0.08);
            padding-bottom: 1.5rem;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .k-rep-title-wrap h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 50%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            letter-spacing: -0.03em;
        }

        .k-rep-subtitle {
            font-size: 0.95rem;
            color: var(--rep-text-muted);
            margin-top: 0.35rem;
        }

        /* Formulario Filtros */
        .k-rep-filters-form {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .k-rep-date-range-box {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 1rem;
            border-radius: 99px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);
            transition: border-color 0.2s ease;
        }

        .k-rep-date-range-box:focus-within {
            border-color: var(--rep-primary);
        }

        .k-rep-date-input {
            border: none;
            background: transparent;
            color: #374151;
            font-size: 0.82rem;
            font-family: inherit;
            outline: none;
            cursor: pointer;
            width: 120px;
        }

        .k-rep-date-separator {
            font-size: 0.8rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .k-rep-submit-btn {
            background: linear-gradient(135deg, var(--rep-primary) 0%, #6366f1 100%);
            color: #ffffff;
            border: none;
            border-radius: 99px;
            padding: 0.55rem 1.3rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .k-rep-submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
        }

        /* KPIs Premium Grid */
        .k-rep-kpis-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .k-rep-kpis-grid {
                grid-template-columns: 1fr;
            }
        }

        .k-rep-kpi-card {
            background: #ffffff;
            border: 1px solid rgba(229, 231, 235, 0.7);
            border-radius: 20px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px -2px rgba(139, 92, 246, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .k-rep-kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.08);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .k-rep-kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .k-rep-kpi-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .k-rep-kpi-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .k-rep-kpi-icon-wrap.purple {
            background: rgba(99, 102, 241, 0.08);
            color: #4f46e5;
        }

        .k-rep-kpi-icon-wrap.emerald {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
        }

        .k-rep-kpi-icon-wrap.orange {
            background: rgba(249, 115, 22, 0.08);
            color: #ea580c;
        }

        .k-rep-kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #111827;
            margin: 0.25rem 0;
            letter-spacing: -0.02em;
        }

        .k-rep-kpi-trend {
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            border-radius: 99px;
            padding: 0.15rem 0.5rem;
            background: #ecfdf5;
            color: #065f46;
            margin-top: 0.25rem;
        }

        .k-rep-kpi-sparkline {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 20px;
        }

        .k-rep-spark-bar {
            width: 5px;
            background: #e5e7eb;
            border-radius: 99px;
        }

        .k-rep-spark-bar.active {
            background: var(--rep-primary);
        }

        /* Grid de Datos Principal */
        .k-rep-main-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        @media (max-width: 991px) {
            .k-rep-main-grid {
                grid-template-columns: 1fr;
            }
        }

        .k-rep-data-card {
            background: #ffffff;
            border: 1px solid rgba(229, 231, 235, 0.7);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px -2px rgba(139, 92, 246, 0.02);
        }

        .k-rep-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid var(--rep-border);
            padding-bottom: 0.75rem;
        }

        .k-rep-card-header h2 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            color: var(--rep-text-main);
        }

        .k-rep-card-badge {
            font-size: 0.78rem;
            font-weight: 600;
            background: rgba(79, 70, 229, 0.08);
            color: var(--rep-primary);
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
        }

        /* Tabla Estilo Stripe */
        .k-rep-table-wrapper {
            overflow-x: auto;
        }

        .k-rep-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .k-rep-table th {
            padding: 0.85rem 1rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--rep-border);
        }

        .k-rep-table td {
            padding: 1rem;
            font-size: 0.88rem;
            color: #374151;
            border-bottom: 1px solid var(--rep-border);
            transition: background 0.15s ease;
        }

        .k-rep-table tbody tr:last-child td {
            border-bottom: none;
        }

        .k-rep-table tbody tr:hover td {
            background: rgba(79, 70, 229, 0.015);
        }

        /* Fila de Producto Estilo Menú */
        .k-rep-prod-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .k-rep-prod-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.06) 0%, rgba(99, 102, 241, 0.09) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--rep-primary);
            font-size: 1rem;
            font-weight: 600;
            border: 1px solid rgba(79, 70, 229, 0.15);
        }

        .k-rep-prod-name {
            font-weight: 600;
            color: #1f2937;
        }

        /* Sección de Reportes en Tabla lateral */
        .k-rep-section-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 1.25rem 0 0.75rem 0;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .k-rep-section-title::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 14px;
            background: var(--rep-primary);
            border-radius: 99px;
        }

        /* Badges de Origen y Tipo */
        .k-rep-origin-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .k-rep-origin-badge.qr {
            background: rgba(168, 85, 247, 0.08);
            color: #a855f7;
            border: 1px solid rgba(168, 85, 247, 0.15);
        }

        .k-rep-origin-badge.counter {
            background: rgba(249, 115, 22, 0.08);
            color: #ea580c;
            border: 1px solid rgba(249, 115, 22, 0.15);
        }

        .k-rep-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .k-rep-type-badge.dine-in {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        .k-rep-type-badge.take-away {
            background: rgba(59, 130, 246, 0.08);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, 0.15);
        }

        .k-rep-type-badge.default {
            background: rgba(107, 114, 128, 0.08);
            color: #4b5563;
            border: 1px solid rgba(107, 114, 128, 0.15);
        }

        /* Estado Vacío */
        .k-rep-empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--rep-text-muted);
        }

        .k-rep-empty-state i {
            font-size: 3.5rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 1.25rem;
        }

        .k-rep-empty-state h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        .k-rep-empty-state p {
            font-size: 0.85rem;
            max-width: 280px;
            margin: 0 auto;
        }
    </style>

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
