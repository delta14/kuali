<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',  'description' => 'Administrador del negocio'],
            ['name' => 'mesero', 'description' => 'Atiende mesas y toma pedidos'],
            ['name' => 'cocina', 'description' => 'Ve y prepara pedidos'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
