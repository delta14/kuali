@csrf

<div class="k-field">
    <label class="k-label">Nombre del comercio</label>
    <input type="text"
           name="name"
           class="k-input"
           value="{{ old('name', $business->name) }}"
           required>
    @error('name')
        <small style="color:#fca5a5;">{{ $message }}</small>
    @enderror
</div>

<div class="k-field">
    <label class="k-label">
        Slug (URL amigable)
        <span style="font-weight:400; color:var(--kuali-text-muted);">(opcional)</span>
    </label>
    <input type="text"
           name="slug"
           class="k-input"
           value="{{ old('slug', $business->slug) }}"
           placeholder="ej: pizzeria-mi-barrio">
    @error('slug')
        <small style="color:#fca5a5;">{{ $message }}</small>
    @enderror
</div>

<button type="submit" class="k-main-cta">
    {{ $submitLabel ?? 'Guardar' }}
</button>
