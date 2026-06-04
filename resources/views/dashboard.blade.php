{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="k-main-inner">
    {{-- Cabecera --}}
    <header class="k-main-header">
        <div>
            <h1 class="k-main-title">Bienvenido, Juan</h1>
            <p class="k-main-subtitle">
                Revisa el estado general de tu menú y pedidos de hoy.
            </p>
        </div>
        <button class="k-main-cta">Nuevo pedido manual</button>
    </header>

    {{-- GRID PRINCIPAL: KPIs + pastel --}}
    <section class="k-main-grid">
        {{-- Tarjetas KPI --}}
        <div class="k-kpi-grid">
            <article class="k-kpi-card">
                <span class="k-kpi-label">Productos</span>
                <span class="k-kpi-value">24</span>
                <span class="k-kpi-helper">Activos en el menú</span>
            </article>

            <article class="k-kpi-card">
                <span class="k-kpi-label">Pedidos hoy</span>
                <span class="k-kpi-value">5</span>
                <span class="k-kpi-helper">Confirmados</span>
            </article>

            <article class="k-kpi-card">
                <span class="k-kpi-label">Mesas ocupadas</span>
                <span class="k-kpi-value">8</span>
                <span class="k-kpi-helper">En servicio</span>
            </article>

            <article class="k-kpi-card">
                <span class="k-kpi-label">Ingresos hoy</span>
                <span class="k-kpi-value">$620</span>
                <span class="k-kpi-helper">MXN aproximado</span>
            </article>
        </div>

        {{-- Panel pastel / hero --}}
        <aside class="k-hero-card">
            <div class="k-hero-text">
                <h2>Digitaliza tu sabor</h2>
                <p>
                    Mantén tu menú siempre actualizado, muestra fotos
                    atractivas y recibe pedidos sin levantar una libreta.
                </p>
            </div>
            <div class="k-hero-image"></div>
        </aside>
    </section>

    {{-- Sección de pedidos recientes --}}
    <section class="k-section">
        <div class="k-section-header">
            <h2>Pedido reciente</h2>
            <a href="#" class="k-link">Ver todos</a>
        </div>

        <article class="k-recent-card">
            <div class="k-recent-info">
                <div class="k-recent-thumb"></div>
                <div>
                    <h3 class="k-recent-title">Pizza Especial Kuali</h3>
                    <p class="k-recent-subtitle">Mesa 4 · 2 personas</p>
                </div>
            </div>
            <div class="k-recent-meta">
                <span class="k-pill k-pill--success">En preparación</span>
                <span class="k-recent-time">5:00 min</span>
            </div>
        </article>
    </section>
</div>
@endsection


