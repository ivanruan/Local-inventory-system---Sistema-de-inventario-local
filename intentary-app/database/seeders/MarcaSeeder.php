<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marca;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        Marca::factory()->count(10)->create();

        // Algunas marcas fijas de ejemplo
        $marcas = ['Sony', 'Samsung', 'HP', 'Lenovo', 'Logitech'];
        foreach ($marcas as $nombre) {
            Marca::create(['nombre' => $nombre]);
        }
    }
}

