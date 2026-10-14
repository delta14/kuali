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

    public function test_order_total_is_computed_from_the_real_product_price_not_the_client(): void
    {
        $response = $this->postJson(route('public.orders.store'), [
            'business_id' => $this->business->id,
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
            'items' => [
                ['product_id' => $this->product->id, 'qty' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['table_id']);
        $this->assertSame(0, Order::count());
    }
}
