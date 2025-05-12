<?php

namespace Tests\Feature;

use App\Models\Mantenimiento;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MantenimientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_mantenimiento(): void
    {
        $producto = Producto::factory()->create();

        $payload = [
            'producto_id' => $producto->id,
            'fecha_programada' => now()->addDays(5)->toDateString(),
            'tipo' => 'preventivo',
            'status' => 'pendiente',
        ];

        $response = $this->postJson('/api/mantenimientos', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['tipo' => 'preventivo']);

        $this->assertDatabaseHas('mantenimientos', ['producto_id' => $producto->id]);
    }

    public function test_mantenimiento_requires_valid_fields(): void
    {
        $response = $this->postJson('/api/mantenimientos', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['producto_id', 'fecha_programada', 'tipo', 'status']);
    }

    public function test_user_can_view_mantenimientos(): void
    {
        $mantenimiento = Mantenimiento::factory()->create();

        $response = $this->getJson("/api/mantenimientos/{$mantenimiento->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $mantenimiento->id]);
    }
}

