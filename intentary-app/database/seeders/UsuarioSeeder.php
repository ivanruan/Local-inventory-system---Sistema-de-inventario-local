<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::factory()->count(2)->create();

        // Usuario admin fijo para pruebas
        Usuario::create([
            'nombre' => 'Admin Principal',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin1234'),
            'rol' => 'admin',
            'activo' => true,
        ]);
    }
}

