<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Models\Usuario;
use App\Models\MovimientoInventario;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       // Seed maestros
        Marca::factory()->count(5)->create();
        Categoria::factory()->count(5)->create();
        Ubicacion::factory()->count(10)->create();
	Usuario::factory()->count(5)->create();
	Mantenimiento::factory()->count(15)->create();
	Autorizacion::factory()->count(10)->create();	
	Adjunto::factory()->count(10)->create();

        // Crear movimientos y productos asociados
        MovimientoInventario::factory()
            ->count(100)
	    ->create();

	$this->call(AlertasStockSeeder::class);
	$this->call(MantenimientoSeeder::class);
	$this->call(AutorizacionSeeder::class);
    }
}
