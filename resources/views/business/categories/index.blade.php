{{-- resources/views/business/categories/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="k-main-inner k-premium-container">

    {{-- ERRORES DE VALIDACIÓN --}}
    @if ($errors->any())
        <div class="k-premium-card" style="margin-bottom:1.5rem; border-color:#fecaca; background:#fef2f2; border-left:4px solid #dc2626; padding: 1rem 1.5rem;">
            <div style="display: flex; gap: 0.5rem; align-items: center; color:#b91c1c; font-weight: 600; font-size: 0.95rem;">
                <i class="ri-error-warning-line" style="font-size: 1.15rem;"></i>
                <span>Revisa los datos ingresados:</span>
            </div>
            <ul style="margin:0.4rem 0 0; padding-left:1.5rem; font-size:0.85rem; color:#991b1b; line-height: 1.45;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TOOLBAR --}}
    <header class="k-premium-header" style="border-bottom: none; margin-bottom: 0;">
        <div class="k-premium-title-wrap">
            <h1 style="font-size: 2rem;">Categorías</h1>
            <p class="k-premium-subtitle">
                Define las secciones de tu menú digital (Ej. Bebidas, Platillos, Postres).
            </p>
        </div>

        <div class="k-premium-header-actions">
            <button type="button" class="k-main-cta" id="btnOpenCreateCategory" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.3rem;">
                <i class="ri-add-line" style="font-size: 1.1rem;"></i>
                <span>Nueva Categoría</span>
            </button>
        </div>
    </header>

    {{-- CARD TABLA --}}
    <div class="k-premium-card" style="padding: 1.5rem;">
        
        <div class="k-table-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f3f4f6;">
            <div class="k-premium-kpi-trend up" style="background: rgba(79, 70, 229, 0.08); color: #4f46e5; font-size: 0.82rem; padding: 0.3rem 0.8rem;">
                Total: {{ $categories->total() }} categorías
            </div>

            <form method="GET" class="k-table-search" style="max-width: 300px;">
                <div style="position: relative; width: 100%;">
                    <i class="ri-search-line" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.95rem;"></i>
                    <input type="text" name="q" class="k-table-search-input" placeholder="Buscar categoría..." value="{{ request('q') }}" style="width: 100%; padding-left: 2.5rem; background: #ffffff; border: 1px solid #e5e7eb; color: #1f2937; border-radius: 99px; outline: none; font-size: 0.85rem; height: 38px;">
                    @if (request('q'))
                        <a href="{{ route('categories.index') }}" class="k-table-search-clear" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 0.8rem; text-decoration: none;">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="k-table-wrap">
            <table class="k-table k-table--compact" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #f3f4f6; color: #4b5563; font-size: 0.8rem; font-weight: 600;">
                        <th style="width:60px; padding: 0.85rem 1rem;">Posición</th>
                        <th style="padding: 0.85rem 1rem;">Nombre</th>
                        <th style="padding: 0.85rem 1rem;">Descripción</th>
                        <th style="padding: 0.85rem 1rem; width: 120px;">Estado</th>
                        <th style="padding: 0.85rem 1rem; width: 150px;">Fecha creación</th>
                        <th style="width:80px; padding: 0.85rem 1rem; text-align:right;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        @php
                            // Identificación elegante de iconos al igual que en la vista pública
                            $icon = 'ri-bowl-line';
                            $nameLower = Str::lower($cat->name);
                            if (str_contains($nameLower, 'café') || str_contains($nameLower, 'coffee') || str_contains($nameLower, 'bebida') || str_contains($nameLower, 'jugo')) {
                                $icon = 'ri-cup-line';
                            } elseif (str_contains($nameLower, 'malteada') || str_contains($nameLower, 'frapp') || str_contains($nameLower, 'helado')) {
                                $icon = 'ri-ice-cream-line';
                            } elseif (str_contains($nameLower, 'postre') || str_contains($nameLower, 'pastel') || str_contains($nameLower, 'dulce') || str_contains($nameLower, 'crepa')) {
                                $icon = 'ri-cake-2-line';
                            } elseif (str_contains($nameLower, 'snack') || str_contains($nameLower, 'botana') || str_contains($nameLower, 'entrada')) {
                                $icon = 'ri-restaurant-2-line';
                            } elseif (str_contains($nameLower, 'sandwich') || str_contains($nameLower, 'baguette') || str_contains($nameLower, 'panini') || str_contains($nameLower, 'torta')) {
                                $icon = 'ri-bread-slice-line';
                            }
                        @endphp
                        <tr style="border-bottom: 1px solid #f9fafb; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(79,70,229,0.01)'" onmouseout="this.style.background='none'">
                            <td data-label="Posición" style="padding: 1rem; font-weight: 600; color: #4f46e5;">
                                <div style="background: rgba(79, 70, 229, 0.05); border-radius: 8px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                    {{ $cat->position ?? '-' }}
                                </div>
                            </td>
                            <td data-label="Nombre" style="padding: 1rem; font-weight: 600; color: #111827;">
                                <div style="display: flex; align-items: center; gap: 0.65rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99, 102, 241, 0.08); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.05rem;">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                    <span>{{ $cat->name }}</span>
                                </div>
                            </td>
                            <td data-label="Descripción" style="padding: 1rem; color: #4b5563; font-size: 0.85rem;">
                                {{ $cat->description ?: '—' }}
                            </td>
                            <td data-label="Estado" style="padding: 1rem;">
                                @if ($cat->is_active)
                                    <span class="k-premium-badge completed" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">Activa</span>
                                @else
                                    <span class="k-premium-badge cancelled" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">Inactiva</span>
                                @endif
                            </td>
                            <td data-label="Creada" style="padding: 1rem; color: #6b7280; font-size: 0.82rem;">
                                <div style="display: flex; align-items: center; gap: 0.35rem;">
                                    <i class="ri-time-line" style="font-size: 0.9rem;"></i>
                                    <span>{{ $cat->created_at?->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td data-label="Acciones" style="padding: 1rem; text-align:right;" class="k-actions-cell">
                                <div class="k-actions-dropdown" style="justify-content: flex-end;">
                                    <button type="button" class="k-actions-trigger js-open-actions"
                                            data-id="{{ $cat->id }}"
                                            data-name="{{ $cat->name }}"
                                            data-description="{{ $cat->description }}"
                                            data-active="{{ $cat->is_active }}"
                                            data-edit-url="{{ route('categories.edit', $cat) }}"
                                            data-delete-url="{{ route('categories.destroy', $cat) }}"
                                            style="width: 34px; height: 34px; background: #f3f4f6; border-radius: 50%; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; color: #4b5563; transition: all 0.2s ease;">
                                        <i class="ri-more-2-fill" style="font-size: 1.05rem;"></i>
                                    </button>
                                </div>
                            </td>       
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem 1rem; text-align: center; color: #6b7280;">
                                <div style="font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem;">
                                    <i class="ri-list-check-3"></i>
                                </div>
                                <h4 style="font-size: 0.95rem; font-weight: 600; color: #374151; margin-bottom: 0.2rem;">Sin categorías registradas</h4>
                                <p style="font-size: 0.8rem; max-width: 300px; margin: 0 auto; line-height: 1.4;">
                                    Crea tu primera categoría para organizar los platillos de tu menú digital.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MENU FLOTANTE PARA CATEGORÍAS (COMPATIBILIDAD CON SCRIPTS) --}}
        <div id="k-actions-popover" class="k-popover hidden" style="background: #181424; border-radius: 14px; border: 1px solid rgba(255,255,255,0.06); padding: 6px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999;">
            <div class="k-actions-group-label" style="color: #8b5cf6; padding: 4px 8px 6px; font-weight: 600;">Opciones</div>

            <button class="k-actions-item js-edit-category" style="width: 100%; border: none; background: transparent; color: #e0ddff; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
                <i class="ri-edit-2-line" style="font-size: 1rem;"></i>
                <span>Editar categoría</span>
            </button>

            <button class="k-actions-item k-actions-item--danger js-delete-category" style="width: 100%; border: none; background: transparent; color: #fca5a5; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; margin-top: 2px;">
                <i class="ri-delete-bin-line" style="font-size: 1rem;"></i>
                <span>Eliminar categoría</span>
            </button>
        </div>

        {{-- BACKDROP --}}
        <div id="k-popover-backdrop" class="k-popover-backdrop hidden" style="position: fixed; inset: 0; background: rgba(0,0,0,0.1); z-index: 9998; backdrop-filter: blur(1px);"></div>

        @if($categories->hasPages())
            <div style="margin-top:1.5rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                {{ $categories->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ============ MODAL NUEVA CATEGORÍA ============ --}}
<div class="k-modal" id="modalCreateCategory">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5);">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827;">Nueva categoría</h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="k-modal__body k-form" style="padding: 1.5rem;">
                <div class="k-field" style="margin-bottom: 1.25rem;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Nombre de la categoría *</label>
                    <input type="text" name="name" class="k-input" maxlength="190" placeholder="Ej. Hamburguesas, Cafés calientes..." required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                    <div class="k-field-message" style="font-size: 0.75rem; color: #6b7280; margin-top: 0.3rem;">
                        Aparecerá como pestaña de filtrado rápido en tu menú.
                    </div>
                </div>

                <div class="k-field" style="margin-bottom: 1.25rem;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Descripción (opcional)</label>
                    <input type="text" name="description" class="k-input" maxlength="255" placeholder="Breve nota de la sección..." style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                </div>

                <div class="k-field k-switch" style="display: flex; align-items: center; justify-content: space-between; background: #f9fafb; border-radius: 12px; padding: 0.75rem 1rem;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin: 0;">Visible en menú público</label>
                    <label class="k-switch__control" style="position: relative; display: inline-block; width: 44px; height: 24px;">
                        <input type="checkbox" name="is_active" checked style="opacity: 0; width: 0; height: 0;">
                        <span class="k-switch__slider" style="position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: .3s; border-radius: 34px;"></span>
                        <span class="k-switch__text" style="display:none;"></span>
                    </label>
                </div>
            </div>

            <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
                <button type="button" class="k-btn-secondary js-close-modal" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cancelar</button>
                <button type="submit" class="k-btn-primary" style="border-radius: 99px; background: #4f46e5; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">Guardar categoría</button>
            </div>
        </form>
    </div>
</div>

{{-- ============ MODAL EDITAR CATEGORÍA ============ --}}
<div class="k-modal" id="modalEditCategory">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5);">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827;">Editar categoría</h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>

        <form method="POST" action="" id="formEditCategory">
            @csrf
            @method('PUT')
            <div class="k-modal__body" style="padding: 1.5rem;">
                <div class="k-field" style="margin-bottom: 1.25rem;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Nombre de la categoría *</label>
                    <input type="text" name="name" class="k-input" maxlength="190" required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                </div>

                <div class="k-field" style="margin-bottom: 1.25rem;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Descripción (opcional)</label>
                    <input type="text" name="description" class="k-input" maxlength="255" style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                </div>

                <div class="k-field k-switch" style="display: flex; align-items: center; justify-content: space-between; background: #f9fafb; border-radius: 12px; padding: 0.75rem 1rem;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin: 0;">Visible en menú público</label>
                    <label class="k-switch__control" style="position: relative; display: inline-block; width: 44px; height: 24px;">
                        <input type="checkbox" name="is_active" id="edit_is_active_category" style="opacity: 0; width: 0; height: 0;">
                        <span class="k-switch__slider" style="position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: .3s; border-radius: 34px;"></span>
                        <span class="k-switch__text" style="display:none;"></span>
                    </label>
                </div>
            </div>

            <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
                <button type="button" class="k-btn-secondary js-close-modal" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cerrar</button>
                <button type="submit" class="k-btn-primary" style="border-radius: 99px; background: #4f46e5; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- ============ MODAL CONFIRMAR ELIMINACIÓN ============ --}}
<div class="k-modal" id="modalConfirmDeleteCategory">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); max-width: 440px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ri-error-warning-line" style="color: #dc2626; font-size: 1.3rem;"></i>
                <span>Eliminar categoría</span>
            </h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>
        <div class="k-modal__body" style="padding: 1.5rem; font-size: 0.9rem; color: #4b5563; line-height: 1.5;">
            <p style="margin: 0 0 0.5rem 0; font-weight: 600; color: #1f2937;">¿Estás seguro de que deseas eliminar esta categoría?</p>
            <p style="margin: 0;">Esta acción es irreversible y los productos asociados podrían quedar sin categoría asignada en el menú.</p>
        </div>
        <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
            <button type="button" class="k-btn-secondary js-cancel-delete-category" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cancelar</button>
            <button type="button" class="k-btn-primary js-confirm-delete-category" style="border-radius: 99px; background: #dc2626; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">Eliminar</button>
        </div>
    </div>
</div>

{{-- TOASTS --}}
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
    {{-- CSS Inline de switches premium --}}
    <style>
        .k-switch__control input:checked + .k-switch__slider {
            background-color: #4f46e5 !important;
        }
        .k-switch__control input:checked + .k-switch__slider::before {
            transform: translateX(20px);
        }
        .k-switch__slider::before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }
        .k-switch__slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 34px;
        }
    </style>
    <script src="{{ asset('js/business-categories.js') }}"></script>
    <script src="{{ asset('js/categories-actions.js') }}"></script>
@endpush
