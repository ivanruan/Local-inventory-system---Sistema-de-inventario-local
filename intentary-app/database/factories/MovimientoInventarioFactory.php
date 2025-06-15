<?php

namespace Database\Factories;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovimientoInventario>
 */
class MovimientoInventarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Aseguramos que hay productos, usuarios y opcionalmente proveedores y proyectos
        return [
            'fecha_hora'           => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tipo'                 => $this->faker->randomElement(['entrada', 'salida']),
            'cantidad'             => $this->faker->randomFloat(2, 1, 100),
            'producto_id'          => Producto::factory(),
            'proveedor_id'         => $this->faker->optional()->randomElement(Proveedor::pluck('id')->toArray()),
            'proyecto_id'          => $this->faker->optional()->randomElement(Proyecto::pluck('id')->toArray()),
            'usuario_id'           => Usuario::factory(),
            'tiempo_uso_acumulado' => $this->faker->randomFloat(2, 0, 50),
            'documento_soporte'    => $this->faker->optional()->bothify('DOC-#####') . '.pdf',
            'motivo'        => $this->faker->optional()->sentence(),
            'procedimiento_disposicion' => $this->faker->optional()->paragraph(),
            'observaciones'        => $this->faker->optional()->text(),
        ];
    }
}
