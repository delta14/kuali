@extends('layouts.app')

@section('title', 'Mesas')

@section('content')
<div class="k-main-inner k-premium-container">

    {{-- Errores --}}
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
            <h1 style="font-size: 2rem;">Mesas y Códigos QR</h1>
            <p class="k-premium-subtitle">
                Administra las mesas del salón y descarga flyers con códigos QR exclusivos de auto-pedido.
            </p>
        </div>

        <div class="k-premium-header-actions">
            <button type="button" class="k-main-cta" id="btnOpenCreateTable" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.3rem;">
                <i class="ri-add-line" style="font-size: 1.1rem;"></i>
                <span>Nueva Mesa</span>
            </button>
        </div>
    </header>

    {{-- HEADER DE FILTROS Y META --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div class="k-premium-kpi-trend up" style="background: rgba(79, 70, 229, 0.08); color: #4f46e5; font-size: 0.82rem; padding: 0.3rem 0.8rem;">
            Total: {{ $tables->count() }} mesas en el salón
        </div>

        <div class="k-table-search" style="max-width: 300px; margin: 0;">
            <div style="position: relative; width: 100%;">
                <i class="ri-search-line" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.95rem;"></i>
                <input type="text" id="kPosSearch" placeholder="Buscar mesa..." style="width: 100%; padding-left: 2.5rem; background: #ffffff; border: 1px solid #e5e7eb; color: #1f2937; border-radius: 99px; outline: none; font-size: 0.85rem; height: 38px;">
            </div>
        </div>
    </div>

    {{-- GRID FÍSICO DE TARJETAS DE MESA (DASHBOARD PREMIUM) --}}
    <div class="k-premium-tables-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;" id="kTablesContainer">
        @forelse($tables as $idx => $table)
            <article class="k-premium-card js-table-card-item" 
                     data-name="{{ Str::lower($table->name) }}" 
                     data-code="{{ Str::lower($table->code) }}" 
                     style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; border-color: {{ $table->is_active ? 'rgba(229, 231, 235, 0.7)' : '#fee2e2' }}; transition: all 0.3s ease;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.04)'; this.style.borderColor='#4f46e5';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(0,0,0,0.02)'; this.style.borderColor='{{ $table->is_active ? 'rgba(229, 231, 235, 0.7)' : '#fee2e2' }}';">
                
                {{-- Cabecera de la mesa --}}
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #111827;">{{ $table->name }}</h3>
                        <span style="font-size: 0.75rem; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Ref: {{ $table->code }}</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.35rem; background: #f3f4f6; border-radius: 8px; padding: 0.25rem 0.5rem; font-size: 0.8rem; font-weight: 600; color: #4b5563;">
                        <i class="ri-user-2-line" style="font-size: 0.9rem;"></i>
                        <span>{{ $table->capacity ?: '—' }} pax</span>
                    </div>
                </div>

                {{-- QR Central Interactivo --}}
                <div style="background: #fafafa; border: 1px solid #f3f4f6; border-radius: 16px; padding: 1rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; position: relative;">
                    @if ($table->qr_path)
                        <img src="{{ asset('storage/' . $table->qr_path) }}" alt="QR {{ $table->name }}" style="width: 140px; height: 140px; object-fit: contain; background: #fff; padding: 0.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    @else
                        <div style="width: 140px; height: 140px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 0.85rem; font-weight: 500; flex-direction: column; gap: 0.4rem;">
                            <i class="ri-qr-code-line" style="font-size: 2.2rem; color: #d1d5db;"></i>
                            <span>Falta generar QR</span>
                        </div>
                    @endif
                </div>

                {{-- Detalles y Estado --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; border-bottom: 1px dashed #e5e7eb; padding-bottom: 0.75rem;">
                    <span style="font-size: 0.78rem; color: #9ca3af;">Creada: {{ $table->created_at?->format('d/m/Y') }}</span>
                    @if ($table->is_active)
                        <span class="k-premium-badge completed" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">Activa</span>
                    @else
                        <span class="k-premium-badge cancelled" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">Inactiva</span>
                    @endif
                </div>

                {{-- Acciones contextuales --}}
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.35rem;">
                    
                    {{-- Ver link público --}}
                    <a href="{{ route('public.menu.table', $table->qr_token) }}" target="_blank" 
                       style="height: 36px; border-radius: 10px; background: #f3f4f6; color: #4b5563; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; text-decoration: none;"
                       title="Abrir menú de la mesa"
                       onmouseover="this.style.background='#e5e7eb'; this.style.color='#111827';"
                       onmouseout="this.style.background='#f3f4f6'; this.style.color='#4b5563';">
                        <i class="ri-external-link-line" style="font-size: 1.05rem;"></i>
                    </a>

                    {{-- Descargar QR --}}
                    <a href="{{ route('tables.download-qr', $table) }}" 
                       style="height: 36px; border-radius: 10px; background: #f3f4f6; color: #4b5563; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; text-decoration: none;"
                       title="Descargar código QR PNG"
                       onmouseover="this.style.background='#e5e7eb'; this.style.color='#111827';"
                       onmouseout="this.style.background='#f3f4f6'; this.style.color='#4b5563';">
                        <i class="ri-download-2-line" style="font-size: 1.05rem;"></i>
                    </a>

                    {{-- Ver flyer imprimible --}}
                    <a href="{{ route('tables.flyer', $table) }}" target="_blank" 
                       style="height: 36px; border-radius: 10px; background: rgba(79, 70, 229, 0.05); color: #4f46e5; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; text-decoration: none;"
                       title="Ver flyer de mesa para imprimir"
                       onmouseover="this.style.background='rgba(79, 70, 229, 0.1)';"
                       onmouseout="this.style.background='rgba(79, 70, 229, 0.05)';">
                        <i class="ri-printer-line" style="font-size: 1.05rem;"></i>
                    </a>

                    {{-- Menú de más acciones (Editar / Eliminar / Regenerar) --}}
                    <div class="k-actions-dropdown" style="width: 100%;">
                        <button type="button" class="k-actions-trigger" 
                                style="width: 100%; height: 36px; border-radius: 10px; background: #181424; color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: none;"
                                title="Más opciones">
                            <i class="ri-settings-3-line" style="font-size: 1.05rem;"></i>
                        </button>

                        <div class="k-actions-menu" style="background: #181424; border-radius: 14px; border: 1px solid rgba(255,255,255,0.06); padding: 6px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; text-align: left; right: 0; min-width: 200px;">
                            <div class="k-actions-group-label" style="color: #8b5cf6; padding: 4px 8px 6px; font-weight: 600;">Opciones QR</div>

                            {{-- Descargar flyer PDF --}}
                            <a href="{{ route('tables.flyer-download', $table) }}" class="k-actions-item" style="color: #e0ddff; text-decoration: none; padding: 6px 10px; border-radius: 8px; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;">
                                <i class="ri-file-pdf-line" style="font-size: 1rem; color: #db2777;"></i>
                                <span>Descargar flyer (PDF)</span>
                            </a>

                            {{-- Regenerar QR --}}
                            <form method="POST" action="{{ route('tables.regenerate-qr', $table) }}" class="k-actions-item k-actions-item--form" style="margin: 0; padding: 0;">
                                @csrf
                                <button type="submit" style="width: 100%; border: none; background: transparent; color: #e0ddff; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; text-align: left;">
                                    <i class="ri-refresh-line" style="font-size: 1rem; color: #f97316;"></i>
                                    <span>Regenerar QR</span>
                                </button>
                            </form>

                            <div class="k-actions-divider" style="height: 1px; background: rgba(255,255,255,0.06); margin: 6px 4px;"></div>
                            <div class="k-actions-group-label" style="color: #8b5cf6; padding: 4px 8px 6px; font-weight: 600;">Mesa</div>

                            {{-- Editar --}}
                            <button type="button" class="k-actions-item js-edit-table"
                                    data-id="{{ $table->id }}" data-name="{{ $table->name }}"
                                    data-code="{{ $table->code }}"
                                    data-active="{{ $table->is_active ? '1' : '0' }}"
                                    data-capacity="{{ $table->capacity }}"
                                    style="width: 100%; border: none; background: transparent; color: #e0ddff; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; text-align: left;">
                                <i class="ri-edit-line" style="font-size: 1rem;"></i>
                                <span>Editar mesa</span>
                            </button>

                            {{-- Eliminar --}}
                            <button type="button"
                                    class="k-actions-item k-actions-item--danger js-delete-table"
                                    data-id="{{ $table->id }}" data-name="{{ $table->name }}"
                                    style="width: 100%; border: none; background: transparent; color: #fca5a5; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; text-align: left; margin-top: 2px;">
                                <i class="ri-delete-bin-6-line" style="font-size: 1rem;"></i>
                                <span>Eliminar mesa</span>
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="k-premium-card" style="grid-column: 1 / -1; padding: 4rem 1rem; text-align: center; color: #6b7280;">
                <div style="font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem;">
                    <i class="ri-layout-grid-line"></i>
                </div>
                <h4 style="font-size: 0.95rem; font-weight: 600; color: #374151; margin-bottom: 0.2rem;">Sin mesas registradas</h4>
                <p style="font-size: 0.8rem; max-width: 300px; margin: 0 auto; line-height: 1.4;">
                    Crea tu primera mesa de salón para habilitar su código QR de comandas autogestionadas.
                </p>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL · NUEVA / EDITAR MESA --}}
<div class="k-modal" id="modalTableForm">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); width: 100%; max-width: 480px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 id="modalTableFormTitle" style="font-size: 1.2rem; font-weight: 700; color: #111827;">Nueva mesa</h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('tables.store') }}" id="formTable">
            @csrf
            <input type="hidden" id="formTableMethod">

            <div class="k-modal__body k-form" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div class="k-field">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Nombre de la mesa *</label>
                    <input type="text" name="name" id="fieldTableName" class="k-input" placeholder="Ej. Mesa 1, Terraza 4, Barra..." required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                </div>

                <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1rem;">
                    <div class="k-field">
                        <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">
                            Código de mesa
                        </label>
                        <input type="text" name="code" id="fieldTableCode" class="k-input" placeholder="Ej. M01" style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                    </div>

                    <div class="k-field">
                        <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Capacidad</label>
                        <input type="number" name="capacity" id="fieldTableCapacity" class="k-input" placeholder="4" min="1" max="999" style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                    </div>
                </div>

                <div class="k-field k-switch" style="display: flex; align-items: center; justify-content: space-between; background: #f9fafb; border-radius: 12px; padding: 0.75rem 1rem; margin: 0.5rem 0 0 0;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin: 0;">Mesa activa y habilitada</label>
                    <label class="k-switch__control" style="position: relative; display: inline-block; width: 44px; height: 24px;">
                        <input type="checkbox" name="is_active" id="fieldTableActive" checked style="opacity: 0; width: 0; height: 0;">
                        <span class="k-switch__slider" style="position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: .3s; border-radius: 34px;"></span>
                        <span class="k-switch__text" style="display:none;"></span>
                    </label>
                </div>
            </div>

            <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
                <button type="button" class="k-btn-secondary js-close-modal" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cancelar</button>
                <button type="submit" class="k-btn-primary" id="btnTableSubmit" style="border-radius: 99px; background: #4f46e5; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">Guardar mesa</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL · CONFIRMAR ELIMINACIÓN --}}
<div class="k-modal" id="modalConfirmDeleteTable">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); max-width: 440px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ri-error-warning-line" style="color: #dc2626; font-size: 1.3rem;"></i>
                <span>Eliminar mesa</span>
            </h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>
        <form method="POST" action="" id="formDeleteTable">
            @csrf
            @method('DELETE')
            <div class="k-modal__body" style="padding: 1.5rem; font-size: 0.9rem; color: #4b5563; line-height: 1.5;">
                <p id="deleteTableText" style="margin: 0 0 0.5rem 0; font-weight: 600; color: #1f2937;">¿Estás seguro de que deseas eliminar esta mesa?</p>
                <p style="margin: 0 0 0.75rem 0;">Esta acción es irreversible y el código QR impreso dejará de ser válido inmediatamente.</p>
                <div style="background: rgba(249, 115, 22, 0.05); border: 1px solid rgba(249, 115, 22, 0.2); border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.8rem; color: #ea580c; display: flex; gap: 0.5rem; align-items: flex-start;">
                    <i class="ri-alert-line" style="font-size: 1rem; margin-top: 0.1rem;"></i>
                    <span>Si esta mesa ya cuenta con pedidos previos registrados, te sugerimos únicamente marcarla como <strong>Inactiva</strong> desde su edición.</span>
                </div>
            </div>
            <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
                <button type="button" class="k-btn-secondary js-close-modal" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cancelar</button>
                <button type="submit" class="k-btn-primary" style="border-radius: 99px; background: #dc2626; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">Eliminar</button>
            </div>
        </form>
    </div>
</div>

{{-- Config JS para URLs dinámicas --}}
<div id="kTablesConfig" data-update-url-template="{{ route('tables.update', ['table' => '__ID__']) }}"
    data-destroy-url-template="{{ route('tables.destroy', ['table' => '__ID__']) }}">
</div>
@endsection

@push('scripts')
    {{-- CSS Inline de switches premium y loaders --}}
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
    <script>
        // Buscador interactivo local rápido sobre tarjetas
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('kPosSearch');
            if (search) {
                search.addEventListener('input', () => {
                    const q = search.value.trim().toLowerCase();
                    const cards = document.querySelectorAll('.js-table-card-item');
                    cards.forEach(card => {
                        const name = card.dataset.name || '';
                        const code = card.dataset.code || '';
                        if (!q || name.includes(q) || code.includes(q)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
    <script src="{{ asset('js/business-tables.js') }}"></script>
@endpush
