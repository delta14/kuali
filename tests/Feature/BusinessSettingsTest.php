<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createBusinessAdmin(Business $business): User
    {
        $role = Role::firstOrCreate(['id' => 2], ['name' => 'admin']);

        $user = User::factory()->create();
        $user->businesses()->attach($business->id, ['role_id' => $role->id, 'is_active' => true]);

        return $user;
    }

    public function test_business_admin_can_view_settings_page(): void
    {
        $business = Business::create([
            'name' => 'Negocio de Prueba',
            'slug' => 'negocio-de-prueba',
            'is_active' => true,
        ]);

        $admin = $this->createBusinessAdmin($business);

        $response = $this->actingAs($admin)->get(route('settings.edit'));

        $response->assertOk();
        $response->assertSee('Mi Negocio');
        $response->assertSee('Negocio de Prueba');
    }

    public function test_business_admin_can_update_own_business_data(): void
    {
        $business = Business::create([
            'name' => 'Negocio Viejo',
            'slug' => 'negocio-viejo',
            'is_active' => true,
        ]);

        $admin = $this->createBusinessAdmin($business);

        $response = $this->actingAs($admin)->put(route('settings.update'), [
            'name' => 'Negocio Actualizado',
            'email_contact' => 'contacto@negocio.com',
            'phone' => '555-000-1111',
            'address' => 'Calle Nueva 456',
        ]);

        $response->assertRedirect(route('settings.edit'));

        $business->refresh();
        $this->assertSame('Negocio Actualizado', $business->name);
        $this->assertSame('contacto@negocio.com', $business->email_contact);
        // El slug no debe cambiar aunque cambie el nombre: ya está impreso en los QR.
        $this->assertSame('negocio-viejo', $business->slug);
    }

    public function test_business_admin_can_upload_logo_and_flyer_bg(): void
    {
        Storage::fake('public');

        $business = Business::create([
            'name' => 'Negocio Con Archivos',
            'slug' => 'negocio-con-archivos',
            'is_active' => true,
        ]);

        $admin = $this->createBusinessAdmin($business);

        $response = $this->actingAs($admin)->put(route('settings.update'), [
            'name' => $business->name,
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'flyer_bg' => UploadedFile::fake()->image('fondo.jpg'),
        ]);

        $response->assertRedirect(route('settings.edit'));

        $business->refresh();
        $this->assertNotNull($business->logo_path);
        $this->assertNotNull($business->flyer_bg_path);
        Storage::disk('public')->assertExists($business->logo_path);
        Storage::disk('public')->assertExists($business->flyer_bg_path);
    }

    public function test_business_admin_cannot_edit_another_business_via_settings(): void
    {
        $ownBusiness = Business::create([
            'name' => 'Mi Negocio',
            'slug' => 'mi-negocio',
            'is_active' => true,
        ]);

        $otherBusiness = Business::create([
            'name' => 'Otro Negocio',
            'slug' => 'otro-negocio-settings',
            'is_active' => true,
        ]);

        $admin = $this->createBusinessAdmin($ownBusiness);

        $this->actingAs($admin)->put(route('settings.update'), [
            'name' => 'Nombre Hackeado',
        ]);

        $otherBusiness->refresh();
        $this->assertSame('Otro Negocio', $otherBusiness->name);
    }

    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get(route('settings.edit'));

        $response->assertRedirect(route('login'));
    }
}
