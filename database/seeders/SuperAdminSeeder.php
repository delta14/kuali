<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Crear usuario super admin
            $superAdmin = User::firstOrCreate(
                ['email' => 'robertokuali@gmail.com'],
                [
                    'name'           => 'Super Admin Kuali',
                    'password'       => Hash::make('qwerty192709'), // cámbialo después
                    'is_super_admin' => true,
                ]
            );

            // 2. Crear negocio demo
            $business = Business::firstOrCreate(
                ['slug' => 'pizzeria-kuali-demo'],
                [
                    'name'          => 'Pizzería Kuali Demo',
                    'email_contact' => 'contacto@kuali.test',
                    'phone'         => '555-000-0000',
                    'address'       => 'Demo Street 123',
                    'plan'          => 'basic',
                    'is_active'     => true,
                ]
            );

            // 3. Obtener rol admin
            $adminRole = Role::where('name', 'admin')->first();

            // 4. Asociar super admin como admin del negocio demo
            if ($adminRole && $business) {
                DB::table('business_user')->updateOrInsert(
                    [
                        'business_id' => $business->id,
                        'user_id'     => $superAdmin->id,
                    ],
                    [
                        'role_id'   => $adminRole->id,
                        'is_active' => true,
                    ]
                );
            }
        });
    }
}
