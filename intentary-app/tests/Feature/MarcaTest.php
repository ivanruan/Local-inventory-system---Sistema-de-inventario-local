<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Marca;

class MarcaTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_una_marca()
    {
        $response = $this->postJson('/api/marcas', [
            'nombre' => 'Nueva Marca'
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('marcas', ['nombre' => 'Nueva Marca']);
    }

    public function test_puede_listar_marcas()
    {
        Marca::factory()->count(3)->create();

        $response = $this->getJson('/api/marcas');

        $response->assertOk()
                 ->assertJsonCount(3);
    }

    public function test_puede_actualizar_marca()
    {
        $marca = Marca::factory()->create(['nombre' => 'Original']);

        $response = $this->putJson("/api/marcas/{$marca->id}", [
            'nombre' => 'Actualizado'
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('marcas', ['nombre' => 'Actualizado']);
    }

    public function test_puede_eliminar_marca()
    {
        $marca = Marca::factory()->create();

        $response = $this->deleteJson("/api/marcas/{$marca->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('marcas', ['id' => $marca->id]);
    }
}
