// public/js/business-products.js
document.addEventListener("DOMContentLoaded", () => {
    const modalCreate = document.getElementById("modalCreateProduct");
    const modalEdit = document.getElementById("modalEditProduct");
    const modalDelete = document.getElementById("modalConfirmDeleteProduct");

    if (!modalCreate && !modalEdit) {
        // este JS sólo aplica en la pantalla de productos
        return;
    }

    function openModal(m) {
        if (m) m.classList.add("k-modal--open");
    }
    function closeModal(m) {
        if (m) m.classList.remove("k-modal--open");
    }

    // cerrar por .js-close-modal
    document.querySelectorAll(".js-close-modal").forEach((btn) => {
        btn.addEventListener("click", () => {
            [modalCreate, modalEdit, modalDelete].forEach(closeModal);
        });
    });

    // abrir NUEVO
    const btnOpenCreate = document.getElementById("btnOpenCreateProduct");
    if (btnOpenCreate && modalCreate) {
        btnOpenCreate.addEventListener("click", () => openModal(modalCreate));
    }

    // EDITAR
    const editButtons = document.querySelectorAll(".js-edit-product");
    const formCreate = document.getElementById("formCreateProduct");
    const formEdit = document.getElementById("formEditProduct");
    const imagePreview = document.getElementById("productImagePreview");

    editButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            if (!formEdit) return;

            const id = btn.dataset.id;
            formEdit.action = `/products/${id}`;

            formEdit.querySelector('[name="name"]').value =
                btn.dataset.name || "";
            formEdit.querySelector('[name="category_id"]').value =
                btn.dataset.categoryId || "";
            formEdit.querySelector('[name="price"]').value =
                btn.dataset.price || "";
            formEdit.querySelector('[name="description"]').value =
                btn.dataset.description || "";

            const chk = formEdit.querySelector("#edit_product_is_active");
            if (chk) {
                chk.checked = btn.dataset.active === "1";
            }

            // preview de imagen
            if (imagePreview) {
                const imageUrl = btn.dataset.imageUrl || "";
                if (imageUrl) {
                    imagePreview.src = imageUrl;
                    imagePreview.style.display = "block";
                } else {
                    imagePreview.src = "";
                    imagePreview.style.display = "none";
                }
            }

            // variantes de precio
            const editWrap = document.getElementById("editVariantsWrap");
            const editToggle = document.getElementById("edit_variants_toggle");
            if (editWrap && editToggle) {
                let variants = [];
                try {
                    variants = JSON.parse(btn.dataset.variants || "[]");
                } catch (e) {
                    variants = [];
                }

                fillVariantRows(editWrap, variants);
                editToggle.checked = variants.length > 0;
                editWrap.style.display = variants.length > 0 ? "flex" : "none";
            }

            openModal(modalEdit);
        });
    });

    function attachClientValidation(form) {
        if (!form) return;

        form.addEventListener("submit", (e) => {
            // limpiamos estados previos
            form.querySelectorAll(".k-input--error").forEach(el => {
                el.classList.remove("k-input--error");
            });

            let firstInvalid = null;

            form.querySelectorAll("[required]").forEach((input) => {
                if (!input.value || input.value.trim() === "") {
                    input.classList.add("k-input--error");
                    if (!firstInvalid) firstInvalid = input;
                }
            });

            const price = form.querySelector('input[name="price"]');
            if (price && price.value && Number(price.value) < 0) {
                price.classList.add("k-input--error");
                firstInvalid = firstInvalid || price;
            }

            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.focus();
            }
        });
    }

    attachClientValidation(formCreate);
    attachClientValidation(formEdit);

    // =============================
    // Variantes de precio (tamaños)
    // =============================
    function makeVariantRow(name = "", price = "") {
        const row = document.createElement("div");
        row.className = "js-variant-row";
        row.style.cssText = "display:flex; gap:0.5rem; align-items:center;";
        row.innerHTML = `
            <input type="text" class="k-input" name="variants[][name]" placeholder="Nombre (ej. Chica)" value="${name.replace(/"/g, '&quot;')}"
                style="flex:1; height:38px; border-radius:10px; border:1px solid #d1d5db; padding:0.4rem 0.7rem; font-size:0.85rem; outline:none;">
            <input type="number" step="0.01" min="0" class="k-input" name="variants[][price]" placeholder="Precio" value="${price}"
                style="width:110px; height:38px; border-radius:10px; border:1px solid #d1d5db; padding:0.4rem 0.7rem; font-size:0.85rem; outline:none;">
            <button type="button" class="js-remove-variant-row" style="width:32px; height:32px; border-radius:50%; border:none; background:#fef2f2; color:#dc2626; cursor:pointer; flex-shrink:0;">
                <i class="ri-close-line"></i>
            </button>
        `;
        row.querySelector(".js-remove-variant-row").addEventListener("click", () => {
            row.remove();
        });
        return row;
    }

    function fillVariantRows(wrap, variants) {
        const rowsContainer = wrap.querySelector(".js-variants-rows");
        if (!rowsContainer) return;
        rowsContainer.innerHTML = "";
        variants.forEach((v) => {
            rowsContainer.appendChild(makeVariantRow(v.name || "", v.price ?? ""));
        });
    }

    document.querySelectorAll(".js-variants-toggle").forEach((toggle) => {
        toggle.addEventListener("change", () => {
            const wrap = document.getElementById(toggle.dataset.wrap);
            if (!wrap) return;
            wrap.style.display = toggle.checked ? "flex" : "none";

            const rowsContainer = wrap.querySelector(".js-variants-rows");
            if (toggle.checked && rowsContainer && !rowsContainer.children.length) {
                rowsContainer.appendChild(makeVariantRow());
            }
        });
    });

    document.querySelectorAll(".js-add-variant-row").forEach((btn) => {
        btn.addEventListener("click", () => {
            const wrap = btn.closest(".js-variants-wrap");
            const rowsContainer = wrap?.querySelector(".js-variants-rows");
            if (rowsContainer) {
                rowsContainer.appendChild(makeVariantRow());
            }
        });
    });

    // ELIMINAR con modal (si lo tienes)
    let deleteFormPending = null;
    document.querySelectorAll(".js-delete-product-form").forEach((form) => {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            deleteFormPending = form;
            openModal(modalDelete);
        });
    });

    const btnCancelDelete = document.querySelector(".js-cancel-delete-product");
    const btnConfirmDelete = document.querySelector(
        ".js-confirm-delete-product"
    );

    if (btnCancelDelete) {
        btnCancelDelete.addEventListener("click", () => {
            deleteFormPending = null;
            closeModal(modalDelete);
        });
    }
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener("click", () => {
            if (deleteFormPending) {
                deleteFormPending.submit();
                deleteFormPending = null;
            }
        });
    }

    // TOASTS
    const stack = document.getElementById("kToastStack");
    if (stack) {
        stack.addEventListener("click", (e) => {
            if (e.target.classList.contains("k-toast__close")) {
                e.target.closest(".k-toast")?.classList.add("k-toast--hide");
            }
        });

        setTimeout(() => {
            stack
                .querySelectorAll(".k-toast")
                .forEach((t) => t.classList.add("k-toast--hide"));
        }, 4000);
    }

    // =============================
    // Dropdown de acciones (3 puntitos)
    // =============================
    document.addEventListener("click", (e) => {
        const trigger = e.target.closest(".k-actions-trigger");
        const clickedMenu = e.target.closest(".k-actions-menu");

        // Si se hizo click dentro del menú (en Editar / Eliminar), no cierres aún
        if (clickedMenu && !trigger) {
            return;
        }

        // Cerrar cualquier menú abierto
        document
            .querySelectorAll(".k-actions-menu.is-open")
            .forEach((m) => m.classList.remove("is-open"));

        // Si no se hizo click en un trigger, hasta aquí
        if (!trigger) return;

        const container = trigger.closest(".k-actions-dropdown");
        if (!container) return;

        const menu = container.querySelector(".k-actions-menu");
        if (!menu) return;

        // Abrir el menú (posición la controla el CSS: top:50% + translateY)
        menu.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
    });

    // Cerrar todos con ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            document
                .querySelectorAll(".k-actions-menu.is-open")
                .forEach((m) => m.classList.remove("is-open"));
        }
    });

    const card = document.getElementById('productsCard');
    const skel = document.getElementById('productsSkeleton');

    if (card && skel) {
        // En tu caso, como el contenido ya viene del server,
        // lo apagamos casi al instante (queda el efecto si la red es lenta).
        setTimeout(() => {
            skel.style.display = 'none';
            card.classList.remove('k-is-loading');
        }, 300); // puedes subirlo a 600–800ms si quieres que se note más
    }

});
