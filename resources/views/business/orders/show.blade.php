@extends('layouts.app')

@section('title', 'Pedido #'.$order->id)

@section('content')
<div class="k-main-inner">

    @if(session('success'))
        <div class="k-alert k-alert--success">
            {{ session('success') }}
        </div>
    @endif

    <div class="k-page-toolbar">
        <div>
            <h1 class="k-page-toolbar-title">Pedido #{{ $order->id }}</h1>
            <p class="k-page-toolbar-subtitle">
                {{ $business->name }} — 
                {{ optional($order->placed_at ?? $order->created_at)->format('d/m/Y H:i') }}
            </p>
        </div>

        <a href="{{ route('orders.index') }}" class="k-btn-secondary">
            ← Volver a pedidos
        </a>
    </div>

    <div class="k-grid-2">
        {{-- Detalle principal --}}
        <div class="k-table-card">
            <h2 style="margin-top:0;">Resumen del pedido</h2>

            <p><strong>Mesa:</strong> {{ $order->table?->name ?? 'Sin mesa' }}</p>

            @if($order->customer_name)
                <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
            @endif
            @if($order->customer_phone)
                <p><strong>Teléfono:</strong> {{ $order->customer_phone }}</p>
            @endif
            @if($order->notes)
                <p><strong>Notas:</strong> {{ $order->notes }}</p>
            @endif

            <p><strong>Subtotal:</strong> ${{ number_format($order->subtotal,2) }}</p>
            <p><strong>Descuento:</strong> ${{ number_format($order->discount,2) }}</p>
            <p><strong>Total:</strong> ${{ number_format($order->total,2) }}</p>
        </div>

        {{-- Estado / acciones rápidas --}}
        <div class="k-table-card">
            <h2 style="margin-top:0;">Estado</h2>

            <form method="POST" action="{{ route('orders.update-status', $order) }}" class="k-form">
                @csrf
                @method('PATCH')

                <div class="k-field">
                    <label class="k-label">Estado del pedido</label>
                    <select name="status" class="k-input">
                        <option value="pending"    {{ $order->status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="in_progress"{{ $order->status === 'in_progress' ? 'selected' : '' }}>En preparación</option>
                        <option value="served"     {{ $order->status === 'served' ? 'selected' : '' }}>Servido</option>
                        <option value="cancelled"  {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <button type="submit" class="k-btn-primary">
                    Guardar estado
                </button>
            </form>
        </div>
    </div>

    {{-- Items --}}
    <div class="k-table-card" style="margin-top:1.5rem;">
        <h2 style="margin-top:0;">Productos del pedido</h2>

        <table class="k-table">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Notas</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td data-label="Producto">
                        {{ $item->name }}
                    </td>
                    <td data-label="Notas">
                        {{ $item->notes ?: '—' }}
                    </td>
                    <td data-label="Cantidad">
                        {{ $item->qty }}
                    </td>
                    <td data-label="Precio">
                        ${{ number_format($item->unit_price, 2) }}
                    </td>
                    <td data-label="Total">
                        ${{ number_format($item->total, 2) }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
