{{-- resources/views/super/businesses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Comercios')

@section('content')
    <div class="k-main-inner">
        @if ($errors->any())
            <div class="k-table-card" style="margin-bottom:1rem; border-color:#fecaca; background:#fef2f2;">
                <strong style="color:#b91c1c; font-size:0.9rem;">Revisa los datos del formulario:</strong>
                <ul style="margin:0.3rem 0 0; padding-left:1.2rem; font-size:0.82rem; color:#991b1b;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- TOOLBAR --}}
        <div class="k-page-toolbar">
            <div>
                <h1 class="k-page-toolbar-title">Comercios</h1>
                <p class="k-page-toolbar-subtitle">
                    Administra los negocios registrados en Kuali.
                </p>
            </div>

            <button type="button" class="k-main-cta" id="btnOpenCreateBusiness">
                <i class="ri-add-line" style="margin-right:.3rem;"></i>
                Nuevo comercio
            </button>
        </div>

        {{-- CARD TABLA --}}
        <div class="k-table-card">
            <div class="k-table-header">
                <div class="k-table-meta">
                    Total: {{ $businesses->total() }} comercios
                </div>

                <form method="GET" class="k-table-search">
                    <input type="text" name="q" class="k-table-search-input" placeholder="Buscar comercio..."
                        value="{{ request('q') }}">
                    @if (request('q'))
                        <button class="k-table-search-clear">Limpiar</button>
                    @endif
                </form>
            </div>

            <table class="k-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Correo</th>
                        <th>Plan</th>
                        <th>Admin</th>
                        <th>Activo</th>
                        <th>Creado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($businesses as $business)
                        @php
                            // Admin principal del comercio (si existe)
                            $mainAdmin = $business->admins->first();
                        @endphp
                        <tr>
                            <td data-label="Nombre">{{ $business->name }}</td>

                            <td data-label="Slug">
                                <span class="k-badge-soft">{{ $business->slug }}</span>
                            </td>

                            <td data-label="Correo">
                                {{ $business->email_contact ?? '—' }}
                            </td>

                            <td data-label="Plan">
                                {{ $business->plan ?? '—' }}
                            </td>

                            <td data-label="Admin">
                                @if ($business->admins_count > 0 && $mainAdmin)
                                    <div class="k-admin-chip">
                                        <span class="k-admin-chip__name">{{ $mainAdmin->name }}</span>
                                        <span class="k-admin-chip__email">{{ $mainAdmin->email }}</span>
                                        @if ($business->admins_count > 1)
                                            <span class="k-admin-chip__more">
                                                +{{ $business->admins_count - 1 }} más
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="k-pill k-pill--warning">Sin admin</span>
                                @endif
                            </td>

                            <td data-label="Activo">
                                @if ($business->is_active)
                                    <span class="k-pill k-pill--success">Activo</span>
                                @else
                                    <span class="k-pill">Inactivo</span>
                                @endif
                            </td>

                            <td data-label="Creado">
                                {{ $business->created_at?->format('d/m/Y') }}
                            </td>

                            <td data-label="Acciones">
                                <div class="k-table-actions">
                                    {{-- EDITAR abre modal wizard --}}
                                    <button type="button" class="k-link-ghost js-edit-business"
                                        data-id="{{ $business->id }}" data-name="{{ $business->name }}"
                                        data-slug="{{ $business->slug }}" data-email="{{ $business->email_contact }}"
                                        data-phone="{{ $business->phone }}" data-address="{{ $business->address }}"
                                        data-plan="{{ $business->plan }}"
                                        data-active="{{ $business->is_active ? '1' : '0' }}"
                                        data-admin-name="{{ $mainAdmin->name ?? '' }}"
                                        data-admin-email="{{ $mainAdmin->email ?? '' }}">
                                        Editar
                                    </button>

                                    {{-- ELIMINAR --}}
                                    <form method="POST" action="{{ route('super.businesses.destroy', $business) }}"
                                        class="js-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="k-link-ghost">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No hay comercios registrados.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

            <div style="margin-top:1rem;">
                {{ $businesses->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- =========================
         MODAL · NUEVO COMERCIO
       ========================= --}}
    <div class="k-modal" id="modalCreateBusiness">
        <div class="k-modal__backdrop js-close-modal"></div>
        <div class="k-modal__dialog">
            <div class="k-modal__header">
                <h2>Nuevo comercio</h2>
                <button type="button" class="k-modal__close js-close-modal">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('super.businesses.store') }}">
                @csrf
                <div class="k-modal__body">
                    <div class="k-field">
                        <label class="k-label">Nombre del comercio</label>
                        <input type="text" name="name" class="k-input" placeholder="Ej. Pizza Especial Kuali"
                            required>
                    </div>

                    <div class="k-grid-2">
                        <div class="k-field">
                            <label class="k-label">Slug (opcional)</label>
                            <input type="text" name="slug" class="k-input" placeholder="ej. pizza-especial-kuali">
                        </div>

                        <div class="k-field">
                            <label class="k-label">Plan</label>
                            <select name="plan" class="k-input">
                                <option value="">Seleccionar…</option>
                                <option value="basic">Básico</option>
                                <option value="pro">Pro</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                    </div>

                    <div class="k-grid-2">
                        <div class="k-field">
                            <label class="k-label">Correo de contacto</label>
                            <input type="email" name="email_contact" class="k-input" placeholder="tucorreo@ejemplo.com">
                        </div>

                        <div class="k-field">
                            <label class="k-label">Teléfono</label>
                            <input type="text" name="phone" class="k-input" placeholder="55 1234 5678">
                        </div>
                    </div>

                    <div class="k-field">
                        <label class="k-label">Dirección</label>
                        <input type="text" name="address" class="k-input"
                            placeholder="Calle, número, colonia, ciudad">
                    </div>

                    <div class="k-field k-switch">
                        <label class="k-label">Comercio activo</label>
                        <label class="k-switch__control">
                            <input type="checkbox" name="is_active" checked>
                            <span class="k-switch__slider"></span>
                            <span class="k-switch__text">Activo desde el inicio</span>
                        </label>
                    </div>
                </div>

                <div class="k-modal__footer">
                    <button type="button" class="k-btn-secondary js-close-modal">Cancelar</button>
                    <button type="submit" class="k-btn-primary">Guardar comercio</button>
                </div>
            </form>
        </div>
    </div>


    {{-- =========================
         MODAL · EDITAR (WIZARD)
       ========================= --}}
    <div class="k-modal" id="modalEditBusiness">
        <div class="k-modal__backdrop js-close-modal"></div>
        <div class="k-modal__dialog">
            <div class="k-modal__header">
                <h2>Editar comercio</h2>
                <button type="button" class="k-modal__close js-close-modal">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div class="k-tabs">
                <button type="button" class="k-tabs__tab k-tabs__tab--active" data-tab="business">
                    Datos del comercio
                </button>
                <button type="button" class="k-tabs__tab" data-tab="admin">
                    Usuario admin
                </button>
            </div>

            {{-- TAB 1: datos negocio --}}
            <form method="POST" action="" id="formEditBusiness" class="k-tabs__panel k-tabs__panel--active"
                data-tab-panel="business">
                @csrf
                @method('PUT')
                <div class="k-modal__body">
                    <div class="k-field">
                        <label class="k-label">Nombre del comercio</label>
                        <input type="text" name="name" class="k-input" required>
                    </div>

                    <div class="k-grid-2">
                        <div class="k-field">
                            <label class="k-label">Slug</label>
                            <input type="text" name="slug" class="k-input">
                        </div>

                        <div class="k-field">
                            <label class="k-label">Plan</label>
                            <select name="plan" class="k-input">
                                <option value="">Sin plan</option>
                                <option value="basic">Básico</option>
                                <option value="pro">Pro</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                    </div>

                    <div class="k-grid-2">
                        <div class="k-field">
                            <label class="k-label">Correo de contacto</label>
                            <input type="email" name="email_contact" class="k-input">
                        </div>

                        <div class="k-field">
                            <label class="k-label">Teléfono</label>
                            <input type="text" name="phone" class="k-input">
                        </div>
                    </div>

                    <div class="k-field">
                        <label class="k-label">Dirección</label>
                        <input type="text" name="address" class="k-input">
                    </div>

                    <div class="k-field k-switch">
                        <label class="k-label">Estado</label>
                        <label class="k-switch__control">
                            <input type="checkbox" name="is_active" id="edit_is_active">
                            <span class="k-switch__slider"></span>
                            <span class="k-switch__text">Comercio activo</span>
                        </label>
                    </div>
                </div>

                <div class="k-modal__footer">
                    <button type="button" class="k-btn-secondary js-close-modal">Cerrar</button>
                    <button type="submit" class="k-btn-primary">Guardar cambios</button>
                </div>
            </form>

            {{-- TAB 2: crear / asignar admin --}}
            <form method="POST" action="{{ route('super.businesses.assign-admin') }}" class="k-tabs__panel"
                data-tab-panel="admin">
                @csrf
                <input type="hidden" name="business_id" id="admin_business_id">

                <div class="k-modal__body">
                    <p class="k-auth-help-text">
                        Crea o asigna el usuario administrador para este comercio.
                    </p>

                    <div class="k-field">
                        <label class="k-label">Nombre del admin</label>
                        <input type="text" name="admin_name" class="k-input">
                    </div>

                    <div class="k-field">
                        <label class="k-label">Correo electrónico *</label>
                        <input type="email" name="admin_email" class="k-input" required>
                    </div>

                    <div class="k-field">
                        <label class="k-label">Contraseña (si es nuevo)</label>
                        <input type="password" name="admin_password" class="k-input">
                        <div class="k-field-message">
                            Si el correo ya existe, la contraseña es opcional.
                        </div>
                    </div>
                </div>

                <div class="k-modal__footer">
                    <button type="submit" class="k-btn-primary">
                        Guardar / asignar admin
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- =========================
         MODAL · CONFIRMAR ELIMINACIÓN
       ========================= --}}
    <div class="k-modal" id="modalConfirmDelete">
        <div class="k-modal__backdrop js-close-modal"></div>
        <div class="k-modal__dialog">
            <div class="k-modal__header">
                <h2>Eliminar comercio</h2>
                <button type="button" class="k-modal__close js-close-modal">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="k-modal__body">
                <p>¿Seguro que deseas eliminar este comercio? Esta acción no se puede deshacer.</p>
            </div>
            <div class="k-modal__footer">
                <button type="button" class="k-btn-secondary js-cancel-delete">Cancelar</button>
                <button type="button" class="k-btn-primary js-confirm-delete">Eliminar</button>
            </div>
        </div>
    </div>

    {{-- =========================
         TOASTS (mensajes tipo card)
       ========================= --}}
    <div class="k-toast-stack" id="kToastStack">
        @if (session('success'))
            <div class="k-toast k-toast--success">
                <div class="k-toast__icon"><i class="ri-check-line"></i></div>
                <div class="k-toast__content">
                    <strong>Acción exitosa</strong>
                    <span>{{ session('success') }}</span>
                </div>
                <button class="k-toast__close">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="k-toast k-toast--error">
                <div class="k-toast__icon"><i class="ri-error-warning-line"></i></div>
                <div class="k-toast__content">
                    <strong>Ocurrió un error</strong>
                    <span>{{ session('error') }}</span>
                </div>
                <button class="k-toast__close">&times;</button>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/super-businesses.js') }}"></script>
@endpush
