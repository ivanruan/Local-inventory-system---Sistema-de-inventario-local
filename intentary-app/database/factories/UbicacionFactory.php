<?php

namespace Database\Factories;

use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ubicacion>
 */
class UbicacionFactory extends Factory
{
    protected $model = Ubicacion::class;

    public function definition(): array
    {
        return [
            'codigo' => strtoupper($this->faker->unique()->lexify('UBIC???')), // Ej: UBIC123
            'nivel' => $this->faker->numberBetween(1, 5),
        ];
    }
}
