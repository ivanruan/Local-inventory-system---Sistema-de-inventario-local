<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Models\Usuario;
use App\Models\MovimientoInventario;
use App\Models\Mantenimiento;
use App\Models\Autorizacion;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Adjunto;
use App\Models\AlertaStock;

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
	Ubicacion::factory()->count(10)->create();
	Categoria::factory()->count(10)->create();
	Proveedor::factory()->count(10)->create();
	Proyecto::factory()->count(10)->create();

        // Crear movimientos y productos asociados
        MovimientoInventario::factory()
            ->count(100)
	    ->create();
	AlertaStock::factory()->count(10)->create();
	

    	$this->call(UsuarioSeeder::Class);
	$this->call(MarcaSeeder::Class);
	$this->call(CategoriaSeeder::Class);
	$this->call(MantenimientoSeeder::Class);
    }
}
