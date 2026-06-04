<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_can_be_rendered_for_active_business_admin(): void
    {
        $user = User::factory()->create();

        $role = \App\Models\Role::create([
            'name' => 'admin',
            'description' => 'Administrador del negocio',
        ]);
        
        $business = Business::create([
            'name' => 'Pizza Kuali Test',
            'slug' => 'pizza-kuali-test',
            'email_contact' => 'test@kuali.com',
            'phone' => '123-456-7890',
            'address' => 'Test Address 123',
            'plan' => 'basic',
            'is_active' => true,
        ]);

        $user->businesses()->attach($business->id, [
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();
        $response->assertViewHas('business');
        $response->assertViewHas('categoriesCount');
        $response->assertViewHas('productsCount');
        $response->assertViewHas('tablesCount');
        $response->assertViewHas('ordersCount');
        $response->assertViewHas('pendingOrdersCount');
        $response->assertViewHas('totalSales');
        $response->assertViewHas('recentOrders');
        $response->assertViewHas('popularDishes');
        
        $response->assertSee('Pizza Kuali Test');
        $response->assertSee('Acciones de Configuración');
    }

    public function test_dashboard_redirects_unauthenticated_users_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
