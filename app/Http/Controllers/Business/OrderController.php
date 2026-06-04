<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Helper igual que en TableController
    protected function currentBusiness(Request $request): Business
    {
        $user = $request->user();

        $business = $user->businesses()
            ->wherePivot('is_active', true)
            ->orderBy('business_user.created_at')
            ->first();

        if (! $business) {
            abort(403, 'No tienes un comercio asignado o activo.');
        }

        return $business;
    }

    public function index(Request $request)
    {
        $business = $this->currentBusiness($request);

        $status = $request->query('status', 'pending'); // pending|in_progress|served|cancelled
        $date = $request->query('date', now()->toDateString());

        $ordersQuery = Order::where('business_id', $business->id)
            ->with('table')
            ->latest('placed_at');

        if ($status) {
            $ordersQuery->where('status', $status);
        }

        if ($date) {
            $ordersQuery->whereDate('created_at', $date);
            // o ->whereDate('placed_at', $date);
        }

        $orders = $ordersQuery->paginate(15);

        return view('business.orders.index', compact('business', 'orders', 'status', 'date'));
    }

    public function show(Request $request, Order $order)
    {
        $business = $this->currentBusiness($request);
        abort_unless($order->business_id === $business->id, 403);

        $order->load([
            'table',
            'items.product',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $order->id,
                'folio' => '#'.$order->id,
                'table' => $order->table?->name ?? 'Sin mesa',
                'status' => $order->status,
                'status_label' => ucfirst($order->status),
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount,
                'total' => (float) $order->total,
                'placed_at' => optional($order->placed_at)->format('d/m/Y H:i'),
                'items' => $order->items->map(function ($item) {
                    $name = $item->name ?: optional($item->product)->name;

                    $unit = (float) $item->unit_price;
                    $qty = (int) $item->qty;
                    $total = $item->total !== null
                        ? (float) $item->total
                        : $unit * $qty;

                    return [
                        'id' => $item->id,
                        'product_name' => $name,
                        'notes' => $item->notes,
                        'qty' => $qty,
                        'unit_price' => $unit,
                        'total' => $total,
                    ];
                })->values(),
            ]);
        }

        return redirect()->route('orders.index');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $business = $request->user()->currentBusiness;
        abort_unless($business && $order->business_id === $business->business_id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:pending,in_progress,ready,delivered,cancelled'],
        ]);

        $order->status = $data['status'];

        if ($order->status === 'delivered' && ! $order->closed_at) {
            $order->closed_at = now();
        }

        $order->save();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Estado del pedido actualizado correctamente.');
    }

    public function markAsCompleted(Order $order)
    {
        if ($order->status === 'completed') {
            if (request()->wantsJson()) {
                return response()->json(['ok' => true, 'already' => true]);
            }

            return back()->with('success', 'Este pedido ya estaba completado.');
        }

        $order->status = 'completed';
        $order->completed_at = now(); // si tienes esta columna
        $order->save();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Pedido marcado como completado.');
    }

    public function complete(Request $request, Order $order)
    {
        // Si quieres, puedes forzar que sea por AJAX
        // if (! $request->ajax()) {
        //     abort(404);
        // }

        // Validar estado actual
        if ($order->status === 'completed') {
            return response()->json([
                'ok' => false,
                'message' => 'Este pedido ya está marcado como completado.',
            ], 422);
        }

        // Marcar como completado
        $order->status = 'completed';
        $order->closed_at = now();
        $order->save();

        $order->refresh();

        // ===== Armar link de WhatsApp si hay teléfono =====
        $whatsappUrl = null;

        if ($order->customer_phone) {
            // Limpiamos a sólo dígitos
            $phone = preg_replace('/\D+/', '', $order->customer_phone);

            // Si no trae lada, asumimos 52 (México)
            if (! Str::startsWith($phone, '52')) {
                $phone = '52'.$phone;
            }

            $businessName = $order->business->name ?? 'nuestro negocio';
            $customerName = $order->customer_name ?: 'cliente';

            $text = urlencode(
                "Hola {$customerName}, tu pedido #{$order->id} en {$businessName} ya está listo para recoger. ¡Gracias! 😊"
            );

            $whatsappUrl = "https://wa.me/{$phone}?text={$text}";
        }

        // ===== Respuesta esperada por business-orders.js =====
        return response()->json([
            'ok' => true,
            'message' => 'El pedido se marcó como completado.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                // Si tienes un accessor getStatusLabelAttribute úsalo,
                // si no, usamos ucfirst:
                'status_label' => $order->status_label ?? ucfirst($order->status),
            ],
            'whatsapp_url' => $whatsappUrl,
        ]);
    }

    public function createPos(Request $request)
    {
        $business = $this->currentBusiness($request);

        // Traer categorías con productos del negocio
        $categories = Category::with(['products' => function ($q) use ($business) {
            $q->where('business_id', $business->id)
                ->where('is_active', true);
        }])
            ->where('business_id', $business->id)
            ->orderBy('name')
            ->get();

        return view('business.orders.pos', [
            'business' => $business,
            'categories' => $categories,
        ]);
    }

    public function storePos(Request $request)
    {
        $user = $request->user();
        $business = $this->currentBusiness($request); // 👈 usa tu helper
    
        $data = $request->validate([
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'integer', 'exists:products,id'],
            'items.*.qty'         => ['required', 'integer', 'min:1'],
            'items.*.price'       => ['required', 'numeric', 'min:0'],
            'items.*.note'        => ['nullable', 'string', 'max:255'],
            'table_name'          => ['nullable', 'string', 'max:50'],
            'order_type'          => ['nullable', 'in:dine_in,take_away'],
            'notes'               => ['nullable', 'string', 'max:500'],
        ]);
    
        try {
            DB::beginTransaction();
    
            $subtotal = collect($data['items'])->sum(fn($item) =>
                $item['price'] * $item['qty']
            );
    
            $order = Order::create([
                'business_id' => $business->id,
                'table_name'  => $data['table_name'] ?? 'Mostrador',
                'status'      => 'pending',
                'status_label'=> 'En proceso',
                'source'      => 'counter',
                'order_type'  => $data['order_type'] ?? null,
                'notes'       => $data['notes'] ?? null,
                'subtotal'    => $subtotal,
                'total'       => $subtotal,
                'placed_at'   => now(),
            ]);
    
            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'] ?? null,
                    'quantity'     => $item['qty'],
                    'unit_price'   => $item['price'],
                    'total_price'  => $item['price'] * $item['qty'],
                    'notes'        => $item['note'] ?? null,
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'ok'      => true,
                'message' => 'Pedido creado en mostrador correctamente.',
                'order'   => [
                    'id'           => $order->id,
                    'status'       => $order->status,
                    'status_label' => $order->status_label,
                ],
                'redirect_url' => route('orders.index', ['status' => 'pending']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
    
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo crear el pedido en mostrador.',
            ], 500);
        }
    }

    public function checkNew(Request $request)
    {
        try {
            $business = $this->currentBusiness($request);
            $lastId = (int) $request->query('last_id', 0);

            if ($lastId === 0) {
                $latestOrder = Order::where('business_id', $business->id)
                    ->latest('id')
                    ->first();

                return response()->json([
                    'ok' => true,
                    'has_new' => false,
                    'last_id' => $latestOrder ? $latestOrder->id : 0,
                    'orders' => []
                ]);
            }

            $newOrders = Order::where('business_id', $business->id)
                ->where('source', 'qr')
                ->where('status', 'pending')
                ->where('id', '>', $lastId)
                ->with('table')
                ->orderBy('id', 'asc')
                ->get();

            $formattedOrders = $newOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name ?: 'Cliente',
                    'table_name' => $order->table?->name ?? 'Sin mesa',
                    'total' => (float) $order->total,
                    'placed_at_human' => $order->placed_at ? $order->placed_at->diffForHumans() : 'hace un momento',
                ];
            });

            $nextLastId = $newOrders->isNotEmpty() ? $newOrders->last()->id : $lastId;

            return response()->json([
                'ok' => true,
                'has_new' => $newOrders->isNotEmpty(),
                'last_id' => $nextLastId,
                'orders' => $formattedOrders
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'ok' => false,
                'message' => 'Error al verificar nuevos pedidos.',
            ], 500);
        }
    }
    
}
