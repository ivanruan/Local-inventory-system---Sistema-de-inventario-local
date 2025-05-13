<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Adjunto;
use App\Models\MovimientoInventario;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Adjunto>
 */
class AdjuntoFactory extends Factory
{
     protected $model = Adjunto::class;

    public function definition(): array
    {
        return [
            'movimiento_id' => MovimientoInventario::factory(),
            'tipo'          => $this->faker->randomElement(['factura', 'manual', 'foto', 'otros']),
            'ruta_archivo'  => $this->faker->imageUrl(640, 480, 'technics', true, 'adjunto'),
            'descripcion'   => $this->faker->optional()->sentence(8),
        ];
    }
}
