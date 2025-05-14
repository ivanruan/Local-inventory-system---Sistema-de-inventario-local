<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CategoriaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_categoria_can_be_created(): void
    {
        $usuario = Usuario::factory()->create(); // crea un usuario

        $response = $this->actingAs($usuario, 'sanctum')->postJson('/api/categorias', [
            'nombre' => 'Electrónica',
        ]);

        dump($response->status(), $response->json());

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Electrónica']);

        $this->assertDatabaseHas('categorias', ['nombre' => 'Electrónica']);
    }

    public function test_categoria_can_be_updated(): void
    {
        $usuario = Usuario::factory()->create();

        $categoria = Categoria::factory()->create(['nombre' => 'Original']);

        $response = $this->actingAs($usuario, 'sanctum')->putJson("/api/categorias/{$categoria->id}", [
            'nombre' => 'Actualizada',
        ]);

        dump($response->status(), $response->json());

        $response->assertOk()
                 ->assertJsonFragment(['nombre' => 'Actualizada']);

        $this->assertDatabaseHas('categorias', ['nombre' => 'Actualizada']);
    }

    public function test_categoria_can_be_deleted(): void
    {
        $usuario = Usuario::factory()->create();

        $categoria = Categoria::factory()->create();

        $response = $this->actingAs($usuario, 'sanctum')->deleteJson("/api/categorias/{$categoria->id}");

        dump($response->status(), $response->getContent());

        $response->assertNoContent();

        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }
}

