<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryScopeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Business $business;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'admin',
            'description' => 'Administrador del negocio',
        ]);

        $this->user = User::factory()->create();

        $this->business = Business::create([
            'name' => 'Restaurante Kuali',
            'slug' => 'restaurante-kuali',
            'email_contact' => 'contacto@kuali.com',
            'is_active' => true,
        ]);

        $this->user->businesses()->attach($this->business->id, [
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Bebidas',
            'is_active' => true,
        ]);
    }

    public function test_it_rejects_creating_a_product_with_a_category_from_another_business(): void
    {
        $otherBusiness = Business::create([
            'name' => 'Otro Negocio',
            'slug' => 'otro-negocio',
            'email_contact' => 'contacto@otro.com',
            'is_active' => true,
        ]);

        $foreignCategory = Category::create([
            'business_id' => $otherBusiness->id,
            'name' => 'Postres ajenos',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => 'Pastel intruso',
                'category_id' => $foreignCategory->id,
                'price' => 50,
            ]);

        $response->assertSessionHasErrors(['category_id']);
        $this->assertSame(0, Product::count());
    }

    public function test_it_allows_creating_a_product_with_a_category_from_the_own_business(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => 'Limonada',
                'category_id' => $this->category->id,
                'price' => 45,
            ]);

        $response->assertRedirect(route('products.index'));
        $this->assertSame(1, Product::count());
    }
}
