<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Marca;
use App\Models\Usuario; // Asegúrate de que el modelo se llame "Usuario"

class MarcaTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_una_marca()
    {
        // 1. Crear un usuario y autenticarlo con Sanctum
        $usuario = Usuario::factory()->create();
        $token = $usuario->createToken('test-token')->plainTextToken;

        // 2. Hacer la petición con el token en los headers
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/marcas', [
            'nombre' => 'Nueva Marca'
        ]);

        // 3. Verificar la respuesta
        $response->assertCreated();
        $this->assertDatabaseHas('marcas', ['nombre' => 'Nueva Marca']);
    }

    public function test_puede_listar_marcas()
    {
        // No requiere autenticación si es una ruta pública
        Marca::factory()->count(3)->create();

        $response = $this->getJson('/api/marcas');

        $response->assertOk()
                 ->assertJsonCount(3);
    }

    public function test_puede_actualizar_marca()
    {
        $usuario = Usuario::factory()->create();
        $token = $usuario->createToken('test-token')->plainTextToken;

        $marca = Marca::factory()->create(['nombre' => 'Original']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/marcas/{$marca->id}", [
            'nombre' => 'Actualizado'
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('marcas', ['nombre' => 'Actualizado']);
    }

    public function test_puede_eliminar_marca()
    {
        $usuario = Usuario::factory()->create();
        $token = $usuario->createToken('test-token')->plainTextToken;

        $marca = Marca::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/marcas/{$marca->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('marcas', ['id' => $marca->id]);
    }
}
