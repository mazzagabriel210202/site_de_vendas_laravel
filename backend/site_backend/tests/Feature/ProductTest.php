<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products_publicly(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'nome', 'descricao', 'preco', 'estoque', 'ativo', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    public function test_admin_can_create_product(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $productData = [
            'nome'      => 'Teclado Mecânico RGB',
            'descricao' => 'Teclado mecânico gamer switch blue',
            'preco'     => 299.99,
            'estoque'   => 15,
            'ativo'     => true,
        ];

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Produto criado com sucesso!',
                 ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'Teclado Mecânico RGB',
            'preco' => 299.99
        ]);
    }

    public function test_client_cannot_create_product(): void
    {
        /** @var Client $client */
        $client = Client::factory()->create();

        $productData = [
            'nome'      => 'Mouse Gamer',
            'descricao' => 'Mouse óptico 16000 DPI',
            'preco'     => 150.00,
            'estoque'   => 10,
        ];

        $response = $this->actingAs($client, 'sanctum')
                         ->postJson('/api/products', $productData);

        // Deve retornar 403 Forbidden devido à ProductPolicy
        $response->assertStatus(403);
    }

    public function test_admin_can_update_product(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();
        
        $product = Product::factory()->create([
            'nome' => 'Nome Antigo',
            'preco' => 100.00
        ]);

        $updateData = [
            'nome'  => 'Nome Atualizado',
            'preco' => 120.00
        ];

        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Produto atualizado com sucesso!',
                 ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'nome' => 'Nome Atualizado',
            'preco' => 120.00
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();
        
        $product = Product::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Produto excluído com sucesso!',
                 ]);

        $this->assertModelMissing($product);
    }
}