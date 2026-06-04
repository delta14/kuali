<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>{{ $business->name }} - Menú</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fuente y RemixIcon --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    {{-- CSS del menú público --}}
    <link rel="stylesheet" href="{{ asset('css/public-menu.css') }}">
    {{-- Reusar estilos de modales k-modal (los mismos del admin) --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    {{-- Config para JS --}}

    <body>
        <div id="pmConfig" data-order-endpoint="{{ route('public.orders.store') }}"
            data-business-id="{{ $business->id }}"
            @if (!empty($table)) data-table-id="{{ $table->id }}" @endif>
        </div>


        <div class="pm-app">
            {{-- ==== HOME / LISTA ==== --}}
            <header class="pm-header">
                <div class="pm-top-row">
                    <div>
                        <div class="pm-welcome">Bienvenido!</div>
                        <div class="pm-name">{{ $business->name }}</div>

                        @if ($tableLabel)
                            <div class="pm-table-pill">
                                <i class="ri-restaurant-line"></i>
                                <span>{{ $tableLabel }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Botón carrito en header --}}
                    <button type="button" class="pm-menu-btn" id="pmCartButton">
                        <i class="ri-shopping-bag-3-line"></i>
                        <span class="pm-cart-badge" id="pmCartBadge" style="display:none;">0</span>
                    </button>
                </div>

                <div class="pm-search-box">
                    <div class="pm-search">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Buscar un platillo…" id="pmSearchInput">
                    </div>
                </div>
            </header>

            <section class="pm-cats-section">
                <div class="pm-section-title pm-cats-title">Categorías</div>

                <div class="pm-cat-scroll">
                    @foreach ($categories as $cat)
                        @php
                            $icon = 'ri-bowl-line';
                            $nameLower = Str::lower($cat->name);
                            if (str_contains($nameLower, 'café') || str_contains($nameLower, 'coffee')) {
                                $icon = 'ri-cup-line';
                            } elseif (str_contains($nameLower, 'malteada') || str_contains($nameLower, 'frapp')) {
                                $icon = 'ri-ice-cream-line';
                            } elseif (
                                str_contains($nameLower, 'postre') ||
                                str_contains($nameLower, 'pastel') ||
                                str_contains($nameLower, 'dulce')
                            ) {
                                $icon = 'ri-cake-2-line';
                            } elseif (str_contains($nameLower, 'snack') || str_contains($nameLower, 'botana')) {
                                $icon = 'ri-restaurant-2-line';
                            } elseif (
                                str_contains($nameLower, 'sandwich') ||
                                str_contains($nameLower, 'baguette') ||
                                str_contains($nameLower, 'panini')
                            ) {
                                $icon = 'ri-bread-slice-line';
                            }
                        @endphp
                        <button class="pm-cat-pill" data-cat="{{ $cat->id }}">
                            <span class="pm-cat-icon"><i class="{{ $icon }}"></i></span>
                            <span>{{ $cat->name }}</span>
                        </button>
                    @endforeach
                </div>
            </section>

            @foreach ($categories as $cat)
                @if ($cat->products->count())
                    <section style="margin-bottom:1.3rem;" data-category-block="{{ $cat->id }}">
                        <div class="pm-section-title" style="font-size:0.95rem; margin-bottom:0.55rem;">
                            {{ $cat->name }}
                        </div>

                        <div class="pm-grid">
                            @foreach ($cat->products as $product)
                                <article class="pm-card" data-product-name="{{ Str::lower($product->name) }}">
                                    <div class="pm-card-img">
                                        @if ($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                                alt="{{ $product->name }}">
                                        @else
                                            <div class="pm-dish-placeholder" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, rgba(88,28,87,0.04) 0%, rgba(88,28,87,0.08) 100%); color:var(--pm-primary); font-size:2rem;">
                                                @php
                                                    $catName = strtolower($cat->name);
                                                    $phIcon = 'ri-restaurant-line';
                                                    if (str_contains($catName, 'bebida') || str_contains($catName, 'soda') || str_contains($catName, 'frapp') || str_contains($catName, 'malteada')) {
                                                        $phIcon = 'ri-cup-line';
                                                    } elseif (str_contains($catName, 'postre') || str_contains($catName, 'crepa') || str_contains($catName, 'waffle')) {
                                                        $phIcon = 'ri-cake-3-line';
                                                    }
                                                @endphp
                                                <i class="{{ $phIcon }}"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="pm-card-body">
                                        <div class="pm-card-title">{{ $product->name }}</div>
                                        @if ($product->description)
                                            <div class="pm-card-desc">
                                                {{ Str::limit($product->description, 55) }}
                                            </div>
                                        @endif
                                        <div class="pm-card-bottom">
                                            <span class="pm-card-price">
                                                ${{ number_format($product->price, 2) }}
                                            </span>

                                            {{-- Botón + con data-* para el detalle y el carrito --}}
                                            <button class="pm-add-btn js-add-product" type="button"
                                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-description="{{ $product->description }}"
                                                data-image="{{ $product->image_path ? asset('storage/' . $product->image_path) : '' }}">
                                                <i class="ri-add-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach

            @if ($categories->sum(fn($c) => $c->products->count()) === 0)
                <p class="pm-empty">Aún no hay productos publicados.</p>
            @endif
        </div>

        {{-- ===== PANTALLA DETALLE PRODUCTO ===== --}}
        <div class="pm-modal pm-product-screen" id="pmProductModal">
            <div class="pm-modal__backdrop" data-pm-close></div>
            <div class="pm-modal__dialog pm-product-screen-card">
                <div class="pm-modal__header pm-product-header">
                    <button type="button" class="pm-modal__close" data-pm-close>
                        <i class="ri-arrow-left-line"></i>
                    </button>
                    <div class="pm-product-header-title">
                        <h2 class="pm-modal__title" id="pmModalTitle">Producto</h2>
                        <span class="pm-modal__price-top" id="pmModalPriceTop">$0.00</span>
                    </div>
                </div>

                <div class="pm-modal__image-wrap pm-product-image-wrap" id="pmModalImageWrap">
                    <img src="" alt="" id="pmModalImage" style="display:none;">
                </div>

                <p class="pm-modal__description" id="pmModalDescription"></p>

                <div class="pm-modal-row">
                    <span class="pm-modal-label">Cantidad</span>
                    <div class="pm-stepper">
                        <button type="button" class="pm-stepper-btn" data-action="minus">−</button>
                        <span class="pm-stepper-value" id="pmStepperValue">1</span>
                        <button type="button" class="pm-stepper-btn" data-action="plus">+</button>
                    </div>
                    <input type="hidden" id="pmModalQty" value="1">
                </div>

                <div class="pm-modal__row">
                    <label class="pm-modal__label" for="pmModalNote">Notas</label>
                    <textarea id="pmModalNote" class="pm-modal__textarea" rows="2"
                        placeholder="Sin azúcar, leche deslactosada, etc."></textarea>
                </div>

                <div class="pm-modal__price">
                    <span>Total:</span>
                    <strong id="pmModalTotal">$0.00</strong>
                </div>

                <div class="pm-modal__footer">
                    <button type="button" class="pm-modal__btn-primary" id="pmAddToCartBtn">
                        <i class="ri-shopping-bag-3-line"></i>
                        <span>Agregar al pedido</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== PANTALLA CARRITO ===== --}}
        <div id="pm-cart-screen" class="pm-cart-screen" aria-hidden="true">
            <div class="pm-cart-screen-card">
                <div class="pm-cart-screen-header">
                    <button type="button" id="pm-cart-close" class="pm-icon-btn">
                        <i class="ri-arrow-left-line"></i>
                    </button>
                    <div class="pm-cart-screen-titles">
                        <h2>Your Order Summary</h2>
                        @if ($tableLabel)
                            <span class="pm-cart-table">
                                <i class="ri-restaurant-line"></i> {{ $tableLabel }}
                            </span>
                        @endif
                    </div>
                </div>

                <div id="pm-cart-list" class="pm-cart-items">
                    {{-- Items del carrito (JS) --}}
                </div>

                <div class="pm-cart-summary">
                    <div class="pm-cart-summary-row">
                        <span>Subtotal</span>
                        <strong id="pm-cart-subtotal">$0.00</strong>
                    </div>
                    <div class="pm-cart-summary-row pm-cart-summary-total">
                        <span>Total</span>
                        <strong id="pm-cart-total-summary">$0.00</strong>
                    </div>

                    <button id="pm-cart-send" class="pm-btn-primary">
                        Enviar pedido
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== MODAL: carrito vacío ===== --}}
        <div id="pm-empty-cart" class="pm-empty-modal" hidden>
            <div class="pm-empty-modal-card">
                <div class="pm-empty-icon">
                    <i class="ri-shopping-bag-line"></i>
                </div>
                <h3>Tu pedido está vacío</h3>
                <p>Agrega algún platillo desde el menú para verlo aquí.</p>
                <button type="button" id="pm-empty-close" class="pm-btn-primary">
                    Ver menú
                </button>
            </div>
        </div>

        {{-- ===== Ticket / confirmación de pedido (local) ===== --}}
        <div id="pm-ticket" class="pm-ticket" hidden>
            <div class="pm-ticket-card">
                <div class="pm-ticket-icon">
                    ✓
                </div>
                <h2>¡Pedido enviado!</h2>
                <p class="pm-ticket-text">
                    Tu orden <strong id="pm-ticket-id"></strong> se ha guardado en este dispositivo.
                    Muéstrala en caja o espera la confirmación del negocio.
                </p>
                <button id="pm-ticket-close" class="pm-btn-primary">
                    Cerrar
                </button>
            </div>
        </div>

        {{-- MODAL: Datos del cliente --}}
        <div class="k-modal" id="pmCustomerModal" hidden>
            <div class="k-modal__backdrop js-pm-close-modal"></div>
            <div class="k-modal__dialog">
                <div class="k-modal__header">
                    <h2>Datos para tu pedido</h2>
                    <button type="button" class="k-modal__close js-pm-close-modal">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <div class="k-modal__body k-form">
                    <p style="font-size:.85rem; color:#6b7280; margin-bottom:.75rem;">
                        Cuéntanos a nombre de quién va el pedido y un número de contacto para
                        avisarte cuando esté listo.
                    </p>

                    <div class="k-field">
                        <label class="k-label">Nombre *</label>
                        <input type="text" id="pmCustomerName" class="k-input" placeholder="Ej. Roberto"
                            required>
                    </div>

                    <div class="k-field">
                        <label class="k-label">WhatsApp / Teléfono *</label>
                        <input type="tel" id="pmCustomerPhone" class="k-input" placeholder="Ej. 5512345678"
                            required>
                        <div class="k-field-message">
                            Usaremos este número solo para notificarte sobre tu pedido.
                        </div>
                    </div>

                    <div id="pmCustomerError"
                        style="display:none; font-size:.8rem; color:#dc2626; margin-top:.25rem;">
                    </div>
                </div>

                <div class="k-modal__footer">
                    <button type="button" class="k-btn-secondary js-pm-close-modal">
                        Cancelar
                    </button>
                    <button type="button" class="k-btn-primary" id="pmCustomerConfirmBtn">
                        Enviar pedido
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL: Gracias por tu pedido (opcional, sobre el ticket) --}}
        <div class="k-modal" id="pmThanksModal" hidden>
            <div class="k-modal__backdrop js-pm-thanks-close"></div>
            <div class="k-modal__dialog">
                <div class="k-modal__header">
                    <h2>¡Pedido enviado!</h2>
                    <button type="button" class="k-modal__close js-pm-thanks-close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <div class="k-modal__body">
                    <p id="pmThanksText">
                        Hemos recibido tu pedido. Te avisaremos cuando esté listo.
                    </p>
                </div>
                <div class="k-modal__footer">
                    <button type="button" class="k-btn-primary js-pm-thanks-close">
                        Entendido
                    </button>
                </div>
            </div>
        </div>

        {{-- JS del menú público --}}
        <script src="{{ asset('js/public-menu.js') }}"></script>
    </body>

</html>
