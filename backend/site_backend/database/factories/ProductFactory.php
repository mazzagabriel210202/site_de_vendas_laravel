<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'nome'      => $this->faker->unique()->words(3, true),
            'descricao' => $this->faker->sentence(10),
            'preco'     => $this->faker->randomFloat(2, 10, 1000), // Preço entre R$ 10,00 e R$ 1.000,00
            'estoque'   => $this->faker->numberBetween(1, 100),
            'ativo'     => true,
        ];
    }
}