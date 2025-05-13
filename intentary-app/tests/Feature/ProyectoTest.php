<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Proyecto;

class ProyectoTest extends TestCase
{
	use RefreshDatabase;

    public function test_proyecto_can_be_created(): void
    {
        $response = $this->postJson('/api/proyectos', [
            'nombre' => 'Nuevo Proyecto',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nuevo Proyecto']);

        $this->assertDatabaseHas('proyectos', ['nombre' => 'Nuevo Proyecto']);
    }

    public function test_proyecto_can_be_updated(): void
    {
        $proyecto = Proyecto::factory()->create(['nombre' => 'Viejo Nombre']);

        $response = $this->putJson("/api/proyectos/{$proyecto->id}", [
            'nombre' => 'Nombre Actualizado',
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['nombre' => 'Nombre Actualizado']);

        $this->assertDatabaseHas('proyectos', ['nombre' => 'Nombre Actualizado']);
    }

    public function test_proyecto_can_be_deleted(): void
    {
        $proyecto = Proyecto::factory()->create();

        $response = $this->deleteJson("/api/proyectos/{$proyecto->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('proyectos', ['id' => $proyecto->id]);
    }
}
