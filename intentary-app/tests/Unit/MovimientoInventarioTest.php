<?php


// tests/Unit/MovimientoInventarioTest.php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MovimientoInventario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MovimientoInventarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_movimiento_con_datos_validos()
    {
        $movimiento = MovimientoInventario::factory()->create([
            'cantidad' => 25.50,
            'tipo'     => 'entrada',
        ]);

        $this->assertDatabaseHas('movimientos_inventario', [
            'id'       => $movimiento->id,
            'cantidad' => 25.50,
            'tipo'     => 'entrada',
        ]);
    }

    /** @test */
    public function scope_entrada_retorna_solo_entradas()
    {
        MovimientoInventario::factory()->count(3)->state(['tipo' => 'entrada'])->create();
        MovimientoInventario::factory()->count(2)->state(['tipo' => 'salida'])->create();

        $entradas = MovimientoInventario::entrada()->get();
        $this->assertCount(3, $entradas);
        $this->assertTrue($entradas->every(fn($m) => $m->tipo === 'entrada'));
    }

    /** @test */
    public function scope_salida_retorna_solo_salidas()
    {
        MovimientoInventario::factory()->count(4)->state(['tipo' => 'salida'])->create();
        MovimientoInventario::factory()->count(1)->state(['tipo' => 'entrada'])->create();

        $salidas = MovimientoInventario::salida()->get();
        $this->assertCount(4, $salidas);
        $this->assertTrue($salidas->every(fn($m) => $m->tipo === 'salida'));
    }
}
