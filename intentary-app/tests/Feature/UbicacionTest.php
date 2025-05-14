<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ubicacion;
use App\Models\Usuario;

class UbicacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear usuario y token para usar en todas las pruebas
        $this->usuario = Usuario::factory()->create();
        $this->token = $this->usuario->createToken('test-token')->plainTextToken;
    }

    public function test_puede_crear_una_ubicacion()
    {
        $data = [
            'codigo' => 'COD-001',
            'nivel' => 3,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/ubicaciones', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('ubicaciones', $data);
    }

    public function test_valida_datos_obligatorios()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/ubicaciones', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['codigo', 'nivel']);
    }

public function test_puede_actualizar_una_ubicacion()
{
    // Crear ubicación y usuario
    $ubicacion = Ubicacion::factory()->create(['codigo' => 'ORIGINAL']);
    $user = Usuario::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Verificar que la ubicación existe
    $this->assertDatabaseHas('ubicaciones', ['id' => $ubicacion->id]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ])->putJson("/api/ubicaciones/{$ubicacion->id}", [
        'codigo' => 'NUEVOCOD',
        'nivel' => 4,
    ]);

    // Depuración
    if ($response->status() !== 200) {
        dump('Error en actualización:', $response->json());
        dump('Ubicaciones en DB:', Ubicacion::all()->toArray());
    }

    $response->assertStatus(200);
    $this->assertDatabaseHas('ubicaciones', [
        'id' => $ubicacion->id,
        'codigo' => 'NUEVOCOD'
    ]);
}

public function test_puede_eliminar_una_ubicacion()
{
    $ubicacion = Ubicacion::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->deleteJson("/api/ubicaciones/{$ubicacion->id}");

    $response->assertStatus(204);
    $response->dump();    
    // Verifica que esté marcado como eliminado
    $this->assertSoftDeleted($ubicacion);
    
    // Verifica que no aparezca en consultas normales
    $this->assertNull(Ubicacion::find($ubicacion->id));
}

}
