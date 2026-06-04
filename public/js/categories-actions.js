document.addEventListener("DOMContentLoaded", () => {
    const popover  = document.getElementById("k-actions-popover");
    const backdrop = document.getElementById("k-popover-backdrop");

    if (!popover || !backdrop) return;

    let currentEditUrl = "";
    let currentDeleteUrl = "";
    let currentCategoryName = "";
    let currentCategoryId = "";
    let currentBtn = null;

    function openPopover(btn) {
        currentBtn = btn;

        currentCategoryId   = btn.dataset.id;
        currentCategoryName = btn.dataset.name || "";
        currentEditUrl      = btn.dataset.editUrl || "";
        currentDeleteUrl    = btn.dataset.deleteUrl || "";

        // Copiar datasets al botón de edición para que el modal los lea
        const popoverEditBtn = popover.querySelector(".js-edit-category");
        if (popoverEditBtn) {
            popoverEditBtn.dataset.id = btn.dataset.id || "";
            popoverEditBtn.dataset.name = btn.dataset.name || "";
            popoverEditBtn.dataset.description = btn.dataset.description || "";
            popoverEditBtn.dataset.active = btn.dataset.active || "0";
        }

        // Mostrar para poder medir tamaños
        popover.classList.remove("hidden");
        backdrop.classList.remove("hidden");

        // Asegúrate de que en el CSS tengas:
        // #k-actions-popover { position: fixed; }
        const rect      = btn.getBoundingClientRect();
        const popWidth  = popover.offsetWidth;
        const popHeight = popover.offsetHeight;

        const margin = 16;

        // Centramos verticalmente el popover respecto al botón
        let top  = rect.top + rect.height / 2 - popHeight / 2;
        let left = rect.right + 16; // un poco a la derecha del botón

        // No pegarlo demasiado al borde superior
        if (top < margin) top = margin;

        // Si no cabe a la derecha, lo movemos a la izquierda del botón
        const maxLeft = window.innerWidth - popWidth - margin;
        if (left > maxLeft) {
            left = rect.left - popWidth - 16;
        }

        popover.style.top  = `${top}px`;
        popover.style.left = `${left}px`;
    }

    function closePopover() {
        popover.classList.add("hidden");
        backdrop.classList.add("hidden");
        currentBtn = null;
    }

    // Abrir / toggle popover
    document.querySelectorAll(".js-open-actions").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();

            // Si ya está abierto sobre el mismo botón, lo cerramos
            if (
                currentBtn === btn &&
                !popover.classList.contains("hidden")
            ) {
                closePopover();
                return;
            }

            openPopover(btn);
        });
    });

    // Cerrar popover al hacer clic en el backdrop
    backdrop.addEventListener("click", () => {
        closePopover();
    });

    // Cerrar al hacer clic en cualquier parte fuera del popover/botón
    document.addEventListener("click", (e) => {
        if (
            popover.classList.contains("hidden") ||
            popover.contains(e.target) ||
            e.target.closest(".js-open-actions")
        ) {
            return;
        }
        closePopover();
    });

    // Cerrar con ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !popover.classList.contains("hidden")) {
            closePopover();
        }
    });

    // Acción: Editar
    const editBtn = document.querySelector(".js-edit-category");
    if (editBtn) {
        editBtn.addEventListener("click", () => {
            closePopover();
        });
    }

    // Acción: Eliminar
    const deleteBtn = document.querySelector(".js-delete-category");
    if (deleteBtn) {
        deleteBtn.addEventListener("click", () => {
            if (!currentDeleteUrl) return;

            if (
                !confirm(
                    `¿Eliminar la categoría "${currentCategoryName}"?`
                )
            )
                return;

            const form = document.createElement("form");
            form.method = "POST";
            form.action = currentDeleteUrl;

            const token = document.createElement("input");
            token.type = "hidden";
            token.name = "_token";
            token.value =
                document.querySelector('meta[name="csrf-token"]')
                    .content;

            const method = document.createElement("input");
            method.type = "hidden";
            method.name = "_method";
            method.value = "DELETE";

            form.appendChild(token);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        });
    }
});
