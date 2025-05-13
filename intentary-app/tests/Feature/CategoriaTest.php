<?php

namespace Tests\Feature;

use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CategoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_categoria_can_be_created(): void
    {
        $response = $this->postJson('/api/categorias', [
            'nombre' => 'Electrónica',
        ]);

        // Debug si falla
        if ($response->status() !== 201) {
            dump($response->json());
        }

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Electrónica']);

        $this->assertDatabaseHas('categorias', ['nombre' => 'Electrónica']);
    }

    public function test_categoria_can_be_updated(): void
    {
        $categoria = Categoria::factory()->create(['nombre' => 'Original']);

        $response = $this->putJson("/api/categorias/{$categoria->id}", [
            'nombre' => 'Actualizada',
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['nombre' => 'Actualizada']);

        $this->assertDatabaseHas('categorias', ['nombre' => 'Actualizada']);
    }

    public function test_categoria_can_be_deleted(): void
    {
        $categoria = Categoria::factory()->create();

        $response = $this->deleteJson("/api/categorias/{$categoria->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }
}
