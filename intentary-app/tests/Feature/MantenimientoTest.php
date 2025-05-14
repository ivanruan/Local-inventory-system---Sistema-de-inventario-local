<?php

namespace Tests\Feature;

use App\Models\Mantenimiento;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MantenimientoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear y autenticar un usuario
        $usuario = Usuario::factory()->create();
        $this->actingAs($usuario, 'sanctum');
    }

    /** @test */
    public function test_puede_crear_mantenimiento()
    {
        $producto = Producto::factory()->create();
    
        $datos = [
            'producto_id' => $producto->id,
            'fecha_programada' => now()->addWeek()->format('Y-m-d H:i:s'),
            'tipo' => 'preventivo',
            'status' => 'pendiente',
            'responsable' => 'Técnico Asignado',
            'descripcion' => 'Mantenimiento preventivo programado',
            'costo' => 1500.50,
            'observaciones' => 'Requiere herramientas especiales'
        ];

        $response = $this->postJson('/api/mantenimientos', $datos);

        $response->assertStatus(201)
                 ->assertJson([
                     'producto_id' => $datos['producto_id'],
                     'tipo' => $datos['tipo'],
                     'status' => $datos['status'],
                     'responsable' => $datos['responsable'],
                     'descripcion' => $datos['descripcion'],
                     'observaciones' => $datos['observaciones']
                 ]);
    
        $this->assertDatabaseHas('mantenimientos', [
            'producto_id' => $datos['producto_id'],
            'tipo' => $datos['tipo'],
            'status' => $datos['status'],
            'responsable' => $datos['responsable']
        ]);
    }

    /** @test */
    public function test_validacion_campos_requeridos()
    {
        $response = $this->postJson('/api/mantenimientos', []);
    
        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'producto_id',
                     'fecha_programada',
                     'tipo',
                     'status'
                 ]);
     
        // Note: 'responsable' is now nullable in your StoreMantenimientoRequest
        // so it shouldn't be in the required fields list
    }

    /** @test */
    public function test_puede_actualizar_mantenimiento()
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'status' => 'pendiente'
        ]);

        $datosActualizados = [
            'status' => 'completado',
            'fecha_ejecucion' => now()->format('Y-m-d H:i:s'),
            'observaciones' => 'Mantenimiento completado con éxito'
        ];

        $response = $this->putJson("/api/mantenimientos/{$mantenimiento->id}", $datosActualizados);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'status' => 'completado',
                     'observaciones' => 'Mantenimiento completado con éxito'
                 ]);
    }

    /** @test */
    public function test_puede_eliminar_mantenimiento()
    {
       $mantenimiento = Mantenimiento::factory()->create();
    
       $response = $this->deleteJson("/api/mantenimientos/{$mantenimiento->id}");
    
        if ($response->status() !== 204) {
            dd($response->json());
        }
    
        $response->assertStatus(204);
        $this->assertDatabaseMissing('mantenimientos', ['id' => $mantenimiento->id]);
    }

    /** @test */
   
    public function test_puede_listar_mantenimientos()
    {
        Mantenimiento::factory()->count(5)->create();

        $response = $this->getJson('/api/mantenimientos');

        $response->assertStatus(200);
    
        // Opción 1: Si tu API devuelve un array directamente
        $response->assertJsonCount(5);
    
        // Opción 2: Si tu API usa paginación con estructura {data: [...]}
        // $response->assertJsonCount(5, 'data');
        // $response->assertJsonStructure([
        //     'data' => [
        //         '*' => ['id', 'producto_id', 'tipo', 'status']
        //     ]
        // ]);
    }

    /** @test */
    public function test_puede_ver_mantenimiento_especifico()
    {
        $mantenimiento = Mantenimiento::factory()->create();

        $response = $this->getJson("/api/mantenimientos/{$mantenimiento->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $mantenimiento->id,
                     'producto_id' => $mantenimiento->producto_id,
                     'tipo' => $mantenimiento->tipo
                 ]);
    }
}
