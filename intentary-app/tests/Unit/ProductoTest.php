<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function crear_producto_con_datos_validos()
    {
        $producto = Producto::factory()->create([
            'stock_actual' => 10,
            'stock_minimo' => 5,
        ]);

        $this->assertDatabaseHas('productos', [
            'id'           => $producto->id,
            'stock_actual' => 10,
            'stock_minimo' => 5,
        ]);
    }

    /** @test */
    public function calculo_de_stock_seguridad_no_es_mayor_que_stock_maximo()
    {
        $producto = Producto::factory()->create([
            'stock_seguridad' => 20,
            'stock_maximo'    => 15,
        ]);

        $this->assertTrue(
            $producto->stock_seguridad <= $producto->stock_maximo,
            'El stock de seguridad no debe exceder el stock máximo'
        );
    }
}

