document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const modalForm = document.getElementById("modalTableForm");
    const modalDelete = document.getElementById("modalConfirmDeleteTable");
    const closeButtons = document.querySelectorAll(".js-close-modal");

    const btnOpenCreate = document.getElementById("btnOpenCreateTable");

    const formTable = document.getElementById("formTable");
    const formTableMethod = document.getElementById("formTableMethod");
    const modalFormTitle = document.getElementById("modalTableFormTitle");
    const btnTableSubmit = document.getElementById("btnTableSubmit");

    const fieldName = document.getElementById("fieldTableName");
    const fieldCode = document.getElementById("fieldTableCode");
    const fieldCapacity = document.getElementById("fieldTableCapacity");
    const fieldActive = document.getElementById("fieldTableActive");

    const formDelete = document.getElementById("formDeleteTable");
    const deleteText = document.getElementById("deleteTableText");

    const configEl = document.getElementById("kTablesConfig");
    const updateTemplate = configEl?.dataset.updateUrlTemplate || "";
    const destroyTemplate = configEl?.dataset.destroyUrlTemplate || "";

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add("k-modal--open");
        body.classList.add("k-no-scroll");
    }

    function closeAllModals() {
        [modalForm, modalDelete].forEach((m) => {
            if (!m) return;
            m.classList.remove("k-modal--open");
        });
        body.classList.remove("k-no-scroll");
    }

    closeButtons.forEach((btn) => {
        btn.addEventListener("click", closeAllModals);
    });

    [modalForm, modalDelete].forEach((modal) => {
        if (!modal) return;
        modal.addEventListener("click", (e) => {
            if (e.target.classList.contains("k-modal__backdrop")) {
                closeAllModals();
            }
        });
    });

    // ===== CREAR =====
    function setCreateMode() {
        formTable.action = formTable.getAttribute("action"); // tables.store
        formTableMethod.removeAttribute("name");
        formTableMethod.value = "";

        modalFormTitle.textContent = "Nueva mesa";
        btnTableSubmit.textContent = "Guardar mesa";

        fieldName.value = "";
        fieldCode.value = "";
        fieldCapacity.value = "";
        fieldActive.checked = true;
    }

    if (btnOpenCreate) {
        btnOpenCreate.addEventListener("click", () => {
            setCreateMode();
            openModal(modalForm);
            setTimeout(() => fieldName?.focus(), 100);
        });
    }

    // ===== EDITAR =====
    document.querySelectorAll(".js-edit-table").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name || "";
            const code = btn.dataset.code || "";
            const active = btn.dataset.active === "1";
            const capacity = btn.dataset.capacity || "";

            if (!id) return;

            const updateUrl = updateTemplate.replace("__ID__", id);
            formTable.action = updateUrl;
            formTableMethod.name = "_method";
            formTableMethod.value = "PUT";

            modalFormTitle.textContent = "Editar mesa";
            btnTableSubmit.textContent = "Guardar cambios";

            fieldName.value = name;
            fieldCode.value = code;
            fieldCapacity.value = capacity;
            fieldActive.checked = active;

            openModal(modalForm);
            setTimeout(() => fieldName?.focus(), 100);
        });
    });

    // ===== ELIMINAR =====
    document.querySelectorAll(".js-delete-table").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name || "";

            if (!id) return;

            const destroyUrl = destroyTemplate.replace("__ID__", id);
            formDelete.action = destroyUrl;
            deleteText.textContent = `¿Seguro que deseas eliminar la mesa “${name}”?`;

            openModal(modalDelete);
        });
    });

    // ===== Buscador =====
    const searchInput = document.getElementById("kTableSearch");
    if (searchInput) {
        searchInput.addEventListener("input", () => {
            const q = searchInput.value.trim().toLowerCase();
            document.querySelectorAll(".js-table-row").forEach((row) => {
                const name = row.dataset.name || "";
                const code = row.dataset.code || "";
                const match = !q || name.includes(q) || code.includes(q);
                row.style.display = match ? "" : "none";
            });
        });
    }

    // Cerrar toasts
    document.querySelectorAll(".k-toast__close").forEach((btn) => {
        btn.addEventListener("click", () => {
            const toast = btn.closest(".k-toast");
            if (toast) toast.remove();
        });
    });

    // =============================
    // Dropdown de acciones (3 puntos)
    // =============================
    document.addEventListener("click", (e) => {
        const trigger = e.target.closest(".k-actions-trigger");
        const clickedMenu = e.target.closest(".k-actions-menu");

        // Si se hizo click dentro del menú, no cierres
        if (clickedMenu && !trigger) {
            return;
        }

        // Cerrar todos los menús abiertos
        document
            .querySelectorAll(".k-actions-menu.is-open")
            .forEach((m) => m.classList.remove("is-open"));

        // Si NO se hizo click en un trigger, termina
        if (!trigger) return;

        const container = trigger.closest(".k-actions-dropdown");
        if (!container) return;

        const menu = container.querySelector(".k-actions-menu");
        if (!menu) return;

        // Abrir menú (posición se controla por CSS)
        menu.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
    });

    // Cerrar con ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            document
                .querySelectorAll(".k-actions-menu.is-open")
                .forEach((m) => m.classList.remove("is-open"));
        }
    });
});
