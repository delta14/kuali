<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    protected Business $business;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->business = Business::create([
            'name' => 'Pizzeria de Prueba',
            'slug' => 'pizzeria-de-prueba',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Pizzas',
            'is_active' => true,
        ]);
    }

    protected function createBusinessAdmin(?Business $business = null): User
    {
        $role = Role::firstOrCreate(['id' => 2], ['name' => 'admin']);

        $user = User::factory()->create();
        $user->businesses()->attach(($business ?? $this->business)->id, ['role_id' => $role->id, 'is_active' => true]);

        return $user;
    }

    public function test_admin_can_create_a_product_with_price_variants_and_no_base_price(): void
    {
        $admin = $this->createBusinessAdmin();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Pizza Hawaiana',
            'category_id' => $this->category->id,
            'variants' => [
                ['name' => 'Chica', 'price' => '99.00'],
                ['name' => 'Mediana', 'price' => '149.00'],
                ['name' => 'Grande', 'price' => '199.00'],
            ],
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Pizza Hawaiana')->firstOrFail();
        $this->assertCount(3, $product->variants);
        $this->assertSame(99.0, (float) $product->price);

        $names = $product->variants->pluck('name')->all();
        $this->assertSame(['Chica', 'Mediana', 'Grande'], $names);
    }

    public function test_admin_must_provide_either_a_price_or_at_least_one_valid_variant(): void
    {
        $admin = $this->createBusinessAdmin();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Pizza Sin Precio',
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors(['price']);
        $this->assertSame(0, Product::count());
    }

    public function test_admin_can_create_a_product_with_a_flat_price_and_no_variants(): void
    {
        $admin = $this->createBusinessAdmin();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Refresco',
            'category_id' => $this->category->id,
            'price' => '25.00',
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Refresco')->firstOrFail();
        $this->assertSame(25.0, (float) $product->price);
        $this->assertCount(0, $product->variants);
    }

    public function test_admin_can_replace_a_products_variants_on_update(): void
    {
        $admin = $this->createBusinessAdmin();

        $product = $this->category->products()->create([
            'business_id' => $this->business->id,
            'name' => 'Pizza Pepperoni',
            'price' => 99.00,
            'is_active' => true,
        ]);

        $oldVariant = $product->variants()->create([
            'business_id' => $this->business->id,
            'name' => 'Chica',
            'price' => 99.00,
            'position' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => $product->name,
            'category_id' => $this->category->id,
            'variants' => [
                ['name' => 'Mediana', 'price' => '150.00'],
                ['name' => 'Grande', 'price' => '200.00'],
            ],
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('product_variants', ['id' => $oldVariant->id]);

        $product->refresh();
        $this->assertCount(2, $product->variants);
        $this->assertSame(['Mediana', 'Grande'], $product->variants->pluck('name')->all());
        $this->assertSame(150.0, (float) $product->price);
    }

    public function test_admin_cannot_edit_a_product_belonging_to_another_business(): void
    {
        $otherBusiness = Business::create([
            'name' => 'Otro Negocio',
            'slug' => 'otro-negocio-variant-admin',
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

        $admin = $this->createBusinessAdmin();

        $response = $this->actingAs($admin)->put(route('products.update', $foreignProduct), [
            'name' => 'Hackeado',
            'category_id' => $this->category->id,
            'price' => '1.00',
        ]);

        $response->assertForbidden();

        $foreignProduct->refresh();
        $this->assertSame('Pastel', $foreignProduct->name);
    }
}
