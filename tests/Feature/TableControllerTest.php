<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Role;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createBusinessAdmin(Business $business): User
    {
        // Sólo "name" es la columna realmente única en roles; forzar un id
        // fijo (como hacen otros tests) falla si este helper se llama más
        // de una vez en el mismo test, porque "id" no es fillable y
        // firstOrCreate no encuentra la fila ya creada en la 2a llamada.
        $role = Role::firstOrCreate(['name' => 'admin']);

        $user = User::factory()->create();
        $user->businesses()->attach($business->id, ['role_id' => $role->id, 'is_active' => true]);

        return $user;
    }

    public function test_two_different_businesses_can_each_have_a_table_named_mesa_1(): void
    {
        $businessA = Business::create(['name' => 'Negocio A', 'slug' => 'negocio-a-tables', 'is_active' => true]);
        $businessB = Business::create(['name' => 'Negocio B', 'slug' => 'negocio-b-tables', 'is_active' => true]);

        $adminA = $this->createBusinessAdmin($businessA);
        $adminB = $this->createBusinessAdmin($businessB);

        $responseA = $this->actingAs($adminA)->post(route('tables.store'), ['name' => 'Mesa 1']);
        $responseA->assertRedirect(route('tables.index'));

        // Simula dos sesiones de navegador distintas: en la suite de tests el
        // cliente HTTP comparte sesión entre llamadas, así que sin esto
        // SetCurrentBusiness seguiría usando el "current_business_id" que
        // quedó en sesión del admin A para la petición del admin B.
        session()->forget('current_business_id');
        $this->app->forgetInstance('currentBusinessId');

        $responseB = $this->actingAs($adminB)->post(route('tables.store'), ['name' => 'Mesa 1']);
        $responseB->assertRedirect(route('tables.index'));

        // El scope global de negocio queda "atado" al último business_id
        // vinculado por el middleware durante la petición anterior; lo
        // soltamos para que estas lecturas directas vean ambos negocios.
        $this->app->forgetInstance('currentBusinessId');

        $this->assertSame('mesa-1', Table::where('business_id', $businessA->id)->first()->code);
        $this->assertSame('mesa-1', Table::where('business_id', $businessB->id)->first()->code);
    }

    public function test_duplicate_table_name_within_the_same_business_gets_a_suffixed_code(): void
    {
        $business = Business::create(['name' => 'Negocio C', 'slug' => 'negocio-c-tables', 'is_active' => true]);
        $admin = $this->createBusinessAdmin($business);

        $this->actingAs($admin)->post(route('tables.store'), ['name' => 'Mesa 1'])
            ->assertRedirect(route('tables.index'));

        $this->actingAs($admin)->post(route('tables.store'), ['name' => 'Mesa 1'])
            ->assertRedirect(route('tables.index'));

        $codes = Table::where('business_id', $business->id)->orderBy('id')->pluck('code')->all();
        $this->assertSame(['mesa-1', 'mesa-1-2'], $codes);
    }
}
