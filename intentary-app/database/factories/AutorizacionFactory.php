<?php

namespace Database\Factories;

use App\Models\Autorizacion;
use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autorizacion>
 */
class AutorizacionFactory extends Factory
{
    protected $model = Autorizacion::class;

    public function definition()
    {
        return [
            'movimiento_id' => MovimientoInventario::factory(),
            'autorizador_nombre' => $this->faker->name,
            'autorizador_cargo' => $this->faker->jobTitle,
            'firma_url' => $this->faker->imageUrl(200, 100, 'business', true, 'firma'),
            'fecha_autorizacion' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'observaciones' => $this->faker->optional()->paragraph,
        ];
    }
}
