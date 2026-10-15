<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessFlyerBgTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_upload_flyer_background_on_create(): void
    {
        Storage::fake('public');

        $superAdmin = User::factory()->create(['is_super_admin' => true]);

        $response = $this->actingAs($superAdmin)->post(route('super.businesses.store'), [
            'name' => 'Negocio con fondo',
            'flyer_bg' => UploadedFile::fake()->image('fondo.jpg'),
        ]);

        $response->assertRedirect(route('super.businesses.index'));

        $business = Business::where('name', 'Negocio con fondo')->first();
        $this->assertNotNull($business->flyer_bg_path);
        Storage::disk('public')->assertExists($business->flyer_bg_path);
    }

    public function test_super_admin_can_replace_flyer_background_on_update(): void
    {
        Storage::fake('public');

        $superAdmin = User::factory()->create(['is_super_admin' => true]);

        $business = Business::create([
            'name' => 'Negocio existente',
            'slug' => 'negocio-existente',
            'plan' => 'basic',
            'is_active' => true,
            'flyer_bg_path' => 'businesses/flyer-bg/old.jpg',
        ]);
        Storage::disk('public')->put('businesses/flyer-bg/old.jpg', 'contenido-viejo');

        $response = $this->actingAs($superAdmin)->put(route('super.businesses.update', $business), [
            'name' => $business->name,
            'flyer_bg' => UploadedFile::fake()->image('nuevo-fondo.jpg'),
        ]);

        $response->assertRedirect(route('super.businesses.index'));

        $business->refresh();
        $this->assertNotEquals('businesses/flyer-bg/old.jpg', $business->flyer_bg_path);
        Storage::disk('public')->assertMissing('businesses/flyer-bg/old.jpg');
        Storage::disk('public')->assertExists($business->flyer_bg_path);
    }
}
