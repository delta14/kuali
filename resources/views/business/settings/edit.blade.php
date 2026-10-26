{{-- resources/views/business/settings/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Mi Negocio')

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
            <h1 style="font-size: 2rem;">Mi Negocio</h1>
            <p class="k-premium-subtitle">
                Administra los datos de contacto, el logo y el fondo del flyer de tu negocio.
            </p>
        </div>
    </header>

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="k-premium-card bs-settings-card">
        @csrf
        @method('PUT')

        <div class="k-field">
            <label class="k-label">Nombre del comercio</label>
            <input type="text" name="name" class="k-input" value="{{ old('name', $business->name) }}" required>
        </div>

        <div class="k-grid-2">
            <div class="k-field">
                <label class="k-label">Correo de contacto</label>
                <input type="email" name="email_contact" class="k-input" value="{{ old('email_contact', $business->email_contact) }}">
            </div>

            <div class="k-field">
                <label class="k-label">Teléfono</label>
                <input type="text" name="phone" class="k-input" value="{{ old('phone', $business->phone) }}">
            </div>
        </div>

        <div class="k-field">
            <label class="k-label">Dirección</label>
            <input type="text" name="address" class="k-input" value="{{ old('address', $business->address) }}">
        </div>

        <div class="k-field">
            <label class="k-label">Link público de tu menú</label>
            <input type="text" class="k-input" value="{{ route('public.menu.splash', $business->slug) }}" readonly disabled>
            <div class="k-field-message">
                Este link ya está impreso en los QR de tus mesas, por lo que no es autoadministrable. Si necesitas cambiarlo, contacta a soporte de Kuali.
            </div>
        </div>

        <div class="k-field">
            <label class="k-label">Logo del comercio</label>
            <div class="bs-settings-upload">
                <img id="bs_logo_preview" src="{{ $business->logo_path ? asset('storage/' . $business->logo_path) : '' }}"
                    alt="Logo actual" class="bs-settings-preview" style="{{ $business->logo_path ? '' : 'display:none;' }}">
                <input type="file" name="logo" class="k-input" accept="image/*" id="bs_logo_input">
            </div>
            <div class="k-field-message">
                Se usa en tu panel y en el flyer/QR para imprimir.
            </div>
        </div>

        <div class="k-field">
            <label class="k-label">Imagen de fondo del flyer</label>
            <div class="bs-settings-upload">
                <img id="bs_flyerbg_preview" src="{{ $business->flyer_bg_path ? asset('storage/' . $business->flyer_bg_path) : '' }}"
                    alt="Fondo actual" class="bs-settings-preview" style="{{ $business->flyer_bg_path ? '' : 'display:none;' }}">
                <input type="file" name="flyer_bg" class="k-input" accept="image/*" id="bs_flyerbg_input">
            </div>
            <div class="k-field-message">
                Fondo fijo que aparece detrás del QR en el flyer de tus mesas. Si no subes uno propio, se usa un fondo genérico.
            </div>
        </div>

        <div class="bs-settings-footer">
            <button type="submit" class="k-btn-primary">Guardar cambios</button>
        </div>
    </form>
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

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/business-settings.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/business-settings.js') }}"></script>
@endpush
