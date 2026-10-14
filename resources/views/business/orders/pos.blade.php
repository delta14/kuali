{{-- resources/views/business/orders/pos.blade.php --}}
@extends('layouts.app')

@section('title', 'Tomar pedido en mostrador')

@section('content')
    {{-- CSS Scoped de Alta Fidelidad para POS (Toast/Shopify-style) --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/business-pos.css') }}">
    @endpush

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
