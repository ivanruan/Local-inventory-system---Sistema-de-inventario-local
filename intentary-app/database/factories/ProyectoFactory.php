<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProyectoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->company(),
        ];
    }
}

