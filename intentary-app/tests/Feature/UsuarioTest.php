<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use Laravel\Sanctum\Sanctum;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_ser_creado()
    {
        // Autenticar un usuario con Sanctum
        $user = Usuario::factory()->create(['rol' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/usuarios', [
            'nombre' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'operador',
            'activo' => true,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('usuarios', ['email' => 'testuser@example.com']);
    }

    public function test_usuario_puede_ser_actualizado()
    {
        $user = Usuario::factory()->create(['rol' => 'admin']);
        Sanctum::actingAs($user);

        $usuario = Usuario::factory()->create();

        $response = $this->putJson("/api/usuarios/{$usuario->id}", [
            'nombre' => 'Nuevo Nombre',
            'email' => 'nuevoemail@example.com',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('usuarios', ['email' => 'nuevoemail@example.com']);
    }

    public function test_usuario_puede_ser_eliminado()
    {
        $user = Usuario::factory()->create(['rol' => 'admin']);
        Sanctum::actingAs($user);

        $usuario = Usuario::factory()->create();

        $response = $this->deleteJson("/api/usuarios/{$usuario->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('usuarios', ['id' => $usuario->id]);
    }
}
