<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Producto::class;

    public function definition()
    {
        return [
            'codigo'             => strtoupper($this->faker->unique()->bothify('PROD-###')),
            'nombre'             => $this->faker->words(3, true),
            'especificacion'     => $this->faker->sentence(6),
            'marca_id'           => Marca::factory(),
            'categoria_id'       => Categoria::factory(),
            'ubicacion_id'       => Ubicacion::factory(),
            'unidad'             => $this->faker->randomElement(['PZA', 'KG', 'L']),
            'nivel'              => $this->faker->numberBetween(1, 5),
            'stock_minimo'       => $this->faker->numberBetween(0, 10),
            'stock_actual'       => $this->faker->numberBetween(0, 100),
            'stock_maximo'       => $this->faker->numberBetween(101, 200),
            'stock_seguridad'    => $this->faker->numberBetween(1, 20),
            'duracion_inventario'=> $this->faker->numberBetween(10, 365),
            'status'             => $this->faker->randomElement(['Stock Optimo', 'Fuera de Stock']),
            'valor_unitario'              => $this->faker->randomFloat(2, 1, 1000),
            'vida_util'          => $this->faker->numberBetween(30, 1095),
            'observaciones'      => $this->faker->optional()->paragraph(),
        ];
    }
} 
