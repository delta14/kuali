@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="k-main-inner k-premium-container">

    {{-- CONFIG para JS --}}
    <div id="kOrdersConfig" data-complete-url-template="{{ route('orders.complete', ['order' => '__ID__']) }}">
    </div>

    {{-- TOOLBAR --}}
    <header class="k-premium-header" style="border-bottom: none; margin-bottom: 0;">
        <div class="k-premium-title-wrap">
            <h1 style="font-size: 2rem;">Pedidos y Comandas</h1>
            <p class="k-premium-subtitle">
                Monitorea comanda por comanda las solicitudes desde tu salón QR y la caja POS.
            </p>
        </div>

        <div class="k-premium-header-actions" style="gap: 1rem;">
            {{-- Filtro como tabs --}}
            <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 99px; padding: 0.25rem; display: flex; gap: 0.15rem;">
                @php $status = request('status', 'pending'); @endphp
                <a href="{{ route('orders.index', ['status' => 'pending']) }}"
                    class="k-premium-badge {{ $status === 'pending' ? 'ready' : '' }}" 
                    style="text-decoration: none; font-size: 0.8rem; font-weight: 600; padding: 0.45rem 1.1rem; border-radius: 99px; display: inline-flex; align-items: center; justify-content: center; {{ $status === 'pending' ? 'background:#4f46e5; color:#ffffff; box-shadow: 0 4px 10px rgba(79,70,229,0.15);' : 'color:#4b5563; background:transparent;' }}">
                    En Proceso
                </a>

                <a href="{{ route('orders.index', ['status' => 'completed']) }}"
                    class="k-premium-badge {{ $status === 'completed' ? 'delivered' : '' }}"
                    style="text-decoration: none; font-size: 0.8rem; font-weight: 600; padding: 0.45rem 1.1rem; border-radius: 99px; display: inline-flex; align-items: center; justify-content: center; {{ $status === 'completed' ? 'background:#059669; color:#ffffff; box-shadow: 0 4px 10px rgba(5,150,105,0.15);' : 'color:#4b5563; background:transparent;' }}">
                    Completados
                </a>
            </div>

            <a href="{{ route('orders.pos') }}" class="k-main-cta" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.3rem;">
                <i class="ri-add-circle-line" style="font-size: 1.1rem;"></i>
                <span>Nuevo Pedido</span>
            </a>
        </div>
    </header>

    {{-- LAYOUT LISTA + DETALLE (SPLIT PANEL PREMIUM) --}}
    <div class="k-premium-main-grid" style="grid-template-columns: 1.25fr 1fr; gap: 1.5rem;">

        {{-- COLUMNA IZQUIERDA: LISTA DE PEDIDOS --}}
        <div class="k-premium-card" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; max-height: calc(100vh - 180px); overflow-y: auto;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.85rem; border-bottom: 1px solid #f3f4f6; flex-wrap: wrap; gap: 1rem;">
                <div class="k-premium-kpi-trend up" style="background: rgba(79, 70, 229, 0.08); color: #4f46e5; font-size: 0.82rem; padding: 0.3rem 0.8rem;">
                    Total: {{ $orders->count() }} comandas
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    {{-- Filtro por fecha --}}
                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                        <input type="date" id="kOrdersDate" class="k-input" value="{{ request('date') }}" style="height: 36px; font-size: 0.82rem; border-radius: 10px; border: 1px solid #e5e7eb; padding: 0 0.5rem; background: #ffffff;">
                        <button type="button" id="kOrdersDateFilterBtn" class="k-premium-btn-white" style="height: 36px; padding: 0 0.85rem; font-size: 0.8rem; border-radius: 10px; box-shadow: none;">
                            Filtrar
                        </button>
                    </div>

                    {{-- Buscador --}}
                    <div class="k-table-search" style="max-width: 200px; margin: 0;">
                        <div style="position: relative; width: 100%;">
                            <i class="ri-search-line" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.85rem;"></i>
                            <input type="text" id="kOrdersSearch" placeholder="Buscar folio..." style="width: 100%; padding-left: 2rem; background: #ffffff; border: 1px solid #e5e7eb; color: #1f2937; border-radius: 99px; outline: none; font-size: 0.82rem; height: 36px;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- LISTA DE FILAS --}}
            <div class="k-orders-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($orders as $order)
                    @php
                        $folio = $order->id;
                        $mesa = $order->table_name ?? ($order->table?->name ?? 'Sin mesa');
                        $fechaTexto = $order->created_at?->format('d/m H:i');
                        $estadoTexto = $order->status_label ?? ucfirst($order->status ?? 'Pending');
                        $totalTexto = '$' . number_format($order->total ?? 0, 2);

                        // items del pedido
                        $items = $order->items?->map(function ($item) {
                            return [
                                'name' => $item->product_name ?? ($item->product?->name ?? 'Producto'),
                                'note' => $item->notes ?? '',
                                'qty' => (int) ($item->quantity ?? 1),
                                'price' => (float) ($item->unit_price ?? 0),
                                'total' => (float) ($item->total_price ?? ($item->unit_price ?? 0) * ($item->quantity ?? 1)),
                            ];
                        })->values() ?? collect();
                    @endphp

                    <button type="button" class="k-premium-order-row js-order-row" 
                            data-order-id="{{ $order->id }}"
                            data-status="{{ $order->status }}" 
                            data-status-label="{{ $estadoTexto }}"
                            data-folio="#{{ $folio }}" 
                            data-mesa="{{ $mesa }}"
                            data-fecha="{{ $order->created_at?->format('d/m/Y H:i') }}" 
                            data-total="{{ $totalTexto }}"
                            data-subtotal="{{ $order->subtotal }}" 
                            data-items='@json($items)'
                            data-source="{{ $order->source ?? '' }}" 
                            data-order-type="{{ $order->order_type ?? '' }}"
                            data-search="{{ '#' . $folio . ' ' . $mesa . ' ' . $fechaTexto }}"
                            style="width: 100%; border: 1px solid #f3f4f6; text-align: left; cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-radius: 16px; background: #fafafa; transition: all 0.2s ease;">
                        
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div class="k-premium-order-badge" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99, 102, 241, 0.08); color: #4f46e5; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(99, 102, 241, 0.12);">
                                #{{ $folio }}
                            </div>
                            
                            <div>
                                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 0.4rem;">
                                    <span>{{ $mesa }}</span>
                                    @if ($order->source === 'counter')
                                        <span class="k-premium-badge pending" style="font-size: 0.65rem; padding: 0.1rem 0.4rem; font-weight: 700;">Mostrador</span>
                                    @elseif ($order->source === 'qr')
                                        <span class="k-premium-badge ready" style="font-size: 0.65rem; padding: 0.1rem 0.4rem; font-weight: 700;">Salón QR</span>
                                    @endif
                                </h4>
                                <p style="margin: 0.2rem 0 0; font-size: 0.78rem; color: #6b7280;">
                                    {{ $order->customer_name ?: 'Cliente' }} • {{ $fechaTexto }}
                                </p>
                            </div>
                        </div>

                        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem;">
                            <span style="font-size: 0.92rem; font-weight: 700; color: #111827;">{{ $totalTexto }}</span>
                            <span class="k-premium-badge {{ $order->status }}" style="font-size: 0.68rem; padding: 0.15rem 0.5rem; font-weight: 600;">
                                {{ $estadoTexto }}
                            </span>
                        </div>
                    </button>

                @empty
                    <div style="text-align: center; padding: 4rem 1rem; color: #6b7280;">
                        <div style="font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem;">
                            <i class="ri-inbox-archive-line"></i>
                        </div>
                        <h4 style="font-size: 0.95rem; font-weight: 600; color: #374151; margin-bottom: 0.2rem;">Sin comandas activas</h4>
                        <p style="font-size: 0.8rem; max-width: 250px; margin: 0 auto; line-height: 1.4;">
                            No se encontraron pedidos registrados para esta selección de filtros.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- COLUMNA DERECHA: DETALLE DEL PEDIDO (TICKET TÉRMICO DIGITAL) --}}
        <div class="k-orders-detail" id="kOrderDetailPanel" style="position: sticky; top: 1.5rem; height: max-content;">

            {{-- TICKET DE COMANDA ACTIVA --}}
            <div class="k-orders-detail-body k-premium-card" id="kOrderDetailBody" style="background: #ffffff; padding: 2rem; border-radius: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 1.5rem; position: relative;">
                
                {{-- Efecto corte troquelado sutil superior --}}
                <div style="position: absolute; top: 0; left: 1rem; right: 1rem; height: 6px; background-image: radial-gradient(circle, #f3f4f6 3px, transparent 4px); background-size: 12px 12px; background-repeat: repeat-x;"></div>


                {{-- Encabezado Ticket --}}
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px dashed #e5e7eb; padding-bottom: 1.25rem; margin-top: 0.5rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span id="od_source_badge" class="k-premium-badge ready" style="font-size: 0.65rem; font-weight: 700;">QR</span>
                            <span id="od_type_label" class="k-premium-badge pending" style="font-size: 0.65rem; font-weight: 700;">Para consumir aquí</span>
                        </div>
                        <h2 style="font-size: 1.5rem; font-weight: 800; color: #111827; margin: 0.35rem 0 0 0;" id="od_folio">#000</h2>
                    </div>

                    <div style="text-align: right;">
                        <span style="font-size: 0.75rem; color: #9ca3af; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem;">Total de Comanda</span>
                        <strong id="od_total" style="font-size: 1.6rem; font-weight: 800; color: #4f46e5; letter-spacing: -0.02em;">$0.00</strong>
                    </div>
                </div>

                {{-- Metadatos de Ocupación e Información del cliente --}}
                <div style="background: #fafafa; border: 1px solid #f3f4f6; border-radius: 16px; padding: 1rem; display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #6b7280; font-weight: 500;">Mesa / Ubicación:</span>
                        <strong id="od_mesa" style="color: #111827;">Mesa 1</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #6b7280; font-weight: 500;">Hora del pedido:</span>
                        <span id="od_fecha" style="color: #4b5563; font-weight: 500;">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #6b7280; font-weight: 500;">Estatus del pedido:</span>
                        <span id="od_estado" class="k-premium-badge pending" style="font-size: 0.7rem; font-weight: 600; padding: 0.15rem 0.5rem;">Pendiente</span>
                    </div>
                </div>

                {{-- LISTA DE PLATILLOS (PRODUCTOS) --}}
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <h3 style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: #4b5563; letter-spacing: 0.08em; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="ri-bowl-line" style="color: #4f46e5;"></i>
                        <span>Desglose de Platillos</span>
                    </h3>
                    
                    {{-- Inyección por JS --}}
                    <div id="od_items" style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 200px; overflow-y: auto; padding-right: 0.25rem;">
                    </div>
                </div>

                {{-- RESUMEN Y BOTÓN ACCIÓN --}}
                <div style="border-top: 2px dashed #e5e7eb; padding-top: 1.25rem; display: flex; flex-direction: column; gap: 1rem; margin-bottom: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.88rem; color: #6b7280;">
                        <span>Subtotal Comanda</span>
                        <span id="od_subtotal" style="font-weight: 600; color: #111827;">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f3f4f6; padding-top: 0.85rem;">
                        <span style="font-size: 0.95rem; font-weight: 700; color: #111827;">Total Pagado</span>
                        <strong id="od_total_footer" style="font-size: 1.35rem; font-weight: 800; color: #111827;">$0.00</strong>
                    </div>

                    <button type="button" class="k-btn-primary" id="kOrderCompleteBtn" style="width: 100%; border-radius: 99px; background: #4f46e5; border: none; color: #ffffff; font-weight: 600; padding: 0.8rem 1.5rem; font-size: 0.9rem; cursor: pointer; box-shadow: 0 6px 20px rgba(79, 70, 229, 0.25); display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;">
                        <i class="ri-checkbox-circle-line"></i>
                        <span>Marcar como completado</span>
                    </button>
                </div>

                {{-- Efecto corte troquelado inferior --}}
                <div style="position: absolute; bottom: 0; left: 1rem; right: 1rem; height: 6px; background-image: radial-gradient(circle, #f3f4f6 3px, transparent 4px); background-size: 12px 12px; background-repeat: repeat-x; transform: rotate(180deg);"></div>

            </div>
        </div>

    </div>
</div>

{{-- MODAL CONFIRMAR COMPLETAR PEDIDO --}}
<div class="k-modal" id="modalConfirmCompleteOrder">
    <div class="k-modal__backdrop js-modal-close" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); max-width: 440px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ri-checkbox-circle-line" style="color: #10b981; font-size: 1.3rem;"></i>
                <span>Completar pedido</span>
            </h2>
            <button type="button" class="k-modal__close js-modal-close" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>
        <div class="k-modal__body" style="padding: 1.5rem; font-size: 0.9rem; color: #4b5563; line-height: 1.5;">
            <p id="kCompleteConfirmText" style="margin: 0 0 0.5rem 0; font-weight: 600; color: #1f2937;">¿Marcar este pedido como pagado/completado?</p>
            <p style="margin: 0;">Esta comanda se registrará en las métricas de ingresos e historial de ventas.</p>
        </div>
        <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
            <button type="button" class="k-btn-secondary js-complete-cancel" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cancelar</button>
            <button type="button" class="k-btn-primary js-complete-confirm" style="border-radius: 99px; background: #10b981; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">Aceptar</button>
        </div>
    </div>
</div>

{{-- MODAL MENSAJE (ÉXITO / ERROR) --}}
<div class="k-modal" id="modalOrdersMessage">
    <div class="k-modal__backdrop js-modal-msg-close" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); max-width: 440px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 id="kMsgTitle" style="font-size: 1.2rem; font-weight: 700; color: #111827;">Mensaje</h2>
            <button type="button" class="k-modal__close js-modal-msg-close" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>
        <div class="k-modal__body" style="padding: 1.5rem; font-size: 0.9rem; color: #4b5563; line-height: 1.5;">
            <p id="kMsgBody" style="margin: 0;"></p>
        </div>
        <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
            <a href="#" id="kMsgWhatsappBtn" class="k-btn-secondary" target="_blank" style="display:none; text-decoration: none; border-radius: 99px; border: 1px solid #25d366; background: rgba(37, 211, 102, 0.05); color: #25d366; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; align-items: center; gap: 0.3rem;">
                <i class="ri-whatsapp-line"></i>
                <span>Enviar WhatsApp</span>
            </a>
            <button type="button" class="k-btn-primary js-modal-msg-close" style="border-radius: 99px; background: #4f46e5; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">Aceptar</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    {{-- CSS Inline de comandas e items dinámicos --}}
    <style>
        .k-order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.65rem 0.85rem;
            background: #fafafa;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            gap: 1rem;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .k-order-item:hover {
            border-color: rgba(99, 102, 241, 0.15);
            background: #ffffff;
        }
        .k-order-item-main {
            flex: 1;
        }
        .k-order-item-name {
            font-weight: 600;
            color: #111827;
        }
        .k-order-item-note {
            font-size: 0.72rem;
            color: #ea580c;
            background: rgba(234, 88, 12, 0.05);
            border-radius: 6px;
            padding: 0.15rem 0.4rem;
            display: inline-block;
            margin-top: 0.15rem;
            border: 1px solid rgba(234, 88, 12, 0.1);
        }
        .k-order-item-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-align: right;
        }
        .k-order-item-qty {
            font-weight: 700;
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.05);
            border-radius: 6px;
            padding: 0.1rem 0.35rem;
            font-size: 0.75rem;
        }
        .k-order-item-price {
            font-size: 0.78rem;
            color: #6b7280;
        }
        .k-order-item-total {
            font-weight: 700;
            color: #111827;
            font-size: 0.85rem;
            min-width: 60px;
        }
    </style>
    <script src="{{ asset('js/business-orders.js') }}"></script>
@endpush
