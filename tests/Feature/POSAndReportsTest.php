<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Business;
use App\Models\Role;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSAndReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Business $business;
    protected Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->role = Role::create([
            'name' => 'admin',
            'description' => 'Administrador del negocio',
        ]);

        $this->business = Business::create([
            'name' => 'Restaurante Kuali Premium',
            'slug' => 'restaurante-kuali-premium',
            'email_contact' => 'contacto@kuali-premium.com',
            'phone' => '999-888-7777',
            'address' => 'Av. Premium 456',
            'plan' => 'pro',
            'is_active' => true,
        ]);

        $this->user->businesses()->attach($this->business->id, [
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);
    }

    public function test_pos_view_renders_correctly_for_authorized_user(): void
    {
        // Crear una categoría de ejemplo con productos para que se renderice el catálogo
        $category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Bebidas Frías',
            'is_active' => true,
        ]);

        $product = $category->products()->create([
            'business_id' => $this->business->id,
            'name' => 'Limonada Mineral',
            'description' => 'Deliciosa limonada fresca con agua mineral',
            'price' => 45.00,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->get(route('orders.pos'));

        $response->assertOk();
        $response->assertViewHas('categories');
        $response->assertSee('Caja Registradora POS');
        $response->assertSee('Bebidas Frías');
        $response->assertSee('Limonada Mineral');
        $response->assertSee('$45.00');
    }

    public function test_sales_report_view_renders_correctly_for_authorized_user(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('reports.sales'));

        $response->assertOk();
        $response->assertViewHas('business');
        $response->assertViewHas('from');
        $response->assertViewHas('to');
        $response->assertViewHas('totalSales');
        $response->assertViewHas('totalOrders');
        $response->assertViewHas('avgTicket');
        $response->assertViewHas('bySource');
        $response->assertViewHas('byType');
        $response->assertViewHas('topProducts');

        $response->assertSee('Reportes y Rendimiento');
        $response->assertSee('Ventas Totales');
        $response->assertSee('Pedidos Completados');
        $response->assertSee('Ticket Promedio');
    }

    public function test_public_menu_view_renders_correctly(): void
    {
        $category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Bebidas Frías',
            'is_active' => true,
        ]);

        $product = $category->products()->create([
            'business_id' => $this->business->id,
            'name' => 'Limonada Mineral',
            'description' => 'Deliciosa limonada fresca con agua mineral',
            'price' => 45.00,
            'is_active' => true,
        ]);

        $response = $this->get(route('public.menu.show', ['slug' => $this->business->slug]));

        $response->assertOk();
        $response->assertViewHas('business');
        $response->assertViewHas('categories');
        $response->assertSee('Restaurante Kuali Premium');
        $response->assertSee('Bebidas Frías');
        $response->assertSee('Limonada Mineral');
        $response->assertSee('$45.00');
    }

    public function test_check_new_orders_endpoint_works(): void
    {
        // 1. Crear un primer pedido (inicialización)
        $order1 = \App\Models\Order::create([
            'business_id' => $this->business->id,
            'status' => 'pending',
            'source' => 'qr',
            'subtotal' => 150.00,
            'total' => 150.00,
            'customer_name' => 'Juan Perez',
            'placed_at' => now(),
        ]);

        // La primera petición con last_id = 0 debe inicializar el last_id al id del pedido más alto
        $response = $this
            ->actingAs($this->user)
            ->get(route('orders.check-new', ['last_id' => 0]));

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'has_new' => false,
            'last_id' => $order1->id,
            'orders' => []
        ]);

        // 2. Crear un segundo pedido nuevo posterior
        $order2 = \App\Models\Order::create([
            'business_id' => $this->business->id,
            'status' => 'pending',
            'source' => 'qr',
            'subtotal' => 200.00,
            'total' => 200.00,
            'customer_name' => 'Maria Lopez',
            'placed_at' => now(),
        ]);

        // La segunda petición con last_id = $order1->id debe detectar el nuevo pedido $order2
        $response2 = $this
            ->actingAs($this->user)
            ->get(route('orders.check-new', ['last_id' => $order1->id]));

        $response2->assertOk();
        $response2->assertJson([
            'ok' => true,
            'has_new' => true,
            'last_id' => $order2->id,
        ]);
        
        $response2->assertJsonFragment([
            'id' => $order2->id,
            'customer_name' => 'Maria Lopez',
            'total' => 200.00,
        ]);

        // 3. Si volvemos a consultar con el last_id actualizado ($order2->id), no debe reportar nada nuevo
        $response3 = $this
            ->actingAs($this->user)
            ->get(route('orders.check-new', ['last_id' => $order2->id]));

        $response3->assertOk();
        $response3->assertJson([
            'ok' => true,
            'has_new' => false,
            'last_id' => $order2->id,
            'orders' => []
        ]);
    }
}

