<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Proveedor;

class ProveedorTest extends TestCase
{
     use RefreshDatabase;

    /** @test */
    public function puede_crear_un_proveedor()
    {
        $response = $this->postJson('/api/proveedores', [
            'nombre' => 'Proveedor de ejemplo',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Proveedor de ejemplo']);

        $this->assertDatabaseHas('proveedores', [
            'nombre' => 'Proveedor de ejemplo'
        ]);
    }

    /** @test */
    public function valida_que_el_nombre_sea_requerido()
    {
        $response = $this->postJson('/api/proveedores', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('nombre');
    }
}
