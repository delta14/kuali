{{-- resources/views/super/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Súper Admin')

@section('content')
    <div class="k-main-inner">

        {{-- HEADER --}}
        <div class="k-main-header">
            <div>
                <h1 class="k-main-title">Dashboard Súper Admin</h1>
                <p class="k-main-subtitle">
                    Panorama general de los comercios y administradores en Kuali.
                </p>
            </div>
        </div>

        {{-- GRID KPIs --}}
        <div class="k-main-grid">
            <div class="k-kpi-grid">
                <div class="k-kpi-card">
                    <span class="k-kpi-label">Comercios totales</span>
                    <span class="k-kpi-value">{{ $totalBusinesses }}</span>
                    <span class="k-kpi-helper">
                        Todos los negocios registrados en la plataforma.
                    </span>
                </div>

                <div class="k-kpi-card">
                    <span class="k-kpi-label">Comercios activos</span>
                    <span class="k-kpi-value">{{ $activeBusinesses }}</span>
                    <span class="k-kpi-helper">
                        Con el toggle de “Activo” encendido.
                    </span>
                </div>

                <div class="k-kpi-card">
                    <span class="k-kpi-label">Comercios sin admin</span>
                    <span class="k-kpi-value">{{ $businessNoAdmin }}</span>
                    <span class="k-kpi-helper">
                        Negocios que aún no tienen usuario administrador asignado.
                    </span>
                </div>

                <div class="k-kpi-card">
                    <span class="k-kpi-label">Admins de comercio</span>
                    <span class="k-kpi-value">{{ $totalAdmins }}</span>
                    <span class="k-kpi-helper">
                        Usuarios vinculados como administradores (role_id = 2).
                    </span>
                </div>
            </div>

            {{-- HERO / CTA --}}
            <div class="k-hero-card">
                <div class="k-hero-text">
                    <h2>Activa y monitorea comercios</h2>
                    <p>
                        Desde este panel puedes crear nuevos negocios, asignar admins
                        y dar seguimiento al crecimiento de Kuali.
                    </p>
                </div>
                <div class="k-hero-image"></div>
            </div>
        </div>

        {{-- SECCIÓN: Comercios recientes --}}
        <div class="k-section" style="margin-top:2rem;">
            <div class="k-section-header">
                <h2>Comercios recientes</h2>
                <a href="{{ route('super.businesses.index') }}" class="k-link">
                    Ver todos los comercios
                </a>
            </div>

            <div class="k-table-card">
                <table class="k-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Plan</th>
                            <th>Admin</th>
                            <th>Estado</th>
                            <th>Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBusinesses as $business)
                            @php
                                $mainAdmin = $business->admins->first();
                            @endphp
                            <tr>
                                <td data-label="Nombre">{{ $business->name }}</td>
                                <td data-label="Plan">
                                    {{ $business->plan ?: '—' }}
                                </td>
                                <td data-label="Admin">
                                    @if($business->admins_count > 0 && $mainAdmin)
                                        <div class="k-admin-chip">
                                            <span class="k-admin-chip__name">{{ $mainAdmin->name }}</span>
                                            <span class="k-admin-chip__email">{{ $mainAdmin->email }}</span>
                                            @if($business->admins_count > 1)
                                                <span class="k-admin-chip__more">
                                                    +{{ $business->admins_count - 1 }} más
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="k-pill k-pill--warning">Sin admin</span>
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    @if($business->is_active)
                                        <span class="k-pill k-pill--success">Activo</span>
                                    @else
                                        <span class="k-pill">Inactivo</span>
                                    @endif
                                </td>
                                <td data-label="Creado">
                                    {{ $business->created_at?->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Aún no hay comercios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- TOASTS (reutilizamos el stack global) --}}
    <div class="k-toast-stack" id="kToastStack">
        @if (session('success'))
            <div class="k-toast k-toast--success">
                <div class="k-toast__icon"><i class="ri-check-line"></i></div>
                <div class="k-toast__content">
                    <strong>Acción exitosa</strong>
                    <span>{{ session('success') }}</span>
                </div>
                <button class="k-toast__close">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="k-toast k-toast--error">
                <div class="k-toast__icon"><i class="ri-error-warning-line"></i></div>
                <div class="k-toast__content">
                    <strong>Ocurrió un error</strong>
                    <span>{{ session('error') }}</span>
                </div>
                <button class="k-toast__close">&times;</button>
            </div>
        @endif
    </div>
@endsection
