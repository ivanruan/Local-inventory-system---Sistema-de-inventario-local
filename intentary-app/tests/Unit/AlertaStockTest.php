<?php

// tests/Unit/AlertaStockTest.php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\AlertaStock;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlertaStockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_alerta_stock()
    {
        $alerta = AlertaStock::factory()->create([
            'resuelta'    => false,
            'nivel_critico' => 'Alto',
        ]);

        $this->assertDatabaseHas('alertas_stock', [
            'id'            => $alerta->id,
            'resuelta'      => false,
            'nivel_critico'=> 'Alto',
        ]);
    }

    /** @test */
    public function scope_sin_resolver_funciona_correctamente()
    {
        AlertaStock::factory()->count(3)->state(['resuelta' => false])->create();
        AlertaStock::factory()->count(2)->state(['resuelta' => true])->create();

        $sinResolver = AlertaStock::sinResolver()->get();
        $this->assertCount(3, $sinResolver);
        $this->assertTrue($sinResolver->every(fn($a) => $a->resuelta === false));
    }

    /** @test */
    public function scope_resueltas_funciona_correctamente()
    {
        AlertaStock::factory()->count(4)->state(['resuelta' => true])->create();
        AlertaStock::factory()->count(1)->state(['resuelta' => false])->create();

        $resueltas = AlertaStock::resueltas()->get();
        $this->assertCount(4, $resueltas);
        $this->assertTrue($resueltas->every(fn($a) => $a->resuelta === true));
    }

    /** @test */
    public function marcar_resuelta_actualiza_campos_correctamente()
    {
        $alerta = AlertaStock::factory()->create(['resuelta' => false]);
        $alerta->marcarResuelta();

        $this->assertTrue($alerta->resuelta);
        $this->assertNotNull($alerta->resuelta_en);
    }
}
