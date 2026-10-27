<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicOrderTest extends TestCase
{
    use RefreshDatabase;

    protected Business $business;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->business = Business::create([
            'name' => 'Restaurante Kuali',
            'slug' => 'restaurante-kuali',
            'email_contact' => 'contacto@kuali.com',
            'phone' => '999-888-7777',
            'address' => 'Av. Principal 123',
            'plan' => 'pro',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Bebidas',
            'is_active' => true,
        ]);

        $this->product = $this->category->products()->create([
            'business_id' => $this->business->id,
            'name' => 'Limonada',
            'price' => 45.00,
            'is_active' => true,
        ]);
    }

    protected function scanToken(?int $businessId = null, ?int $tableId = null): string
    {
        return encrypt([
            'business_id' => $businessId ?? $this->business->id,
            'table_id'    => $tableId,
            'issued_at'   => now()->timestamp,
        ]);
    }

    public function test_order_total_is_computed_from_the_real_product_price_not_the_client(): void
    {
        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'customer_name' => 'Juan Perez',
            'items' => [
                [
                    // El cliente intenta pagar $0.01 por un producto de $45.00
                    'product_id' => $this->product->id,
                    'name' => $this->product->name,
                    'qty' => 2,
                    'unit_price' => 0.01,
                    'total' => 0.02,
                ],
            ],
            // También intenta falsear los totales generales del pedido
            'subtotal' => 0.02,
            'discount' => 0,
            'total' => 0.02,
        ]);

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'subtotal' => 90.0,
            'discount' => 0.0,
            'total' => 90.0,
        ]);

        $order = Order::findOrFail($response->json('order_id'));
        $this->assertSame(90.0, (float) $order->subtotal);
        $this->assertSame(90.0, (float) $order->total);

        $item = $order->items->first();
        $this->assertSame(45.0, (float) $item->unit_price);
        $this->assertSame(90.0, (float) $item->total_price);
    }

    public function test_it_rejects_a_product_that_belongs_to_another_business(): void
    {
        $otherBusiness = Business::create([
            'name' => 'Otro Negocio',
            'slug' => 'otro-negocio',
            'email_contact' => 'contacto@otro.com',
            'is_active' => true,
        ]);

        $foreignProduct = Category::create([
            'business_id' => $otherBusiness->id,
            'name' => 'Postres',
            'is_active' => true,
        ])->products()->create([
            'business_id' => $otherBusiness->id,
            'name' => 'Pastel',
            'price' => 60.00,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $foreignProduct->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items.0.product_id']);
        $this->assertSame(0, Order::count());
    }

    public function test_it_rejects_an_inactive_product(): void
    {
        $this->product->update(['is_active' => false]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertSame(0, Order::count());
    }

    public function test_it_rejects_a_table_that_belongs_to_another_business(): void
    {
        $otherBusiness = Business::create([
            'name' => 'Otro Negocio',
            'slug' => 'otro-negocio-2',
            'email_contact' => 'contacto@otro2.com',
            'is_active' => true,
        ]);

        $foreignTable = Table::create([
            'business_id' => $otherBusiness->id,
            'name' => 'Mesa 1',
            'code' => 'OTRO-1',
            'qr_token' => 'token-otro-1',
            'is_active' => true,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'table_id' => $foreignTable->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['table_id']);
        $this->assertSame(0, Order::count());
    }

    public function test_order_fails_without_a_scan_token(): void
    {
        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['scan_token']);
        $this->assertSame(0, Order::count());
    }

    public function test_order_fails_with_a_malformed_scan_token(): void
    {
        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => 'esto-no-es-un-token-valido',
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false, 'expired' => true]);
        $this->assertSame(0, Order::count());
    }

    public function test_order_fails_with_a_scan_token_from_another_business(): void
    {
        $otherBusiness = Business::create([
            'name' => 'Otro Negocio',
            'slug' => 'otro-negocio-3',
            'email_contact' => 'contacto@otro3.com',
            'is_active' => true,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken($otherBusiness->id),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false, 'expired' => true]);
        $this->assertSame(0, Order::count());
    }

    public function test_order_fails_when_scan_token_is_older_than_the_expiry_window(): void
    {
        $staleToken = encrypt([
            'business_id' => $this->business->id,
            'table_id'    => null,
            'issued_at'   => now()->subMinutes(\App\Http\Controllers\PublicOrderController::SCAN_EXPIRY_MINUTES + 1)->timestamp,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $staleToken,
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false, 'expired' => true]);
        $this->assertSame(0, Order::count());
    }

    public function test_order_succeeds_with_a_fresh_scan_token_just_under_the_expiry_window(): void
    {
        $freshToken = encrypt([
            'business_id' => $this->business->id,
            'table_id'    => null,
            'issued_at'   => now()->subMinutes(\App\Http\Controllers\PublicOrderController::SCAN_EXPIRY_MINUTES - 1)->timestamp,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $freshToken,
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertOk();
        $this->assertSame(1, Order::count());
    }

    public function test_order_uses_the_variant_price_not_the_product_price_when_a_variant_is_sent(): void
    {
        $variant = $this->product->variants()->create([
            'business_id' => $this->business->id,
            'name' => 'Grande',
            'price' => 65.00,
            'position' => 0,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $variant->id,
                    // el cliente intenta mandar un precio falso, debe ignorarse
                    'qty' => 2,
                    'unit_price' => 0.01,
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'subtotal' => 130.0,
            'total' => 130.0,
        ]);

        $item = Order::findOrFail($response->json('order_id'))->items->first();
        $this->assertSame(65.0, (float) $item->unit_price);
        $this->assertSame(130.0, (float) $item->total_price);
        $this->assertSame($variant->id, $item->product_variant_id);
        $this->assertSame('Grande', $item->variant_name);
    }

    public function test_it_rejects_a_variant_that_does_not_belong_to_the_submitted_product(): void
    {
        $otherProduct = $this->category->products()->create([
            'business_id' => $this->business->id,
            'name' => 'Café',
            'price' => 30.00,
            'is_active' => true,
        ]);

        $variant = $otherProduct->variants()->create([
            'business_id' => $this->business->id,
            'name' => 'Grande',
            'price' => 40.00,
            'position' => 0,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $this->product->id, 'variant_id' => $variant->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertSame(0, Order::count());
    }

    public function test_it_rejects_a_variant_that_belongs_to_another_business(): void
    {
        $otherBusiness = Business::create([
            'name' => 'Otro Negocio',
            'slug' => 'otro-negocio-variant',
            'email_contact' => 'contacto@otrovariant.com',
            'is_active' => true,
        ]);

        $foreignProduct = Category::create([
            'business_id' => $otherBusiness->id,
            'name' => 'Postres',
            'is_active' => true,
        ])->products()->create([
            'business_id' => $otherBusiness->id,
            'name' => 'Pastel',
            'price' => 60.00,
            'is_active' => true,
        ]);

        $foreignVariant = $foreignProduct->variants()->create([
            'business_id' => $otherBusiness->id,
            'name' => 'Rebanada grande',
            'price' => 70.00,
            'position' => 0,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $this->product->id, 'variant_id' => $foreignVariant->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items.0.variant_id']);
        $this->assertSame(0, Order::count());
    }

    public function test_it_rejects_an_inactive_variant(): void
    {
        $variant = $this->product->variants()->create([
            'business_id' => $this->business->id,
            'name' => 'Grande',
            'price' => 65.00,
            'position' => 0,
            'is_active' => false,
        ]);

        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $this->product->id, 'variant_id' => $variant->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertSame(0, Order::count());
    }

    protected function createTable(): Table
    {
        return Table::create([
            'business_id' => $this->business->id,
            'name' => 'Mesa 1',
            'code' => 'mesa-1',
            'qr_token' => 'token-mesa-1',
            'is_active' => true,
        ]);
    }

    public function test_a_second_order_from_the_same_table_merges_into_the_existing_open_order(): void
    {
        $table = $this->createTable();

        $first = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'table_id' => $table->id,
            'scan_token' => $this->scanToken(null, $table->id),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $first->assertOk();
        $first->assertJson(['ok' => true, 'merged' => false, 'total' => 45.0]);

        $second = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'table_id' => $table->id,
            'scan_token' => $this->scanToken(null, $table->id),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $second->assertOk();
        $second->assertJson(['ok' => true, 'merged' => true, 'total' => 90.0]);

        $this->assertSame($first->json('order_id'), $second->json('order_id'));
        $this->assertSame(1, Order::count());

        $order = Order::findOrFail($first->json('order_id'));
        $this->assertCount(2, $order->items);
        $this->assertSame(90.0, (float) $order->total);
    }

    public function test_a_new_order_from_the_same_table_does_not_merge_into_a_closed_order(): void
    {
        $table = $this->createTable();

        $first = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'table_id' => $table->id,
            'scan_token' => $this->scanToken(null, $table->id),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $first->assertOk();

        Order::findOrFail($first->json('order_id'))->update(['status' => 'completed', 'closed_at' => now()]);

        $second = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'table_id' => $table->id,
            'scan_token' => $this->scanToken(null, $table->id),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $second->assertOk();
        $second->assertJson(['ok' => true, 'merged' => false]);

        $this->assertNotSame($first->json('order_id'), $second->json('order_id'));
        $this->assertSame(2, Order::count());
    }

    public function test_a_stale_open_order_from_a_previous_visit_does_not_receive_new_items(): void
    {
        $table = $this->createTable();

        $first = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'table_id' => $table->id,
            'scan_token' => $this->scanToken(null, $table->id),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $first->assertOk();

        // El negocio nunca cerró la cuenta y pasó más tiempo del límite de sesión.
        Order::findOrFail($first->json('order_id'))
            ->update(['placed_at' => now()->subMinutes(\App\Http\Controllers\PublicOrderController::SCAN_EXPIRY_MINUTES + 1)]);

        $second = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'table_id' => $table->id,
            'scan_token' => $this->scanToken(null, $table->id),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $second->assertOk();
        $second->assertJson(['ok' => true, 'merged' => false]);

        $this->assertNotSame($first->json('order_id'), $second->json('order_id'));
        $this->assertSame(2, Order::count());
    }

    public function test_orders_without_a_table_never_merge(): void
    {
        $first = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $first->assertOk();

        $second = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
            'scan_token' => $this->scanToken(),
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);
        $second->assertOk();
        $second->assertJson(['ok' => true, 'merged' => false]);

        $this->assertNotSame($first->json('order_id'), $second->json('order_id'));
        $this->assertSame(2, Order::count());
    }

    public function test_status_endpoint_reports_a_pending_order_as_not_closed(): void
    {
        $table = Table::create([
            'business_id' => $this->business->id,
            'name' => 'Mesa 1',
            'code' => 'mesa-1',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'capacity' => 4,
            'is_active' => true,
        ]);

        $order = Order::create([
            'business_id' => $this->business->id,
            'order_number' => 1,
            'table_id' => $table->id,
            'status' => 'pending',
            'source' => 'qr',
            'subtotal' => 100,
            'total' => 100,
            'placed_at' => now(),
        ]);

        $response = $this->getJson(route('public.orders.status', $order) . '?scan_token=' . urlencode($this->scanToken(null, $table->id)));

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'order_id' => $order->id,
            'status' => 'pending',
            'closed' => false,
        ]);
    }

    public function test_status_endpoint_reports_a_completed_order_as_closed(): void
    {
        $table = Table::create([
            'business_id' => $this->business->id,
            'name' => 'Mesa 2',
            'code' => 'mesa-2',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'capacity' => 4,
            'is_active' => true,
        ]);

        $order = Order::create([
            'business_id' => $this->business->id,
            'order_number' => 1,
            'table_id' => $table->id,
            'status' => 'completed',
            'source' => 'qr',
            'subtotal' => 100,
            'total' => 100,
            'placed_at' => now(),
            'closed_at' => now(),
        ]);

        $response = $this->getJson(route('public.orders.status', $order) . '?scan_token=' . urlencode($this->scanToken(null, $table->id)));

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'status' => 'completed',
            'closed' => true,
        ]);
    }

    public function test_status_endpoint_rejects_a_scan_token_from_another_table(): void
    {
        $table = Table::create([
            'business_id' => $this->business->id,
            'name' => 'Mesa 3',
            'code' => 'mesa-3',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'capacity' => 4,
            'is_active' => true,
        ]);

        $otherTable = Table::create([
            'business_id' => $this->business->id,
            'name' => 'Mesa 4',
            'code' => 'mesa-4',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'capacity' => 4,
            'is_active' => true,
        ]);

        $order = Order::create([
            'business_id' => $this->business->id,
            'order_number' => 1,
            'table_id' => $table->id,
            'status' => 'pending',
            'source' => 'qr',
            'subtotal' => 100,
            'total' => 100,
            'placed_at' => now(),
        ]);

        $response = $this->getJson(route('public.orders.status', $order) . '?scan_token=' . urlencode($this->scanToken(null, $otherTable->id)));

        $response->assertStatus(422);
    }

    public function test_status_endpoint_works_even_with_an_old_scan_token(): void
    {
        // A diferencia de crear un pedido, sólo consultar el estado no debe
        // exigir que el scan_token sea "reciente": el cliente puede llevar
        // sentado más de SCAN_EXPIRY_MINUTES y aun así necesita saber si ya
        // le cerraron la cuenta.
        $table = Table::create([
            'business_id' => $this->business->id,
            'name' => 'Mesa 5',
            'code' => 'mesa-5',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'capacity' => 4,
            'is_active' => true,
        ]);

        $order = Order::create([
            'business_id' => $this->business->id,
            'order_number' => 1,
            'table_id' => $table->id,
            'status' => 'completed',
            'source' => 'qr',
            'subtotal' => 100,
            'total' => 100,
            'placed_at' => now()->subHours(5),
            'closed_at' => now(),
        ]);

        $oldToken = encrypt([
            'business_id' => $this->business->id,
            'table_id' => $table->id,
            'issued_at' => now()->subHours(5)->timestamp,
        ]);

        $response = $this->getJson(route('public.orders.status', $order) . '?scan_token=' . urlencode($oldToken));

        $response->assertOk();
        $response->assertJson(['ok' => true, 'closed' => true]);
    }
}
