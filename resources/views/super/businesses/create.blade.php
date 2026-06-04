@extends('layouts.app')

@section('title', 'Nuevo comercio')

@section('content')
<div class="k-main-inner">
    <div class="k-main-header">
        <div>
            <h1 class="k-main-title">Nuevo comercio</h1>
            <p class="k-main-subtitle">
                Registra un nuevo negocio para usar Kuali.
            </p>
        </div>
    </div>

    <div class="k-section">
        <div class="k-recent-card" style="max-width:520px; flex-direction:column;">
            <form action="{{ route('super.businesses.store') }}" method="POST">
                @include('super.businesses._form', ['submitLabel' => 'Crear comercio'])
            </form>
        </div>
    </div>
</div>
@endsection
