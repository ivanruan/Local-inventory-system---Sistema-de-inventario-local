<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Autorizacion;
use App\Models\MovimientoInventario;
use Illuminate\Foundation\Testing\RefreshDatabase;

use PHPUnit\Framework\TestCase;

class AutorizacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_autorizacion_can_be_created()
    {
        $movimiento = MovimientoInventario::factory()->create();

        $autorizacion = Autorizacion::factory()->create([
            'movimiento_id' => $movimiento->id,
        ]);

        $this->assertDatabaseHas('autorizaciones', [
            'id' => $autorizacion->id,
            'movimiento_id' => $movimiento->id,
        ]);
    }
}
