<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicOrderController extends Controller
{
    /**
     * Minutos de vida del scan_token generado en PublicMenuController al
     * renderizar el menú. Pasado este tiempo, se pide re-escanear el QR
     * aunque el link siga siendo técnicamente válido.
     */
    public const SCAN_EXPIRY_MINUTES = 120;

    public function store(Request $request)
    {
        // business_id se valida primero para poder escopar las reglas de abajo a ESE negocio.
        $businessId = (int) $request->input('business_id');

        $data = $request->validate([
            'business_id'    => ['required', 'integer', 'exists:businesses,id'],
            'table_id'       => ['nullable', 'integer', Rule::exists('tables', 'id')->where('business_id', $businessId)],
            'scan_token'     => ['required', 'string'],
            'customer_name'  => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'notes'          => ['nullable', 'string', 'max:500'],

            // subtotal/discount/total e items.*.unit_price/total NO se validan aquí:
            // nunca confiamos en precios que vengan del cliente, se recalculan abajo
            // a partir del precio real del producto en la base de datos.
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('business_id', $businessId)->where('is_active', true),
            ],
            'items.*.qty'    => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.notes'  => ['nullable', 'string', 'max:300'],
        ]);

        $expiredResponse = $this->checkScanToken($data['scan_token'], $businessId, $data['table_id'] ?? null);
        if ($expiredResponse) {
            return $expiredResponse;
        }

        try {
            return DB::transaction(function () use ($data, $businessId) {
                $products = Product::where('business_id', $businessId)
                    ->whereIn('id', collect($data['items'])->pluck('product_id')->unique())
                    ->get()
                    ->keyBy('id');

                $subtotal = 0;
                $lineItems = [];

                foreach ($data['items'] as $item) {
                    $product = $products->get($item['product_id']);

                    // Ya cubierto por Rule::exists, pero por si el producto se
                    // desactivó justo entre la validación y la transacción.
                    abort_unless($product, 422, 'Uno de los productos ya no está disponible.');

                    $qty = (int) $item['qty'];
                    $unitPrice = (float) $product->price;
                    $lineTotal = round($unitPrice * $qty, 2);

                    $subtotal += $lineTotal;

                    $lineItems[] = [
                        'product_id'  => $product->id,
                        'quantity'    => $qty,
                        'unit_price'  => $unitPrice,
                        'total_price' => $lineTotal,
                        'notes'       => $item['notes'] ?? null,
                    ];
                }

                $subtotal = round($subtotal, 2);
                $discount = 0.0; // sin sistema de cupones todavía; no se acepta del cliente
                $total    = round($subtotal - $discount, 2);

                $order = Order::create([
                    'business_id'    => $businessId,
                    'order_number'   => Order::nextNumberFor($businessId),
                    'table_id'       => $data['table_id'] ?? null,
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

                foreach ($lineItems as $lineItem) {
                    OrderItem::create([
                        'business_id' => $businessId,
                        'order_id'    => $order->id,
                        ...$lineItem,
                    ]);
                }

                return response()->json([
                    'ok'           => true,
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                    'subtotal' => (float) $order->subtotal,
                    'discount' => (float) $order->discount,
                    'total'    => (float) $order->total,
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

    /**
     * Verifica que el scan_token venga del propio negocio/mesa del pedido y
     * que no haya pasado más de SCAN_EXPIRY_MINUTES desde que se generó al
     * renderizar el menú. Devuelve una respuesta 422 si falla, o null si todo
     * está en orden y se puede continuar con el guardado del pedido.
     */
    private function checkScanToken(string $scanToken, int $businessId, ?int $tableId)
    {
        try {
            $payload = decrypt($scanToken);
        } catch (DecryptException $e) {
            return response()->json([
                'ok'      => false,
                'expired' => true,
                'message' => 'Tu sesión del menú ya no es válida. Vuelve a escanear el código QR o recarga la página.',
            ], 422);
        }

        if (
            !is_array($payload)
            || ($payload['business_id'] ?? null) !== $businessId
            || ($payload['table_id'] ?? null) !== $tableId
            || !isset($payload['issued_at'])
        ) {
            return response()->json([
                'ok'      => false,
                'expired' => true,
                'message' => 'Tu sesión del menú ya no es válida. Vuelve a escanear el código QR o recarga la página.',
            ], 422);
        }

        $ageMinutes = (now()->timestamp - (int) $payload['issued_at']) / 60;

        if ($ageMinutes > self::SCAN_EXPIRY_MINUTES) {
            return response()->json([
                'ok'      => false,
                'expired' => true,
                'message' => 'Tu sesión del menú expiró. Vuelve a escanear el código QR o recarga la página.',
            ], 422);
        }

        return null;
    }
}
