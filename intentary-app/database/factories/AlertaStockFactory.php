<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\AlertaStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AlertaStock>
 */
class AlertaStockFactory extends Factory
{
    protected $model = AlertaStock::class;

    public function definition()
    {
        return [
            'producto_id'      => Producto::factory(),
            'fecha_generacion' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'tipo_alerta'      => $this->faker->randomElement(['Stock Bajo', 'Stock Excedido', 'Vida Útil Límite']),
            'nivel_critico'    => $this->faker->randomElement(['Alto', 'Medio', 'Bajo']),
            'resuelta'         => $this->faker->boolean(30),
            'resuelta_en'      => function (array $attributes) {
                return $attributes['resuelta'] ? $this->faker->dateTimeBetween($attributes['fecha_generacion'], 'now') : null;
            },
        ];
    }
}
