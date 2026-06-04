document.addEventListener("DOMContentLoaded", () => {
    // ======================================================
    // ============== CONFIG GLOBAL DESDE BLADE =============
    // ======================================================

    const configEl = document.getElementById("pmConfig");
    const ORDER_ENDPOINT = configEl?.dataset.orderEndpoint || null;
    const BUSINESS_ID = configEl?.dataset.businessId || null;
    const TABLE_ID = configEl?.dataset.tableId || null;

    // ====== Scroll a categoría al tocar el pill ======
    document.querySelectorAll(".pm-cat-pill").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.cat;
            const block = document.querySelector(
                `[data-category-block="${id}"]`
            );
            if (block) {
                block.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        });
    });

    // Body.scrolled -> sombra en tabs
    document.addEventListener(
        "scroll",
        () => {
            if (window.scrollY > 10) {
                document.body.classList.add("scrolled");
            } else {
                document.body.classList.remove("scrolled");
            }
        },
        { passive: true }
    );

    // ====== Filtro rápido por nombre ======
    const searchInput = document.getElementById("pmSearchInput");
    if (searchInput) {
        searchInput.addEventListener("input", () => {
            const q = searchInput.value.trim().toLowerCase();
            document
                .querySelectorAll("[data-category-block]")
                .forEach((section) => {
                    let someVisible = false;
                    section
                        .querySelectorAll("[data-product-name]")
                        .forEach((card) => {
                            const name = card.dataset.productName;
                            const match = !q || name.includes(q);
                            card.style.display = match ? "" : "none";
                            if (match) someVisible = true;
                        });
                    section.style.display = someVisible ? "" : "none";
                });
        });
    }

    // ======================================================
    // ==============   CARRITO + LOCALSTORAGE   ============
    // ======================================================

    const CART_KEY = "kuali_cart_v1";
    const TICKET_KEY = "kuali_last_ticket_v1";

    // Header
    const cartBadge = document.getElementById("pmCartBadge");
    const cartButton = document.getElementById("pmCartButton");

    // Pantalla carrito
    const cartScreen = document.getElementById("pm-cart-screen");
    const cartListEl = document.getElementById("pm-cart-list");
    const cartSubtotalEl = document.getElementById("pm-cart-subtotal");
    const cartTotalSummaryEl = document.getElementById("pm-cart-total-summary");
    const cartCloseBtn = document.getElementById("pm-cart-close");
    const cartSendBtn = document.getElementById("pm-cart-send");

    // Datos del cliente (modal)
    const customerModal = document.getElementById("pmCustomerModal");
    const customerNameInput = document.getElementById("pmCustomerName");
    const customerPhoneInput = document.getElementById("pmCustomerPhone");
    const customerError = document.getElementById("pmCustomerError");
    const customerConfirmBtn = document.getElementById("pmCustomerConfirmBtn");
    const customerCloseEls = document.querySelectorAll(".js-pm-close-modal");

    // Modal gracias
    const thanksModal = document.getElementById("pmThanksModal");
    const thanksCloseEls = document.querySelectorAll(".js-pm-thanks-close");
    const thanksText = document.getElementById("pmThanksText");

    // Modal carrito vacío
    const emptyCartModal = document.getElementById("pm-empty-cart");
    const emptyCartCloseBtn = document.getElementById("pm-empty-close");

    // Ticket local (por compatibilidad)
    const ticketWrap = document.getElementById("pm-ticket");
    const ticketIdEl = document.getElementById("pm-ticket-id");
    const ticketCloseBtn = document.getElementById("pm-ticket-close");

    function formatMoney(v) {
        return "$" + Number(v).toFixed(2);
    }

    function getCart() {
        try {
            const raw = localStorage.getItem(CART_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        renderCartUI();
    }

    function clearCart() {
        localStorage.removeItem(CART_KEY);
        renderCartUI();
    }

    function calcTotals(cart) {
        let qty = 0;
        let subtotal = 0;
        cart.forEach((i) => {
            qty += i.qty;
            subtotal += i.qty * i.price;
        });
        return { qty, subtotal };
    }

    function showEmptyCartModal() {
        if (emptyCartModal) emptyCartModal.hidden = false;
    }

    function renderCartUI() {
        const cart = getCart();
        const { qty, subtotal } = calcTotals(cart);

        // Badge del header
        if (qty > 0) {
            cartBadge.style.display = "flex";
            cartBadge.textContent = String(qty);
            
            // Pulse Pop animation feedback
            if (cartButton) {
                cartButton.classList.remove("pm-pulse-pop");
                void cartButton.offsetWidth; // trigger reflow
                cartButton.classList.add("pm-pulse-pop");
            }
        } else {
            cartBadge.style.display = "none";
        }

        if (!cartListEl || !cartSubtotalEl || !cartTotalSummaryEl) return;

        // Contenido de la pantalla de carrito
        cartListEl.innerHTML = "";
        cart.forEach((item, idx) => {
            const row = document.createElement("div");
            row.className = "pm-cart-item";

            const left = document.createElement("div");
            left.className = "pm-cart-item-left";

            // mini imagen
            if (item.image) {
                const thumb = document.createElement("img");
                thumb.className = "pm-cart-thumb";
                thumb.src = item.image;
                thumb.alt = item.name;
                left.appendChild(thumb);
            }

            const textWrap = document.createElement("div");
            textWrap.className = "pm-cart-text";

            const nameEl = document.createElement("div");
            nameEl.className = "pm-cart-item-name";
            nameEl.textContent = item.name;
            textWrap.appendChild(nameEl);

            if (item.note) {
                const noteEl = document.createElement("div");
                noteEl.className = "pm-cart-item-note";
                noteEl.textContent = item.note;
                textWrap.appendChild(noteEl);
            }

            const priceEl = document.createElement("div");
            priceEl.className = "pm-cart-item-price";
            priceEl.textContent = formatMoney(item.price);
            textWrap.appendChild(priceEl);

            left.appendChild(textWrap);

            const qtyWrap = document.createElement("div");
            qtyWrap.className = "pm-cart-item-qty";
            qtyWrap.dataset.cartIndex = String(idx);

            const minusBtn = document.createElement("button");
            minusBtn.type = "button";
            minusBtn.className = "pm-qty-btn pm-qty-minus";
            minusBtn.textContent = "−";

            const qtyValue = document.createElement("span");
            qtyValue.className = "pm-cart-item-qty-value";
            qtyValue.textContent = String(item.qty);

            const plusBtn = document.createElement("button");
            plusBtn.type = "button";
            plusBtn.className = "pm-qty-btn pm-qty-plus";
            plusBtn.textContent = "+";

            qtyWrap.appendChild(minusBtn);
            qtyWrap.appendChild(qtyValue);
            qtyWrap.appendChild(plusBtn);

            row.appendChild(left);
            row.appendChild(qtyWrap);
            cartListEl.appendChild(row);
        });

        cartSubtotalEl.textContent = formatMoney(subtotal);
        cartTotalSummaryEl.textContent = formatMoney(subtotal); // de momento total=subtotal
    }

    function addToCart(product, qty, note) {
        let cart = getCart();
        const existing = cart.find(
            (i) =>
                i.id === product.id &&
                (i.note || "") === (note || "")
        );

        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image || "",
                qty,
                note: note || "",
            });
        }
        saveCart(cart);
    }

    function updateCartItemQty(index, delta) {
        let cart = getCart();
        if (index < 0 || index >= cart.length) return;
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
        saveCart(cart);
    }

    function openCartScreen() {
        const cart = getCart();
        if (!cart.length) {
            showEmptyCartModal();
            return;
        }
        if (cartScreen) {
            cartScreen.setAttribute("aria-hidden", "false");
            document.body.classList.add("pm-no-scroll");
        }
    }

    function closeCartScreen() {
        if (cartScreen) {
            cartScreen.setAttribute("aria-hidden", "true");
            document.body.classList.remove("pm-no-scroll");
        }
    }

    // Helpers modales k-modal
    function openKModal(modal) {
        if (!modal) return;
        modal.hidden = false;
        modal.classList.add("k-modal--open");
        document.body.classList.add("k-no-scroll");
    }

    function closeKModal(modal) {
        if (!modal) return;
        modal.classList.remove("k-modal--open");
        modal.hidden = true;
        const anyOpen = document.querySelector(".k-modal.k-modal--open");
        if (!anyOpen) document.body.classList.remove("k-no-scroll");
    }

    // Abrir carrito desde el header
    if (cartButton) {
        cartButton.addEventListener("click", openCartScreen);
    }

    // Cerrar carrito
    if (cartCloseBtn) {
        cartCloseBtn.addEventListener("click", closeCartScreen);
    }
    if (cartScreen) {
        cartScreen.addEventListener("click", (e) => {
            if (e.target === cartScreen) {
                closeCartScreen();
            }
        });
    }

    // Cambios de cantidad dentro del carrito
    if (cartListEl) {
        cartListEl.addEventListener("click", (e) => {
            const btn = e.target.closest(".pm-qty-btn");
            if (!btn) return;
            const wrap = btn.closest("[data-cart-index]");
            if (!wrap) return;
            const index = parseInt(wrap.dataset.cartIndex, 10);
            if (btn.classList.contains("pm-qty-plus")) {
                updateCartItemQty(index, 1);
            } else if (btn.classList.contains("pm-qty-minus")) {
                updateCartItemQty(index, -1);
            }
        });
    }

    // ======================================================
    // ============== ENVIAR PEDIDO (CON MODAL) =============
    // ======================================================

    function openCustomerModal() {
        const cart = getCart();
        if (!cart.length) {
            showEmptyCartModal();
            return;
        }

        if (customerError) {
            customerError.textContent = "";
            customerError.style.display = "none";
        }

        if (customerNameInput && !customerNameInput.value) {
            customerNameInput.value = "";
        }

        openKModal(customerModal);
    }

    // Click en "Enviar pedido" del carrito -> abre modal de datos
    if (cartSendBtn) {
        cartSendBtn.addEventListener("click", () => {
            openCustomerModal();
        });
    }

    // Cerrar modal de datos
    customerCloseEls.forEach((btn) => {
        btn.addEventListener("click", () => closeKModal(customerModal));
    });

    // Confirmar datos y mandar pedido
    if (customerConfirmBtn) {
        customerConfirmBtn.addEventListener("click", () => {
            const name = (customerNameInput?.value || "").trim();
            const phone = (customerPhoneInput?.value || "").trim();

            if (!name || !phone) {
                if (customerError) {
                    customerError.textContent =
                        "Por favor ingresa tu nombre y teléfono para continuar.";
                    customerError.style.display = "block";
                } else {
                    alert(
                        "Por favor ingresa tu nombre y teléfono para continuar."
                    );
                }
                return;
            }

            if (customerError) {
                customerError.textContent = "";
                customerError.style.display = "none";
            }

            closeKModal(customerModal);
            sendOrderToServer(name, phone);
        });
    }

    // Modal Gracias
    thanksCloseEls.forEach((btn) => {
        btn.addEventListener("click", () => closeKModal(thanksModal));
    });

    // ================== ENVIAR PEDIDO AL SERVER =====================

    async function sendOrderToServer(customerName, customerPhone) {
        const cart = getCart();
        if (!cart.length) {
            showEmptyCartModal();
            return;
        }

        const { subtotal } = calcTotals(cart);

        // Si no tenemos endpoint o business, usamos flujo local anterior
        if (!ORDER_ENDPOINT || !BUSINESS_ID) {
            const now = new Date();
            const ticketId =
                "K-" +
                now.getFullYear() +
                (now.getMonth() + 1).toString().padStart(2, "0") +
                now.getDate().toString().padStart(2, "0") +
                "-" +
                now.getTime().toString().slice(-4);

            const ticket = {
                id: ticketId,
                items: cart,
                subtotal,
                createdAt: now.toISOString(),
            };

            localStorage.setItem(TICKET_KEY, JSON.stringify(ticket));
            clearCart();
            closeCartScreen();

            if (ticketIdEl && ticketWrap) {
                ticketIdEl.textContent = ticketId;
                ticketWrap.hidden = false;
            }
            return;
        }

        // Flujo real: mandar a Laravel
        const payload = {
            business_id: parseInt(BUSINESS_ID, 10),
            table_id: TABLE_ID ? parseInt(TABLE_ID, 10) : null,
            customer_name: customerName,
            customer_phone: customerPhone,
            notes: null,
            subtotal: subtotal,
            discount: 0,
            total: subtotal,
            items: cart.map((it) => ({
                product_id: it.id,
                name: it.name,
                qty: it.qty,
                unit_price: it.price,
                total: it.price * it.qty,
                notes: it.note || null,
            })),
        };

    
        if (cartSendBtn) cartSendBtn.disabled = true;

        try {
            const resp = await fetch(ORDER_ENDPOINT, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(payload),
            });

            const data = await resp.json().catch(() => ({}));

            if (!resp.ok || !data.ok) {
                console.error("Error al guardar pedido", data);
                alert(
                    "Ocurrió un error al enviar tu pedido. Intenta nuevamente."
                );
                return;
            }

            const orderId = data.order_id;
            const totalFromServer = data.total ?? subtotal;

            const now = new Date();
            const ticket = {
                id: "ORD-" + orderId,
                orderId,
                items: cart,
                subtotal,
                total: totalFromServer,
                createdAt: now.toISOString(),
            };

            localStorage.setItem(TICKET_KEY, JSON.stringify(ticket));
            clearCart();
            closeCartScreen();

            // Mostrar modal de gracias
            if (thanksText) {
                thanksText.textContent =
                    "Hemos recibido tu pedido. Te avisaremos cuando esté listo.";
            }
            openKModal(thanksModal);
        } catch (err) {
            console.error(err);
            alert(
                "No pudimos conectar con el servidor. Revisa tu conexión e inténtalo de nuevo."
            );
        } finally {
            if (cartSendBtn) cartSendBtn.disabled = false;
        }
    }

    // Modal carrito vacío
    if (emptyCartCloseBtn && emptyCartModal) {
        emptyCartCloseBtn.addEventListener("click", () => {
            emptyCartModal.hidden = true;
        });
    }

    // Ticket local
    if (ticketCloseBtn && ticketWrap) {
        ticketCloseBtn.addEventListener("click", () => {
            ticketWrap.hidden = true;
        });
    }

    // ======================================================
    // ============== PANTALLA DETALLE PRODUCTO =============
    // ======================================================

    let currentProduct = null;

    const modal = document.getElementById("pmProductModal");
    const modalTitle = document.getElementById("pmModalTitle");
    const modalPriceTop = document.getElementById("pmModalPriceTop");
    const modalImg = document.getElementById("pmModalImage");
    const modalDesc = document.getElementById("pmModalDescription");
    const modalQtyInput = document.getElementById("pmModalQty");
    const qtyDisplay = document.getElementById("pmStepperValue");
    const modalNote = document.getElementById("pmModalNote");
    const modalTotal = document.getElementById("pmModalTotal");
    const qtyButtons = document.querySelectorAll(".pm-stepper-btn");

    function sanitizeQty(value) {
        const raw = parseInt(value, 10);
        if (isNaN(raw) || raw < 1) return 1;
        if (raw > 99) return 99;
        return raw;
    }

    function recalcModalTotal() {
        if (!currentProduct) return;
        const qty = sanitizeQty(modalQtyInput.value || "1");
        modalTotal.textContent = formatMoney(qty * currentProduct.price);
    }

    function updateQtyDisplay(q) {
        const safe = sanitizeQty(q);
        modalQtyInput.value = String(safe);
        if (qtyDisplay) qtyDisplay.textContent = String(safe);
        recalcModalTotal();
    }

    qtyButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const action = btn.dataset.action;
            let current = sanitizeQty(modalQtyInput.value || "1");

            if (action === "minus" && current > 1) current--;
            if (action === "plus") current++;

            updateQtyDisplay(current);
        });
    });

    if (modalNote) {
        modalNote.addEventListener("focus", () => {
            setTimeout(() => {
                modalNote.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
            }, 200);
        });
    }

    function openModal(product) {
        currentProduct = product;
        modalTitle.textContent = product.name;
        modalDesc.textContent = product.description || "";

        if (modalPriceTop) {
            modalPriceTop.textContent = formatMoney(product.price);
        }

        if (product.image) {
            modalImg.src = product.image;
            modalImg.style.display = "block";
        } else {
            modalImg.style.display = "none";
        }

        updateQtyDisplay(1);
        modalNote.value = "";
        recalcModalTotal();

        modal.classList.add("pm-modal--open");
    }

    function closeModal() {
        modal.classList.remove("pm-modal--open");
        currentProduct = null;
    }

    document.querySelectorAll(".js-add-product").forEach((btn) => {
        btn.addEventListener("click", () => {
            const product = {
                id: parseInt(btn.dataset.id, 10),
                name: btn.dataset.name,
                price: parseFloat(btn.dataset.price),
                description: btn.dataset.description || "",
                image: btn.dataset.image || "",
            };
            openModal(product);
        });
    });

    document.querySelectorAll("[data-pm-close]").forEach((el) => {
        el.addEventListener("click", closeModal);
    });

    const addToCartBtn = document.getElementById("pmAddToCartBtn");
    if (addToCartBtn) {
        addToCartBtn.addEventListener("click", () => {
            if (!currentProduct) return;

            const qty = sanitizeQty(modalQtyInput.value || "1");
            const note = modalNote.value.trim();

            addToCart(
                {
                    id: currentProduct.id,
                    name: currentProduct.name,
                    price: currentProduct.price,
                    image: currentProduct.image || "",
                },
                qty,
                note
            );

            closeModal();
        });
    }

    // Al cargar, pintar badge / carrito si había algo guardado
    renderCartUI();
});
