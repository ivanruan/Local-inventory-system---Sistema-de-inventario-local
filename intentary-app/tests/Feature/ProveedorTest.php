<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Proveedor;
use App\Models\Usuario; // Asegúrate de que el modelo se llame "Usuario"

class ProveedorTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_un_proveedor()
    {
        // 1. Crear y autenticar un usuario con Sanctum
        $usuario = Usuario::factory()->create();
        $token = $usuario->createToken('test-token')->plainTextToken;

        // 2. Hacer la petición con el token de autenticación
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/proveedores', [
            'nombre' => 'Proveedor de ejemplo',
        ]);

        // 3. Verificar la respuesta
        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Proveedor de ejemplo']);

        $this->assertDatabaseHas('proveedores', [
            'nombre' => 'Proveedor de ejemplo'
        ]);
    }

    public function test_valida_que_el_nombre_sea_requerido()
    {
        // 1. Crear y autenticar un usuario
        $usuario = Usuario::factory()->create();
        $token = $usuario->createToken('test-token')->plainTextToken;

        // 2. Hacer la petición sin el campo "nombre"
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/proveedores', []);

        // 3. Verificar que Laravel devuelve un error 422 (validación fallida)
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('nombre');
    }
}
