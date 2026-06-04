document.addEventListener("DOMContentLoaded", () => {
    function openModal(modal) {
        if (modal) modal.classList.add("k-modal--open");
    }
    function closeModal(modal) {
        if (modal) modal.classList.remove("k-modal--open");
    }

    const modalCreate = document.getElementById("modalCreateCategory");
    const modalEdit = document.getElementById("modalEditCategory");
    const modalDelete = document.getElementById("modalConfirmDeleteCategory");

    // Cerrar por botones con clase js-close-modal
    document.querySelectorAll(".js-close-modal").forEach((btn) => {
        btn.addEventListener("click", () => {
            [modalCreate, modalEdit, modalDelete].forEach(closeModal);
        });
    });

    // Abrir modal NUEVA categoría
    const btnOpenCreate = document.getElementById("btnOpenCreateCategory");
    if (btnOpenCreate && modalCreate) {
        btnOpenCreate.addEventListener("click", () => {
            // limpiar formulario de creación
            const form = modalCreate.querySelector("form");
            if (form) form.reset();
            openModal(modalCreate);
        });
    }

    // --- BLOQUEO DE BOTÓN EN CREAR CATEGORÍA ---
    const createForm = modalCreate ? modalCreate.querySelector("form") : null;

    if (createForm) {
        const createSubmitBtn = createForm.querySelector(
            'button[type="submit"]'
        );

        createForm.addEventListener("submit", (e) => {
            // Dejamos que el submit siga su curso (redirect normal),
            // solo bloqueamos para evitar doble clic.
            if (createSubmitBtn && !createSubmitBtn.disabled) {
                createSubmitBtn.disabled = true;
                createSubmitBtn.dataset.originalText =
                    createSubmitBtn.textContent;
                createSubmitBtn.textContent = "Guardando...";
            }

            if (btnOpenCreate) {
                btnOpenCreate.disabled = true;
            }
        });
    }

    // EDITAR categoría
    const editButtons = document.querySelectorAll(".js-edit-category");
    const formEdit = document.getElementById("formEditCategory");
    const editActive = document.getElementById("edit_is_active_category");

    editButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            if (!formEdit) return;

            const id = btn.dataset.id;
            formEdit.action = `/categories/${id}`;

            formEdit.querySelector('[name="name"]').value =
                btn.dataset.name || "";
            formEdit.querySelector('[name="description"]').value =
                btn.dataset.description || "";

            if (editActive) {
                editActive.checked = btn.dataset.active === "1";
            }

            openModal(modalEdit);
        });
    });

    // --- BLOQUEO DE BOTÓN EN EDITAR CATEGORÍA ---
    if (formEdit) {
        const editSubmitBtn = formEdit.querySelector('button[type="submit"]');

        formEdit.addEventListener("submit", () => {
            if (editSubmitBtn && !editSubmitBtn.disabled) {
                editSubmitBtn.disabled = true;
                editSubmitBtn.dataset.originalText = editSubmitBtn.textContent;
                editSubmitBtn.textContent = "Guardando...";
            }
        });
    }

    // ELIMINAR con modal de confirmación
    let deleteFormPending = null;

    document.querySelectorAll(".js-delete-form").forEach((form) => {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            deleteFormPending = form;
            openModal(modalDelete);
        });
    });

    const btnCancelDelete = document.querySelector(
        ".js-cancel-delete-category"
    );
    const btnConfirmDelete = document.querySelector(
        ".js-confirm-delete-category"
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
                const toast = e.target.closest(".k-toast");
                if (toast) toast.classList.add("k-toast--hide");
            }
        });

        setTimeout(() => {
            stack.querySelectorAll(".k-toast").forEach((t) => {
                t.classList.add("k-toast--hide");
            });
        }, 4000);
    }

    // --- RESETEAR ESTADO AL VOLVER / RECARGAR (BFCache, etc.) ---
    window.addEventListener("pageshow", () => {
        // Botón del toolbar
        if (btnOpenCreate) {
            btnOpenCreate.disabled = false;
        }

        // Botón submit del modal de creación
        if (createForm) {
            const createSubmitBtn = createForm.querySelector(
                'button[type="submit"]'
            );
            if (createSubmitBtn) {
                createSubmitBtn.disabled = false;
                if (createSubmitBtn.dataset.originalText) {
                    createSubmitBtn.textContent =
                        createSubmitBtn.dataset.originalText;
                }
            }
        }

        // Botón submit del modal de edición
        if (formEdit) {
            const editSubmitBtn = formEdit.querySelector(
                'button[type="submit"]'
            );
            if (editSubmitBtn) {
                editSubmitBtn.disabled = false;
                if (editSubmitBtn.dataset.originalText) {
                    editSubmitBtn.textContent =
                        editSubmitBtn.dataset.originalText;
                }
            }
        }
    });
});
