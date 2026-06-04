<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublicOrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id'          => ['required', 'integer', 'exists:businesses,id'],
            'table_id'             => ['nullable', 'integer', 'exists:tables,id'],
            'customer_name'        => ['nullable', 'string', 'max:100'],
            'customer_phone'       => ['nullable', 'string', 'max:30'],
            'notes'                => ['nullable', 'string', 'max:500'],

            'subtotal'             => ['nullable', 'numeric', 'min:0'],
            'discount'             => ['nullable', 'numeric', 'min:0'],
            'total'                => ['nullable', 'numeric', 'min:0'],

            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'integer'],
            'items.*.name'         => ['required', 'string', 'max:150'],
            'items.*.qty'          => ['required', 'integer', 'min:1'],
            'items.*.unit_price'   => ['required', 'numeric', 'min:0'],
            'items.*.total'        => ['required', 'numeric', 'min:0'],
            'items.*.notes'        => ['nullable', 'string', 'max:300'],
        ]);

        try {
            return DB::transaction(function () use ($data) {
                $tableId = $data['table_id'] ?? null;

                $computedSubtotal = collect($data['items'])->sum('total');

                $subtotal = $data['subtotal'] ?? $computedSubtotal;
                $discount = $data['discount'] ?? 0;
                $total    = $data['total'] ?? ($subtotal - $discount);

                $order = Order::create([
                    'business_id'    => $data['business_id'],
                    'table_id'       => $tableId,
                    'user_id'        => null,
                    'status'         => 'pending',
                    'source'         => 'qr',
                    'order_type'     => null, // desde QR por ahora
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'total'          => $total,
                    'customer_name'  => $data['customer_name'] ?? null,
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'notes'          => $data['notes'] ?? null,
                    'placed_at'      => now(),
                ]);

                foreach ($data['items'] as $item) {
                    OrderItem::create([
                        'business_id' => $data['business_id'],
                        'order_id'    => $order->id,
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['qty'],
                        'unit_price'  => $item['unit_price'],
                        'total_price' => $item['total'],
                        'notes'       => $item['notes'] ?? null,
                    ]);
                }

                return response()->json([
                    'ok'        => true,
                    'order_id'  => $order->id,
                    'subtotal'  => (float) $order->subtotal,
                    'discount'  => (float) $order->discount,
                    'total'     => (float) $order->total,
                ]);
            });
        } catch (ValidationException $e) {
            throw $e; // Laravel ya responde 422 con errores
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo registrar el pedido. Intenta de nuevo.',
            ], 500);
        }
    }
}
