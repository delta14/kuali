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

        // La primera petición (sin "since") debe inicializar el cursor sin reportar nada,
        // aunque ya exista un pedido previo a que el panel estuviera abierto.
        $response = $this
            ->actingAs($this->user)
            ->get(route('orders.check-new'));

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'has_new' => false,
            'orders' => []
        ]);

        $since1 = $response->json('since');

        // Los timestamps de MySQL/sqlite tienen resolución de 1 segundo; sin
        // avanzar el reloj de prueba, order2 podría guardarse con el mismo
        // updated_at que el cursor "since1" y el ">" no lo detectaría.
        \Illuminate\Support\Carbon::setTestNow(now()->addSeconds(2));

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

        // La segunda petición con since = $since1 debe detectar el nuevo pedido $order2
        $response2 = $this
            ->actingAs($this->user)
            ->get(route('orders.check-new', ['since' => $since1]));

        $response2->assertOk();
        $response2->assertJson([
            'ok' => true,
            'has_new' => true,
        ]);

        $response2->assertJsonFragment([
            'id' => $order2->id,
            'customer_name' => 'Maria Lopez',
            'total' => 200.00,
        ]);

        $since2 = $response2->json('since');

        // 3. Si volvemos a consultar con el since actualizado, no debe reportar nada nuevo
        $response3 = $this
            ->actingAs($this->user)
            ->get(route('orders.check-new', ['since' => $since2]));

        \Illuminate\Support\Carbon::setTestNow();

        $response3->assertOk();
        $response3->assertJson([
            'ok' => true,
            'has_new' => false,
            'orders' => []
        ]);
    }
}

