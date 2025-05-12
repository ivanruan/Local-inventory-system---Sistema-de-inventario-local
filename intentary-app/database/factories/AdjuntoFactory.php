<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Adjunto>
 */
class AdjuntoFactory extends Factory
{
    public function definition(): array
    {
        $extension = $this->faker->fileExtension();
        $nombreGuardado = Str::uuid() . '.' . $extension;

        return [
            'tipo' => 'App\\Models\\Producto', // o cualquier otro modelo relacionado
            'relacionado_id' => 1, // Actualiza con IDs reales si es necesario
            'nombre_original' => $this->faker->word() . '.' . $extension,
            'nombre_guardado' => $nombreGuardado,
            'extension' => $extension,
            'tamanio_kb' => $this->faker->numberBetween(10, 5120),
            'url' => '/storage/adjuntos/' . $nombreGuardado,
        ];
    }
}
