<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_ser_creado()
    {
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
        $usuario = Usuario::factory()->create();

        $response = $this->deleteJson("/api/usuarios/{$usuario->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('usuarios', ['id' => $usuario->id]);
    }
}
