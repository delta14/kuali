{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Kuali') }} - @yield('title', 'Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fuentes (alojadas localmente, sin depender de internet) --}}
    <link rel="stylesheet" href="{{ asset('vendor/fonts/poppins.css') }}">

    {{-- Iconos --}}
    <link rel="stylesheet" href="{{ asset('vendor/remixicon/remixicon.css') }}">

    {{-- CSS base del panel --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    {{-- Si ya tienes kuali-admin.css, lo dejamos listo --}}
    @if (file_exists(public_path('css/kuali-admin.css')))
        <link rel="stylesheet" href="{{ asset('css/kuali-admin.css') }}">
    @endif

    {{-- CSS específico de cada vista --}}
    @stack('styles')
</head>

<body class="k-body">
    <div class="k-app">
        {{-- SIDEBAR --}}
        <aside class="k-sidebar">
            <div class="k-sidebar-header">
                <img src="{{ asset('img/kuali/logo01.png') }}" alt="Kuali" class="k-logo-img">
            </div>

            @php
                $user = auth()->user();
                $isSuperAdmin = $user?->is_super_admin ?? false;
            @endphp

            <nav class="k-menu">
                @if ($isSuperAdmin)
                    {{-- SUPERADMIN --}}
                    <a href="{{ route('super.dashboard') }}"
                        class="k-menu-item {{ request()->routeIs('super.dashboard') ? 'k-menu-item--active' : '' }}"
                        data-tooltip="Dashboard">
                        <span class="k-menu-icon"><i class="ri-home-5-line"></i></span>
                        <span class="k-menu-label">Dashboard</span>
                    </a>

                    <a href="{{ route('super.businesses.index') }}"
                        class="k-menu-item {{ request()->routeIs('super.businesses.*') ? 'k-menu-item--active' : '' }}"
                        data-tooltip="Comercios">
                        <span class="k-menu-icon"><i class="ri-store-2-line"></i></span>
                        <span class="k-menu-label">Comercios</span>
                    </a>
                @else
                    {{-- ADMIN DE COMERCIO --}}
                    <a href="{{ route('dashboard') }}"
                        class="k-menu-item {{ request()->routeIs('dashboard') ? 'k-menu-item--active' : '' }}"
                        data-tooltip="Inicio">
                        <span class="k-menu-icon"><i class="ri-home-5-line"></i></span>
                        <span class="k-menu-label">Inicio</span>
                    </a>

                    <a href="{{ route('tables.index') }}"
                        class="k-menu-item {{ request()->routeIs('tables.*') ? 'k-menu-item--active' : '' }}"
                        data-tooltip="Mesas">
                        <span class="k-menu-icon"><i class="ri-layout-grid-fill"></i></span>
                        <span class="k-menu-label">Mesas</span>
                    </a>

                    <a href="{{ route('categories.index') }}"
                        class="k-menu-item {{ request()->routeIs('categories.*') ? 'k-menu-item--active' : '' }}"
                        data-tooltip="Categorías">
                        <span class="k-menu-icon"><i class="ri-scissors-cut-line"></i></span>
                        <span class="k-menu-label">Categorías</span>
                    </a>

                    <a href="{{ route('products.index') }}"
                        class="k-menu-item {{ request()->routeIs('products.*') ? 'k-menu-item--active' : '' }}"
                        data-tooltip="Productos">
                        <span class="k-menu-icon"><i class="ri-restaurant-2-line"></i></span>
                        <span class="k-menu-label">Productos</span>
                    </a>

                    <a href="{{ route('orders.index') }}"
                        class="k-menu-item {{ request()->routeIs('orders.*') ? 'k-menu-item--active' : '' }}"
                        data-tooltip="Pedidos">
                        <span class="k-menu-icon"><i class="ri-receipt-line"></i></span>
                        <span class="k-menu-label">Pedidos</span>
                    </a>

                    <a href="{{ route('reports.sales') }}" 
                    class="k-menu-item  {{ request()->routeIs('reports.*') ? 'k-menu-item--active' : '' }} "
                    data-tooltip="Reportes">
                        <span class="k-menu-icon"><i class="ri-bar-chart-2-line"></i></span>
                        <span class="k-menu-label">Reportes</span>
                    </a>

                @endif
            </nav>

            <div class="k-sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="k-logout-btn" data-tooltip="Salir">
                        <i class="ri-logout-circle-r-line"></i>
                    </button>
                </form>
            </div>
        </aside>


        {{-- CONTENIDO PRINCIPAL --}}
        <main class="k-main">
            @yield('content')
        </main>
    </div>

    {{-- Sidebar (si luego agregas toggle) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const app = document.querySelector('.k-app');
            const toggle = document.getElementById('sidebarToggle');
            if (app && toggle) {
                toggle.addEventListener('click', () => {
                    app.classList.toggle('k-app--collapsed');
                });
            }
        });
    </script>

    {{-- Scripts específicos de cada vista --}}
    @stack('scripts')

    @auth
        {{-- Contenedor de notificaciones --}}
        <div id="k-notification-container" class="k-notification-container"></div>

        <link rel="stylesheet" href="{{ asset('css/toast-notifications.css') }}">

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let lastSeenOrderId = 0;
                const checkIntervalMs = 8000; // 8 segundos
                const container = document.getElementById('k-notification-container');
                const checkUrl = "{{ route('orders.check-new') }}";

                // Sonido de doble campana premium synthesizado
                function playNotificationChime() {
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        
                        // Nota 1
                        const osc1 = audioCtx.createOscillator();
                        const gain1 = audioCtx.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(659.25, audioCtx.currentTime); // E5
                        gain1.gain.setValueAtTime(0.08, audioCtx.currentTime);
                        gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
                        osc1.connect(gain1);
                        gain1.connect(audioCtx.destination);
                        osc1.start();
                        osc1.stop(audioCtx.currentTime + 0.6);
                        
                        // Nota 2
                        setTimeout(() => {
                            const osc2 = audioCtx.createOscillator();
                            const gain2 = audioCtx.createGain();
                            osc2.type = 'sine';
                            osc2.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                            gain2.gain.setValueAtTime(0.12, audioCtx.currentTime);
                            gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);
                            osc2.connect(gain2);
                            gain2.connect(audioCtx.destination);
                            osc2.start();
                            osc2.stop(audioCtx.currentTime + 0.8);
                        }, 150);
                    } catch (e) {
                        console.warn("AudioContext blocked or not supported:", e);
                    }
                }

                // Mostrar el toast en la interfaz
                function showOrderToast(order) {
                    if (!container) return;

                    const toast = document.createElement('div');
                    toast.className = 'k-toast';
                    
                    toast.innerHTML = `
                        <div class="k-toast-icon-wrap k-toast-icon-pulse">
                            <i class="ri-notification-3-line"></i>
                        </div>
                        <div class="k-toast-body">
                            <h4 class="k-toast-header">¡Nuevo Pedido Móvil!</h4>
                            <p class="k-toast-desc">
                                <strong>${order.table_name}</strong> • ${order.customer_name}<br>
                                Total: $${Number(order.total).toFixed(2)}
                            </p>
                            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="k-toast-action">
                                <i class="ri-receipt-line"></i>
                                <span>Ver Comanda</span>
                            </a>
                        </div>
                        <button type="button" class="k-toast-close">
                            <i class="ri-close-line"></i>
                        </button>
                        <div class="k-toast-progress"></div>
                    `;

                    // Añadir al DOM
                    container.appendChild(toast);

                    // Animar entrada
                    setTimeout(() => {
                        toast.classList.add('k-toast--visible');
                    }, 50);

                    // Reproducir campana
                    playNotificationChime();

                    // Botón de cerrar
                    const closeBtn = toast.querySelector('.k-toast-close');
                    closeBtn.addEventListener('click', () => {
                        dismissToast(toast);
                    });

                    // Auto desvanecer en 7.5 segundos
                    const timeoutId = setTimeout(() => {
                        dismissToast(toast);
                    }, 7500);

                    function dismissToast(el) {
                        el.classList.remove('k-toast--visible');
                        setTimeout(() => {
                            el.remove();
                        }, 500);
                    }
                }

                // Poller principal
                async function pollNewOrders() {
                    try {
                        const response = await fetch(`${checkUrl}?last_id=${lastSeenOrderId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) return;

                        const data = await response.json();
                        if (data && data.ok) {
                            // Si es la inicialización
                            if (lastSeenOrderId === 0) {
                                lastSeenOrderId = data.last_id;
                                return;
                            }

                            // Si hay órdenes nuevas y no es la inicialización, notificarlas
                            if (data.has_new && data.orders && data.orders.length > 0) {
                                data.orders.forEach(order => {
                                    showOrderToast(order);
                                });
                            }
                            
                            lastSeenOrderId = data.last_id;
                        }
                    } catch (error) {
                        console.error('Error polling for new orders:', error);
                    }
                }

                // Inicializar baseline de inmediato
                pollNewOrders();

                // Programar intervalo
                setInterval(pollNewOrders, checkIntervalMs);
            });
        </script>
    @endauth

</body>

</html>
