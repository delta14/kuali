document.addEventListener('DOMContentLoaded', () => {
    // utilidades modales
    function openModal(modal) {
        if (modal) modal.classList.add('k-modal--open');
    }
    function closeModal(modal) {
        if (modal) modal.classList.remove('k-modal--open');
    }

    const modalCreate = document.getElementById('modalCreateBusiness');
    const modalEdit   = document.getElementById('modalEditBusiness');
    const modalDelete = document.getElementById('modalConfirmDelete');

    // cerrar modales
    document.querySelectorAll('.js-close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            [modalCreate, modalEdit, modalDelete].forEach(closeModal);
        });
    });

    // abrir modal NUEVO
    const openCreateBtn = document.getElementById('btnOpenCreateBusiness');
    if (openCreateBtn && modalCreate) {
        openCreateBtn.addEventListener('click', () => openModal(modalCreate));
    }

    // ----- Tabs wizard -----
    function setActiveTab(key) {
        document.querySelectorAll('.k-tabs__tab').forEach(b => {
            b.classList.toggle('k-tabs__tab--active', b.dataset.tab === key);
        });
        document.querySelectorAll('[data-tab-panel]').forEach(panel => {
            panel.classList.toggle('k-tabs__panel--active', panel.dataset.tabPanel === key);
        });
    }

    document.querySelectorAll('.k-tabs__tab').forEach(btn => {
        btn.addEventListener('click', () => setActiveTab(btn.dataset.tab));
    });

    // ----- Editar comercio -----
    const editButtons = document.querySelectorAll('.js-edit-business');
    const formEdit    = document.getElementById('formEditBusiness');
    const adminBizId  = document.getElementById('admin_business_id');
    const editActive  = document.getElementById('edit_is_active');
    const adminNameInput  = document.querySelector('form[data-tab-panel="admin"] input[name="admin_name"]');
    const adminEmailInput = document.querySelector('form[data-tab-panel="admin"] input[name="admin_email"]');


    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (!formEdit) return;

            const id = btn.dataset.id;
            formEdit.action = `/super/businesses/${id}`;

            formEdit.querySelector('[name="name"]').value          = btn.dataset.name || '';
            formEdit.querySelector('[name="slug"]').value          = btn.dataset.slug || '';
            formEdit.querySelector('[name="email_contact"]').value = btn.dataset.email || '';
            formEdit.querySelector('[name="phone"]').value         = btn.dataset.phone || '';
            formEdit.querySelector('[name="address"]').value       = btn.dataset.address || '';
            formEdit.querySelector('[name="plan"]').value          = btn.dataset.plan || '';

            if (editActive) {
                editActive.checked = btn.dataset.active === '1';
            }

            if (adminBizId) {
                adminBizId.value = id;
            }

            if (adminNameInput)  adminNameInput.value  = btn.dataset.adminName  || '';
            if (adminEmailInput) adminEmailInput.value = btn.dataset.adminEmail || '';


            setActiveTab('business');
            openModal(modalEdit);
        });
    });

    // ----- Modal ELIMINAR -----
    let deleteFormPending = null;

    document.querySelectorAll('.js-delete-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            deleteFormPending = form;
            openModal(modalDelete);
        });
    });

    const btnCancelDelete  = document.querySelector('.js-cancel-delete');
    const btnConfirmDelete = document.querySelector('.js-confirm-delete');

    if (btnCancelDelete) {
        btnCancelDelete.addEventListener('click', () => {
            deleteFormPending = null;
            closeModal(modalDelete);
        });
    }

    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', () => {
            if (deleteFormPending) {
                deleteFormPending.submit();
                deleteFormPending = null;
            }
        });
    }

    // ----- Toasts -----
    const stack = document.getElementById('kToastStack');
    if (stack) {
        stack.addEventListener('click', e => {
            if (e.target.classList.contains('k-toast__close')) {
                const toast = e.target.closest('.k-toast');
                if (toast) toast.classList.add('k-toast--hide');
            }
        });

        setTimeout(() => {
            stack.querySelectorAll('.k-toast').forEach(t => {
                t.classList.add('k-toast--hide');
            });
        }, 4000);
    }
});
