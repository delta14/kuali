<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Order;
use App\Models\Role;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createBusinessAdmin(Business $business): User
    {
        $role = Role::firstOrCreate(['name' => 'admin']);

        $user = User::factory()->create();
        $user->businesses()->attach($business->id, ['role_id' => $role->id, 'is_active' => true]);

        return $user;
    }

    protected function createOrder(Business $business, ?int $tableId = null): Order
    {
        return Order::create([
            'business_id' => $business->id,
            'order_number' => 1,
            'table_id' => $tableId,
            'status' => 'pending',
            'source' => 'counter',
            'subtotal' => 100,
            'total' => 100,
            'placed_at' => now(),
        ]);
    }

    protected function createTable(Business $business, string $name = 'Mesa 1'): Table
    {
        return Table::create([
            'business_id' => $business->id,
            'name' => $name,
            'code' => Str::slug($name),
            'qr_token' => Str::uuid(),
            'capacity' => 4,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_assign_a_table_to_a_counter_order(): void
    {
        $business = Business::create(['name' => 'Negocio A', 'slug' => 'negocio-a-orders', 'is_active' => true]);
        $admin = $this->createBusinessAdmin($business);
        $table = $this->createTable($business);
        $order = $this->createOrder($business);

        $response = $this->actingAs($admin)
            ->patchJson(route('orders.assign-table', $order), ['table_id' => $table->id]);

        $response->assertOk();
        $response->assertJson(['ok' => true]);

        $this->assertSame($table->id, $order->refresh()->table_id);
    }

    public function test_admin_can_clear_the_table_of_an_order(): void
    {
        $business = Business::create(['name' => 'Negocio B', 'slug' => 'negocio-b-orders', 'is_active' => true]);
        $admin = $this->createBusinessAdmin($business);
        $table = $this->createTable($business);
        $order = $this->createOrder($business, $table->id);

        $response = $this->actingAs($admin)
            ->patchJson(route('orders.assign-table', $order), ['table_id' => null]);

        $response->assertOk();
        $this->assertNull($order->refresh()->table_id);
    }

    public function test_admin_cannot_assign_a_table_belonging_to_another_business(): void
    {
        $business = Business::create(['name' => 'Negocio C', 'slug' => 'negocio-c-orders', 'is_active' => true]);
        $otherBusiness = Business::create(['name' => 'Negocio D', 'slug' => 'negocio-d-orders', 'is_active' => true]);

        $admin = $this->createBusinessAdmin($business);
        $foreignTable = $this->createTable($otherBusiness);
        $order = $this->createOrder($business);

        $response = $this->actingAs($admin)
            ->patchJson(route('orders.assign-table', $order), ['table_id' => $foreignTable->id]);

        $response->assertForbidden();
        $this->assertNull($order->refresh()->table_id);
    }

    public function test_admin_cannot_assign_a_table_to_an_order_from_another_business(): void
    {
        $business = Business::create(['name' => 'Negocio E', 'slug' => 'negocio-e-orders', 'is_active' => true]);
        $otherBusiness = Business::create(['name' => 'Negocio F', 'slug' => 'negocio-f-orders', 'is_active' => true]);

        $admin = $this->createBusinessAdmin($business);
        $table = $this->createTable($business);
        $foreignOrder = $this->createOrder($otherBusiness);

        $response = $this->actingAs($admin)
            ->patchJson(route('orders.assign-table', $foreignOrder), ['table_id' => $table->id]);

        $response->assertForbidden();
        $this->assertNull($foreignOrder->refresh()->table_id);
    }

    public function test_assigning_a_table_makes_the_order_eligible_for_a_later_qr_merge(): void
    {
        $business = Business::create(['name' => 'Negocio G', 'slug' => 'negocio-g-orders', 'is_active' => true]);
        $admin = $this->createBusinessAdmin($business);
        $table = $this->createTable($business);
        $order = $this->createOrder($business);

        $this->actingAs($admin)
            ->patchJson(route('orders.assign-table', $order), ['table_id' => $table->id])
            ->assertOk();

        $this->assertSame(
            $order->id,
            Order::where('business_id', $business->id)
                ->where('table_id', $table->id)
                ->where('status', 'pending')
                ->first()
                ->id
        );
    }
}
