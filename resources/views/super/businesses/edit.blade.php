@extends('layouts.app')

@section('title', 'Editar comercio')

@section('content')
<div class="k-main-inner">
    <div class="k-main-header">
        <div>
            <h1 class="k-main-title">Editar comercio</h1>
            <p class="k-main-subtitle">
                Actualiza los datos del negocio.
            </p>
        </div>
    </div>

    <div class="k-section">
        <div class="k-recent-card" style="max-width:520px; flex-direction:column;">
            <form action="{{ route('super.businesses.update', $business) }}" method="POST">
                @method('PUT')
                @include('super.businesses._form', ['submitLabel' => 'Guardar cambios'])
            </form>
        </div>
    </div>
</div>
@endsection
