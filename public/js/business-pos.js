// public/js/business-pos.js
document.addEventListener('DOMContentLoaded', () => {
    const cfgEl = document.getElementById('kPosConfig');
    if (!cfgEl) return;

    const storeUrl = cfgEl.dataset.storeUrl;

    const cartItemsContainer = document.getElementById('posCartItems');
    const cartTotalEl        = document.getElementById('posCartTotal');
    const cartCountEl        = document.getElementById('posItemsCount');

    const tableInput         = document.getElementById('posTableName');
    const orderTypeSelect    = document.getElementById('posOrderType');
    const submitBtn          = document.getElementById('posCreateOrderBtn');

    const productButtons = Array.from(document.querySelectorAll('.js-pos-add'));

    let cart = [];

    function formatMoney(v) {
        const n = Number(v || 0);
        return '$' + n.toFixed(2);
    }

    function findItemIndex(productId) {
        return cart.findIndex(i => i.product_id === productId);
    }

    function addToCart(product) {
        const idx = findItemIndex(product.product_id);
        if (idx >= 0) {
            cart[idx].qty += 1;
        } else {
            cart.push({ ...product, qty: 1 });
        }
        renderCart();
    }

    function changeQty(productId, delta) {
        const idx = findItemIndex(productId);
        if (idx === -1) return;
        cart[idx].qty += delta;
        if (cart[idx].qty <= 0) {
            cart.splice(idx, 1);
        }
        renderCart();
    }

    function renderCart() {
        cartItemsContainer.innerHTML = '';

        if (cart.length === 0) {
            cartItemsContainer.innerHTML =
                '<div class="k-pos-cart-empty">No hay productos en el pedido.</div>';
        } else {
            cart.forEach(item => {
                const row = document.createElement('div');
                row.className = 'k-pos-cart-item';

                row.innerHTML = `
                    <div class="k-pos-cart-item-main">
                        <div class="k-pos-cart-item-name">${item.name}</div>
                        <div class="k-pos-cart-item-price">${formatMoney(item.price)}</div>
                    </div>
                    <div class="k-pos-cart-item-qty">
                        <button type="button" class="k-pos-qty-btn" data-id="${item.product_id}" data-delta="-1">−</button>
                        <span>${item.qty}</span>
                        <button type="button" class="k-pos-qty-btn" data-id="${item.product_id}" data-delta="1">+</button>
                    </div>
                    <div class="k-pos-cart-item-total">
                        ${formatMoney(item.price * item.qty)}
                    </div>
                `;

                cartItemsContainer.appendChild(row);
            });
        }

        const total = cart.reduce((acc, i) => acc + i.price * i.qty, 0);
        const count = cart.reduce((acc, i) => acc + i.qty, 0);

        cartTotalEl.textContent = formatMoney(total);
        cartCountEl.textContent = `${count} producto${count === 1 ? '' : 's'}`;

        // wire botones de cantidad
        cartItemsContainer
            .querySelectorAll('.k-pos-qty-btn')
            .forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = Number(btn.dataset.id);
                    const delta = Number(btn.dataset.delta);
                    changeQty(id, delta);
                });
            });
    }

    // Click en productos
    productButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const product = {
                product_id: Number(btn.dataset.productId),
                name: btn.dataset.productName,
                price: Number(btn.dataset.productPrice),
                note: null,
            };
            addToCart(product);
        });
    });

    // Crear pedido
    submitBtn.addEventListener('click', async () => {
        if (cart.length === 0) {
            alert('Agrega al menos un producto al pedido.');
            return;
        }

        const payload = {
            items: cart.map(i => ({
                product_id: i.product_id,
                name: i.name,
                qty: i.qty,
                price: i.price,
                note: i.note || null,
            })),
            table_name: tableInput.value || 'Mostrador',
            order_type: orderTypeSelect.value || null,
        };

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';

            const resp = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await resp.json();

            if (!resp.ok || !data.ok) {
                console.error(data);
                alert(data.message || 'No se pudo crear el pedido.');
                return;
            }

            // limpiar carrito
            cart = [];
            renderCart();

            // redirigir a lista de pedidos pending
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert('Pedido creado correctamente.');
            }
        } catch (e) {
            console.error(e);
            alert('Error al crear el pedido.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Crear pedido';
        }
    });

    // render inicial
    renderCart();
});
