@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="k-main-inner k-premium-container">

    {{-- Errores de validación --}}
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
            <h1 style="font-size: 2rem;">Productos</h1>
            <p class="k-premium-subtitle">
                Administra los platillos, bebidas, postres y servicios de tu menú digital.
            </p>
        </div>

        <div class="k-premium-header-actions">
            <button type="button" class="k-main-cta" id="btnOpenCreateProduct" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.3rem;">
                <i class="ri-add-line" style="font-size: 1.1rem;"></i>
                <span>Nuevo Producto</span>
            </button>
        </div>
    </header>

    {{-- CARD TABLA --}}
    <div class="k-premium-card k-is-loading" id="productsCard" style="padding: 1.5rem;">
        
        <div class="k-table-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between;">
            <div class="k-premium-kpi-trend up" style="background: rgba(79, 70, 229, 0.08); color: #4f46e5; font-size: 0.82rem; padding: 0.3rem 0.8rem;">
                Total: {{ $products->total() }} productos
            </div>
        </div>

        {{-- SKELETON / SHIMMER --}}
        <div class="k-skeleton k-skeleton--table" id="productsSkeleton" style="display: none;">
            {{-- Encabezado fake --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
                <div class="k-skeleton-line k-skeleton-line--md"></div>
                <div class="k-skeleton-line k-skeleton-line--sm"></div>
            </div>

            {{-- 4 filas fake --}}
            @for ($i = 0; $i < 4; $i++)
                <div class="k-skeleton-row" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: center; border-bottom: 1px solid #f9fafb; padding-bottom: 0.75rem;">
                    <div style="flex: 1;"><div class="k-skeleton-line k-skeleton-line--lg" style="height: 18px; background: #e5e7eb; border-radius: 4px; width: 60%;"></div></div>
                    <div style="width: 48px;"><div class="k-skeleton-avatar" style="width: 42px; height: 42px; border-radius: 10px; background: #e5e7eb;"></div></div>
                    <div style="width: 120px;"><div class="k-skeleton-line k-skeleton-line--md" style="height: 14px; background: #e5e7eb; border-radius: 4px;"></div></div>
                    <div style="width: 80px;"><div class="k-skeleton-line k-skeleton-line--sm" style="height: 14px; background: #e5e7eb; border-radius: 4px; width: 50px;"></div></div>
                    <div style="width: 80px;"><div class="k-skeleton-pill" style="height: 20px; background: #e5e7eb; border-radius: 99px; width: 60px;"></div></div>
                    <div style="width: 100px;"><div class="k-skeleton-line k-skeleton-line--sm" style="height: 14px; background: #e5e7eb; border-radius: 4px; width: 80px;"></div></div>
                    <div style="width: 50px;"><div class="k-skeleton-dot" style="width: 24px; height: 24px; border-radius: 50%; background: #e5e7eb; margin-inline: auto;"></div></div>
                </div>
            @endfor
        </div>

        {{-- TABLA REAL --}}
        <div class="k-table-wrap">
            <table class="k-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #f3f4f6; color: #4b5563; font-size: 0.8rem; font-weight: 600; text-align: left;">
                        <th style="padding: 0.85rem 1rem; width: 48px;">Imagen</th>
                        <th style="padding: 0.85rem 1rem;">Producto</th>
                        <th style="padding: 0.85rem 1rem; width: 150px;">Categoría</th>
                        <th style="padding: 0.85rem 1rem; width: 100px;">Precio</th>
                        <th style="padding: 0.85rem 1rem; width: 100px;">Estado</th>
                        <th style="padding: 0.85rem 1rem; width: 120px;">Creado</th>
                        <th style="width: 60px; padding: 0.85rem 1rem; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr style="border-bottom: 1px solid #f9fafb; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(79,70,229,0.01)'" onmouseout="this.style.background='none'">
                            
                            {{-- Imagen del producto --}}
                            <td data-label="Imagen" style="padding: 0.85rem 1rem;">
                                @if ($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}"
                                        class="k-premium-dish-img" style="width:44px; height:44px; border-radius:12px; object-fit:cover; display: block; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                                @else
                                    <div class="k-premium-dish-fallback" style="width:44px; height:44px; border-radius:12px; background: rgba(99, 102, 241, 0.05); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; font-weight: 600;">
                                        {{ mb_substr($product->name, 0, 1) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Nombre del producto --}}
                            <td data-label="Producto" style="padding: 0.85rem 1rem; font-weight: 600; color: #111827; font-size: 0.9rem;">
                                <div style="display: flex; flex-direction: column;">
                                    <span>{{ $product->name }}</span>
                                    @if($product->description)
                                        <span style="font-size: 0.75rem; color: #6b7280; font-weight: 400; max-width: 320px; line-height: 1.35; margin-top: 0.2rem;">
                                            {{ Str::limit($product->description, 65) }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Categoría --}}
                            <td data-label="Categoría" style="padding: 0.85rem 1rem; color: #4f46e5; font-weight: 500;">
                                <div style="display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(79, 70, 229, 0.05); border-radius: 99px; padding: 0.2rem 0.65rem; font-size: 0.78rem;">
                                    <span style="width: 6px; height: 6px; background: #4f46e5; border-radius: 50%;"></span>
                                    <span>{{ $product->category?->name ?? 'Sin Categoría' }}</span>
                                </div>
                            </td>

                            {{-- Precio --}}
                            <td data-label="Precio" style="padding: 0.85rem 1rem; font-weight: 700; color: #111827; font-size: 0.92rem;">
                                ${{ number_format($product->price, 2) }}
                            </td>

                            {{-- Estado --}}
                            <td data-label="Activo" style="padding: 0.85rem 1rem;">
                                @if ($product->is_active)
                                    <span class="k-premium-badge completed" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">Activo</span>
                                @else
                                    <span class="k-premium-badge cancelled" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">Inactivo</span>
                                @endif
                            </td>

                            {{-- Creado --}}
                            <td data-label="Creado" style="padding: 0.85rem 1rem; color: #6b7280; font-size: 0.82rem;">
                                <div style="display: flex; align-items: center; gap: 0.35rem;">
                                    <i class="ri-time-line" style="font-size: 0.9rem;"></i>
                                    <span>{{ $product->created_at?->format('d/m/Y') }}</span>
                                </div>
                            </td>

                            {{-- Acciones --}}
                            <td data-label="Acciones" style="padding: 0.85rem 1rem; text-align: right;" class="k-actions-cell">
                                <div class="k-actions-dropdown" style="justify-content: flex-end;">
                                    <button type="button" class="k-actions-trigger" aria-haspopup="true"
                                        aria-expanded="false" style="width: 34px; height: 34px; background: #f3f4f6; border-radius: 50%; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; color: #4b5563; transition: all 0.2s ease;">
                                        <i class="ri-more-2-fill" style="font-size: 1.05rem;"></i>
                                    </button>

                                    <div class="k-actions-menu" style="background: #181424; border-radius: 14px; border: 1px solid rgba(255,255,255,0.06); padding: 6px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; text-align: left;">
                                        <div class="k-actions-group-label" style="color: #8b5cf6; padding: 4px 8px 6px; font-weight: 600;">Opciones</div>

                                        {{-- Editar --}}
                                        <button type="button" class="k-actions-item js-edit-product"
                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            data-category-id="{{ $product->category_id }}"
                                            data-price="{{ $product->price }}"
                                            data-description="{{ $product->description }}"
                                            data-active="{{ $product->is_active ? '1' : '0' }}"
                                            data-image-url="{{ $product->image_path ? asset('storage/' . $product->image_path) : '' }}"
                                            style="width: 100%; border: none; background: transparent; color: #e0ddff; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; text-align: left;">
                                            <i class="ri-edit-line" style="font-size: 1rem;"></i>
                                            <span>Editar producto</span>
                                        </button>

                                        {{-- Eliminar --}}
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                            class="k-actions-item k-actions-item--form js-delete-product-form" novalidate style="margin: 0; padding: 0; width: 100%;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="width: 100%; border: none; background: transparent; color: #fca5a5; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; text-align: left; margin-top: 2px;">
                                                <i class="ri-delete-bin-6-line" style="font-size: 1rem;"></i>
                                                <span>Eliminar producto</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 4rem 1rem; text-align: center; color: #6b7280;">
                                <div style="font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem;">
                                    <i class="ri-restaurant-line"></i>
                                </div>
                                <h4 style="font-size: 0.95rem; font-weight: 600; color: #374151; margin-bottom: 0.2rem;">Sin productos registrados</h4>
                                <p style="font-size: 0.8rem; max-width: 300px; margin: 0 auto; line-height: 1.4;">
                                    Registra tus alimentos, refrescos o postres para que aparezcan en tu menú digital.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div style="margin-top:1.5rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL · NUEVO PRODUCTO --}}
<div class="k-modal" id="modalCreateProduct">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); width: 100%; max-width: 520px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827;">Nuevo producto</h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>

        <form id="formCreateProduct" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="k-modal__body k-form" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                
                {{-- Nombre --}}
                <div class="k-field">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Nombre del producto *</label>
                    <input type="text" name="name" class="k-input" placeholder="Ej. Capuchino grande, Hamburguesa especial..." required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                </div>

                {{-- Categoría y Precio en Fila --}}
                <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1rem;">
                    
                    {{-- Categoría --}}
                    <div class="k-field">
                        <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Categoría *</label>
                        <select name="category_id" class="k-input" required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s; background: #ffffff;" onfocus="this.style.borderColor='#4f46e5'">
                            <option value="">Seleccionar…</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Precio --}}
                    <div class="k-field">
                        <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Precio ($) *</label>
                        <input type="number" step="0.01" min="0" name="price" class="k-input" placeholder="0.00" required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="k-field">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Descripción</label>
                    <textarea name="description" class="k-input" rows="2" placeholder="Fórmula, ingredientes clave o notas sabrosas..." style="border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s; font-family: inherit; resize: vertical;" onfocus="this.style.borderColor='#4f46e5'"></textarea>
                </div>

                {{-- Imagen --}}
                <div class="k-field">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Foto del platillo</label>
                    <input type="file" name="image" class="k-input" accept="image/*" style="height: auto; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.45rem; width: 100%; font-size: 0.82rem; outline: none; background: #ffffff;">
                    <div class="k-field-message" style="font-size: 0.75rem; color: #6b7280; margin-top: 0.3rem;">
                        JPG/PNG recomendado de relación cuadrada hasta 2MB.
                    </div>
                </div>

                {{-- Activo --}}
                <div class="k-field k-switch" style="display: flex; align-items: center; justify-content: space-between; background: #f9fafb; border-radius: 12px; padding: 0.75rem 1rem; margin: 0.5rem 0 0 0;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin: 0;">Publicar de inmediato</label>
                    <label class="k-switch__control" style="position: relative; display: inline-block; width: 44px; height: 24px;">
                        <input type="checkbox" name="is_active" checked style="opacity: 0; width: 0; height: 0;">
                        <span class="k-switch__slider" style="position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: .3s; border-radius: 34px;"></span>
                        <span class="k-switch__text" style="display:none;"></span>
                    </label>
                </div>
            </div>

            <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
                <button type="button" class="k-btn-secondary js-close-modal" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cancelar</button>
                <button type="submit" class="k-btn-primary" style="border-radius: 99px; background: #4f46e5; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">Guardar producto</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL · EDITAR PRODUCTO --}}
<div class="k-modal" id="modalEditProduct">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); width: 100%; max-width: 520px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827;">Editar producto</h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>

        <form method="POST" action="" id="formEditProduct" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            <div class="k-modal__body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                
                {{-- Nombre --}}
                <div class="k-field">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Nombre del producto</label>
                    <input type="text" name="name" class="k-input" required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                </div>

                {{-- Categoría y precio en fila --}}
                <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1rem;">
                    
                    {{-- Categoría --}}
                    <div class="k-field">
                        <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Categoría</label>
                        <select name="category_id" class="k-input" required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s; background: #ffffff;" onfocus="this.style.borderColor='#4f46e5'">
                            <option value="">Seleccionar…</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Precio --}}
                    <div class="k-field">
                        <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Precio ($)</label>
                        <input type="number" name="price" class="k-input" step="0.01" min="0" required style="height: 42px; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4f46e5'">
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="k-field">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Descripción</label>
                    <textarea name="description" class="k-input" rows="2" style="border-radius: 12px; border: 1px solid #d1d5db; padding: 0.5rem 0.8rem; width: 100%; font-size: 0.88rem; outline: none; transition: border-color 0.2s; font-family: inherit; resize: vertical;" onfocus="this.style.borderColor='#4f46e5'"></textarea>
                </div>

                {{-- Imagen --}}
                <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center;">
                    <div class="k-field">
                        <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.4rem;">Foto del platillo</label>
                        <input type="file" name="image" class="k-input" accept="image/*" style="height: auto; border-radius: 12px; border: 1px solid #d1d5db; padding: 0.45rem; width: 100%; font-size: 0.82rem; outline: none; background: #ffffff;">
                        <div class="k-field-message" style="font-size: 0.75rem; color: #6b7280; margin-top: 0.3rem;">
                            Si cargas una nueva foto, se reemplazará la actual.
                        </div>
                    </div>
                    
                    {{-- Miniatura actual --}}
                    <div class="k-product-preview" id="productImagePreviewWrapper" style="padding-top: 1rem;">
                        <img id="productImagePreview" src="" alt="Vista previa"
                            style="width: 56px; height: 56px; border-radius: 12px; object-fit: cover; border: 1px solid #e5e7eb; display: none; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    </div>
                </div>

                {{-- Activo --}}
                <div class="k-field k-switch" style="display: flex; align-items: center; justify-content: space-between; background: #f9fafb; border-radius: 12px; padding: 0.75rem 1rem; margin: 0.5rem 0 0 0;">
                    <label class="k-label" style="font-weight: 600; color: #374151; font-size: 0.85rem; margin: 0;">Visible en menú público</label>
                    <label class="k-switch__control" style="position: relative; display: inline-block; width: 44px; height: 24px;">
                        <input type="checkbox" name="is_active" id="edit_product_is_active" style="opacity: 0; width: 0; height: 0;">
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

{{-- MODAL · CONFIRMAR ELIMINACIÓN --}}
<div class="k-modal" id="modalConfirmDeleteProduct">
    <div class="k-modal__backdrop js-close-modal" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);"></div>
    <div class="k-modal__dialog" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 1px solid rgba(229, 231, 235, 0.5); max-width: 440px;">
        <div class="k-modal__header" style="border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ri-error-warning-line" style="color: #dc2626; font-size: 1.3rem;"></i>
                <span>Eliminar producto</span>
            </h2>
            <button type="button" class="k-modal__close js-close-modal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: none; cursor: pointer;">
                <i class="ri-close-line" style="font-size: 1.1rem; color: #4b5563;"></i>
            </button>
        </div>
        <div class="k-modal__body" style="padding: 1.5rem; font-size: 0.9rem; color: #4b5563; line-height: 1.5;">
            <p style="margin: 0 0 0.5rem 0; font-weight: 600; color: #1f2937;">¿Estás seguro de que deseas eliminar este producto?</p>
            <p style="margin: 0;">Esta acción es irreversible y el platillo desaparecerá por completo de tu menú digital público.</p>
        </div>
        <div class="k-modal__footer" style="border-top: 1px solid #f3f4f6; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; background: #fafafa; border-radius: 0 0 24px 24px;">
            <button type="button" class="k-btn-secondary js-cancel-delete-product" style="border-radius: 99px; border: 1px solid #e5e7eb; background: #ffffff; color: #4b5563; font-weight: 500; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer;">Cancelar</button>
            <button type="button" class="k-btn-primary js-confirm-delete-product" style="border-radius: 99px; background: #dc2626; border: none; color: #ffffff; font-weight: 600; padding: 0.5rem 1.25rem; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">Eliminar</button>
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

        /* Overrides de Paginador Laravel (Tailwind a Vanilla CSS Premium) */
        nav[role="navigation"] {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 0.5rem 0;
            font-family: 'Poppins', sans-serif;
        }
        nav[role="navigation"] svg {
            width: 18px;
            height: 18px;
        }
        nav[role="navigation"] .hidden {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        nav[role="navigation"] .sm\:flex-1 {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        nav[role="navigation"] a, 
        nav[role="navigation"] span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.4rem;
            margin: 0 2px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #4b5563;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        nav[role="navigation"] a:hover {
            border-color: #4f46e5;
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.02);
            transform: translateY(-1px);
        }
        nav[role="navigation"] span[aria-current="page"] {
            background: #4f46e5 !important;
            color: #ffffff !important;
            border-color: #4f46e5 !important;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15);
        }
        nav[role="navigation"] span.cursor-default {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <script src="{{ asset('js/business-products.js') }}"></script>
@endpush
