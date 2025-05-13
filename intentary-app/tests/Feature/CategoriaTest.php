<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Categoria;

class CategoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_categoria_can_be_created(): void
    {
        $response = $this->postJson('/api/categorias', [
            'nombre' => 'Electrónica',
        ]);

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
