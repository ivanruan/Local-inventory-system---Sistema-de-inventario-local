<?php

namespace Database\Factories;

use App\Models\Mantenimiento;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mantenimiento>
 */
class MantenimientoFactory extends Factory
{
    protected $model = Mantenimiento::class;

    public function definition(): array
    {
        $fechaProgramada = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $fechaEjecucion = $this->faker->optional()->dateTimeBetween($fechaProgramada, '+2 months');

        return [
            'producto_id' => Producto::factory(),
            'fecha_programada' => $fechaProgramada,
            'fecha_ejecucion' => $fechaEjecucion,
            'tipo' => $this->faker->randomElement(['preventivo', 'correctivo', 'limpieza']),
            'descripcion' => $this->faker->optional()->sentence(),
            'responsable' => $this->faker->name(),
            'status' => $this->faker->randomElement(['pendiente', 'completado', 'cancelado']),
            'costo' => $this->faker->optional()->randomFloat(2, 0, 5000),
            'observaciones' => $this->faker->optional()->paragraph(),
        ];
    }
}
 
