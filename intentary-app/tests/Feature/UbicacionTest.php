<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ubicacion;

class UbicacionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_una_ubicacion()
    {
        $data = [
            'codigo' => 'UBIC001',
            'nivel' => 2,
        ];

        $response = $this->postJson('/api/ubicaciones', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('ubicaciones', $data);
    }

    /** @test */
    public function valida_datos_obligatorios()
    {
        $response = $this->postJson('/api/ubicaciones', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['codigo', 'nivel']);
    }

    /** @test */
    public function puede_actualizar_una_ubicacion()
    {
        $ubicacion = Ubicacion::factory()->create();

        $response = $this->putJson("/api/ubicaciones/{$ubicacion->id}", [
            'codigo' => 'NUEVOCOD',
            'nivel' => 4,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['codigo' => 'NUEVOCOD']);

        $this->assertDatabaseHas('ubicaciones', ['codigo' => 'NUEVOCOD']);
    }

    /** @test */
    public function puede_eliminar_una_ubicacion()
    {
        $ubicacion = Ubicacion::factory()->create();

        $response = $this->deleteJson("/api/ubicaciones/{$ubicacion->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('ubicaciones', ['id' => $ubicacion->id]);
    }
}
