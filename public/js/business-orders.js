// public/js/business-orders.js
document.addEventListener("DOMContentLoaded", () => {
    // ---------- Config ----------
    const cfgEl = document.getElementById("kOrdersConfig");
    const completeUrlTpl = cfgEl ? cfgEl.dataset.completeUrlTemplate : "";

    const searchInput = document.getElementById("kOrdersSearch");
    const rows = Array.from(document.querySelectorAll(".js-order-row"));

    // Panel detalle
    const panelEmpty = document.getElementById("kOrderDetailEmpty");
    const panelBody = document.getElementById("kOrderDetailBody");

    const elFolio = document.getElementById("od_folio");
    const elMesa = document.getElementById("od_mesa");
    const elSourceBadge = document.getElementById("od_source_badge");
    const elTypeLabel = document.getElementById("od_type_label");
    const elFecha = document.getElementById("od_fecha");
    const elEstado = document.getElementById("od_estado");
    const elTotalHeader = document.getElementById("od_total");
    const elItems = document.getElementById("od_items");
    const elSubtotal = document.getElementById("od_subtotal");
    const elTotalFooter = document.getElementById("od_total_footer");
    const btnComplete = document.getElementById("kOrderCompleteBtn");

    // Modales
    const modalConfirm = document.getElementById("modalConfirmCompleteOrder");
    const modalMsg = document.getElementById("modalOrdersMessage");

    const confirmText = document.getElementById("kCompleteConfirmText");
    const msgTitle = document.getElementById("kMsgTitle");
    const msgBody = document.getElementById("kMsgBody");
    const msgWhatsappBtn = document.getElementById("kMsgWhatsappBtn");

    let selectedOrderId = null;
    let selectedRow = null;
    let pendingCompleteId = null;

    function formatMoney(v) {
        const num = Number(v || 0);
        return "$" + num.toFixed(2);
    }

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add("k-modal--open");
        document.body.classList.add("k-no-scroll");
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove("k-modal--open");
        // si no hay más modales abiertos, quitamos el no-scroll
        const anyOpen = document.querySelector(".k-modal.k-modal--open");
        if (!anyOpen) document.body.classList.remove("k-no-scroll");
    }

    // Cerrar modales de confirmación
    modalConfirm
        ?.querySelectorAll(".js-modal-close, .js-complete-cancel")
        .forEach((btn) =>
            btn.addEventListener("click", () => closeModal(modalConfirm))
        );

    // Cerrar modal de mensaje
    modalMsg
        ?.querySelectorAll(".js-modal-msg-close")
        .forEach((btn) =>
            btn.addEventListener("click", () => closeModal(modalMsg))
        );

    // ---------- Render detalle ----------
    function renderDetailFromRow(row) {
        if (!row) return;

        const {
            orderId,
            status,
            statusLabel,
            folio,
            mesa,
            fecha,
            total,
            subtotal,
            items: itemsStr,
            source,
            orderType,
        } = row.dataset;

        selectedOrderId = orderId || null;
        selectedRow = row;

        // Cambiar highlight en la lista
        rows.forEach((r) => r.classList.remove("is-active"));
        row.classList.add("is-active");

        // Mostrar panel
        if (panelEmpty) panelEmpty.hidden = true;
        if (panelBody) panelBody.hidden = false;

        // Header
        if (elFolio) elFolio.textContent = folio || "";
        if (elMesa) elMesa.textContent = mesa || "";
        if (elFecha) elFecha.textContent = fecha || "";
        if (elTotalHeader) elTotalHeader.textContent = total || "";

        // Origen (QR / Mostrador)
        if (elSourceBadge) {
            elSourceBadge.style.display = "none";
            elSourceBadge.classList.remove(
                "k-order-badge--counter",
                "k-order-badge--qr"
            );

            if (source === "counter") {
                elSourceBadge.textContent = "Mostrador";
                elSourceBadge.classList.add("k-order-badge--counter");
                elSourceBadge.style.display = "inline-flex";
            } else if (source === "qr") {
                elSourceBadge.textContent = "QR";
                elSourceBadge.classList.add("k-order-badge--qr");
                elSourceBadge.style.display = "inline-flex";
            }
        }

        // Tipo (para aquí / para llevar)
        if (elTypeLabel) {
            elTypeLabel.textContent = "";
            if (orderType === "dine_in") {
                elTypeLabel.textContent = "Para consumir aquí";
            } else if (orderType === "take_away") {
                elTypeLabel.textContent = "Para llevar";
            }
        }

        // Pill estado
        if (elEstado) elEstado.textContent = statusLabel || status || "";

        // Items
        if (elItems) {
            elItems.innerHTML = "";
            let items = [];
            try {
                items = itemsStr ? JSON.parse(itemsStr) : [];
            } catch (e) {
                console.error("Error parseando items", e);
            }

            if (!items || !items.length) {
                const div = document.createElement("div");
                div.className = "k-order-item";
                div.textContent = "Sin productos en este pedido.";
                elItems.appendChild(div);
            } else {
                items.forEach((item) => {
                    const wrapper = document.createElement("div");
                    wrapper.className = "k-order-item";

                    const main = document.createElement("div");
                    main.className = "k-order-item-main";

                    const nameEl = document.createElement("div");
                    nameEl.className = "k-order-item-name";
                    nameEl.textContent = item.name || "Producto";

                    const noteEl = document.createElement("div");
                    noteEl.className = "k-order-item-note";
                    noteEl.textContent = item.note || "";

                    main.appendChild(nameEl);
                    if (item.note) main.appendChild(noteEl);

                    const meta = document.createElement("div");
                    meta.className = "k-order-item-meta";

                    const qtyEl = document.createElement("div");
                    qtyEl.className = "k-order-item-qty";
                    qtyEl.textContent = "x" + (item.qty || 1);

                    const priceEl = document.createElement("div");
                    priceEl.className = "k-order-item-price";
                    priceEl.textContent = formatMoney(item.price);

                    const totalEl = document.createElement("div");
                    totalEl.className = "k-order-item-total";
                    totalEl.textContent = formatMoney(item.total);

                    meta.appendChild(qtyEl);
                    meta.appendChild(priceEl);
                    meta.appendChild(totalEl);

                    wrapper.appendChild(main);
                    wrapper.appendChild(meta);

                    elItems.appendChild(wrapper);
                });
            }
        }

        // Resumen
        if (elSubtotal) elSubtotal.textContent = formatMoney(subtotal);
        if (elTotalFooter)
            elTotalFooter.textContent = total || formatMoney(subtotal);

        // Botón completar
        if (btnComplete) {
            if (status === "completed") {
                btnComplete.disabled = true;
                btnComplete.textContent = "Pedido completado";
                btnComplete.classList.add("k-btn-disabled");
            } else {
                btnComplete.disabled = false;
                btnComplete.textContent = "Marcar como pagado / completar";
                btnComplete.classList.remove("k-btn-disabled");
            }
            btnComplete.dataset.status = status;
        }
    }

    // ---------- Listeners en filas ----------
    rows.forEach((row) => {
        row.addEventListener("click", () => {
            renderDetailFromRow(row);
        });
    });

    // ---------- Filtro por texto ----------
    if (searchInput) {
        searchInput.addEventListener("input", () => {
            const q = searchInput.value.trim().toLowerCase();

            rows.forEach((row) => {
                const txt = (row.dataset.search || "").toLowerCase();
                row.style.display = !q || txt.includes(q) ? "" : "none";
            });
        });
    }

    // ---------- Completar pedido ----------
    function buildCompleteUrl(orderId) {
        if (!completeUrlTpl) return "";
        return completeUrlTpl.replace("__ID__", String(orderId));
    }

    // Abrir modal de confirmación
    btnComplete?.addEventListener("click", () => {
        if (!selectedOrderId) {
            // mensaje tipo modal
            msgTitle.textContent = "Aviso";
            msgBody.textContent = "Selecciona primero un pedido de la lista.";
            msgWhatsappBtn.style.display = "none";
            openModal(modalMsg);
            return;
        }

        const status = btnComplete.dataset.status;
        if (status === "completed") {
            msgTitle.textContent = "Aviso";
            msgBody.textContent =
                "Este pedido ya está marcado como completado.";
            msgWhatsappBtn.style.display = "none";
            openModal(modalMsg);
            return;
        }

        confirmText.textContent = `¿Marcar el pedido ${
            elFolio?.textContent || ""
        } como pagado/completado?`;
        pendingCompleteId = selectedOrderId;
        openModal(modalConfirm);
    });

    // Confirmar completar (llamada al backend)
    modalConfirm
        ?.querySelector(".js-complete-confirm")
        ?.addEventListener("click", async () => {
            if (!pendingCompleteId) {
                closeModal(modalConfirm);
                return;
            }

            const url = buildCompleteUrl(pendingCompleteId);
            if (!url) {
                closeModal(modalConfirm);
                msgTitle.textContent = "Error";
                msgBody.textContent =
                    "No se encontró la URL para completar el pedido.";
                msgWhatsappBtn.style.display = "none";
                openModal(modalMsg);
                return;
            }

            try {
                const resp = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                        Accept: "application/json",
                    },
                });

                const data = await resp.json().catch(() => null);

                closeModal(modalConfirm);

                if (!resp.ok || !data || !data.ok) {
                    console.error("Respuesta no OK al completar pedido", data);
                    msgTitle.textContent = "Error";
                    msgBody.textContent =
                        data?.message ||
                        "No se pudo marcar el pedido como completado.";
                    msgWhatsappBtn.style.display = "none";
                    openModal(modalMsg);
                    return;
                }

                // Actualizar fila y panel
                if (
                    selectedRow &&
                    String(selectedRow.dataset.orderId) ===
                        String(pendingCompleteId)
                ) {
                    selectedRow.dataset.status = "completed";
                    selectedRow.dataset.statusLabel =
                        data.order?.status_label || "Completed";

                    const statusSpan =
                        selectedRow.querySelector(".k-order-status");
                    if (statusSpan) {
                        statusSpan.textContent =
                            data.order?.status_label || "Completed";
                    }

                    renderDetailFromRow(selectedRow);
                }

                // ==== NUEVO: refrescar lista cuando se completa un pedido ====
                // Vemos en qué tab estamos (pending / completed) según el query string
                const currentStatusParam =
                    new URL(window.location.href).searchParams.get("status") ||
                    "pending";

                // Si estamos en "En proceso" (pending),
                // quitamos la fila de la lista y actualizamos contador y mensaje vacío.
                if (currentStatusParam === "pending") {
                    const listContainer =
                        document.querySelector(".k-orders-list");

                    if (selectedRow && listContainer) {
                        selectedRow.remove();
                        selectedRow = null;
                        selectedOrderId = null;
                    }

                    // Actualizar contador "Total: X pedidos"
                    const remainingRows = listContainer
                        ? listContainer.querySelectorAll(".js-order-row").length
                        : 0;

                    const metaEl = document.querySelector(".k-table-meta");
                    if (metaEl) {
                        metaEl.textContent = `Total: ${remainingRows} pedidos`;
                    }

                    // Mostrar/ocultar mensaje vacío
                    let emptyDiv = document.querySelector(".k-orders-empty");
                    if (remainingRows === 0) {
                        if (!emptyDiv && listContainer) {
                            emptyDiv = document.createElement("div");
                            emptyDiv.className = "k-orders-empty";
                            emptyDiv.textContent =
                                "No tienes pedidos en este estado todavía.";
                            listContainer.appendChild(emptyDiv);
                        } else if (emptyDiv) {
                            emptyDiv.style.display = "block";
                        }
                    } else if (emptyDiv) {
                        emptyDiv.style.display = "none";
                    }

                    // Resetear panel de detalle
                    if (panelBody) panelBody.hidden = true;
                    if (panelEmpty) panelEmpty.hidden = false;
                }

                // Configurar modal de éxito
                msgTitle.textContent = "Pedido completado";
                msgBody.textContent =
                    data.message || "El pedido se marcó como completado.";

                if (data.whatsapp_url) {
                    msgWhatsappBtn.href = data.whatsapp_url;
                    msgWhatsappBtn.style.display = "inline-flex";
                } else {
                    msgWhatsappBtn.style.display = "none";
                }

                openModal(modalMsg);
            } catch (e) {
                console.error("Error al completar pedido", e);
                closeModal(modalConfirm);
                msgTitle.textContent = "Error";
                msgBody.textContent =
                    "Ocurrió un error al marcar el pedido como completado.";
                msgWhatsappBtn.style.display = "none";
                openModal(modalMsg);
            } finally {
                pendingCompleteId = null;
            }
        });

    // ---------- Selección automática de la primera fila ----------
    if (rows.length > 0) {
        renderDetailFromRow(rows[0]);
    } else {
        if (panelBody) panelBody.style.display = "none";
    }

    // ---------- Filtro funcional de fecha ----------
    const dateInput = document.getElementById("kOrdersDate");
    const dateBtn = document.getElementById("kOrdersDateFilterBtn");
    if (dateBtn && dateInput) {
        dateBtn.addEventListener("click", () => {
            const dateVal = dateInput.value;
            const url = new URL(window.location.href);
            if (dateVal) {
                url.searchParams.set("date", dateVal);
            } else {
                url.searchParams.delete("date");
            }
            window.location.href = url.toString();
        });
    }
});

