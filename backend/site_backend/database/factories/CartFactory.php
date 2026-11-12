<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(), // Cria um cliente automaticamente se nenhum for fornecido
        ];
    }
}