<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Proyecto;
use App\Models\Usuario;

class ProyectoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear y autenticar un usuario para todas las pruebas
        $this->usuario = Usuario::factory()->create();
        $this->token = $this->usuario->createToken('test-token')->plainTextToken;
    }

    public function test_proyecto_can_be_created()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/proyectos', [
            'nombre' => 'Nuevo Proyecto',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nuevo Proyecto']);

        $this->assertDatabaseHas('proyectos', ['nombre' => 'Nuevo Proyecto']);
    }

    public function test_proyecto_can_be_updated()
    {
        $proyecto = Proyecto::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/proyectos/{$proyecto->id}", [
            'nombre' => 'Nombre Actualizado',
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['nombre' => 'Nombre Actualizado']);

        $this->assertDatabaseHas('proyectos', ['nombre' => 'Nombre Actualizado']);
    }

    public function test_proyecto_can_be_deleted()
    {
        $proyecto = Proyecto::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/proyectos/{$proyecto->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('proyectos', ['id' => $proyecto->id]);
    }
}
