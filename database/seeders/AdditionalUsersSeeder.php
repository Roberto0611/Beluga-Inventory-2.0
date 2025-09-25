<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdditionalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // usuario de Natalia
        $natalia = User::factory()->create([
            'name' => 'Natalia',
            'email' => 'natalia@beluga.com',
            'password' => Hash::make('BelugaNa86')
        ]);

        // Permisos
        Permission::create(['name' => 'Acceso a inventario']);
        Permission::create(['name' => 'Eliminar registros de inventario']);

        Permission::create(['name' => 'Acceso a consultorio']);

        // Roles

        // admin
        $roleAdmin = Role::create(['name' => 'Administrador']);

        $adminUser = User::where('email', 'ADMIN')->first();
        $adminUser->assignRole($roleAdmin);
        $rodrigoUser = User::where('email', 'RodrigoOntiveros')->first();
        $rodrigoUser->assignRole($roleAdmin);

        $permissionAdmin = Permission::query()->pluck('name');
        $roleAdmin->syncPermissions($permissionAdmin);

        // inventario
        $roleInventory = Role::create(['name' => 'Inventario']);

        $miguel = User::where('email', 'MiguelAngel')->first();
        $miguel->assignRole($roleInventory);
        $natalia->assignRole($roleInventory);

        $permissionInventory = Permission::whereIn('name', ['Acceso a inventario'])->pluck('name');
        $roleInventory->syncPermissions($permissionInventory);
    }
}   
