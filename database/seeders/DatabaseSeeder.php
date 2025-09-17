<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\password;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'ADMIN',
            'password' => Hash::make('BelugaInventoryAdmin2024'),
            'is_admin' => '1'
        ]);

        User::factory()->create([
            'name' => 'Rodrigo',
            'email' => 'RodrigoOntiveros',
            'password' => Hash::make('RodrigoBe89!'),
            'is_admin' => '1'
        ]);

        User::factory()->create([
            'name' => 'Miguel Angel',
            'email' => 'MiguelAngel',
            'password' => Hash::make('BelugaMi86') 
        ]);

    }
}
