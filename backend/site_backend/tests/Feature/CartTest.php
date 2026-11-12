<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Cart;
use App\Models\ShoppingHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria um cliente e um produto válido para os testes
        $this->client = Client::factory()->create();
        $this->product = Product::factory()->create([
            'preco'   => 100.00,
            'estoque' => 10,
            'ativo'   => true,
        ]);
    }

    public function test_client_can_add_product_to_cart(): void
    {
        Sanctum::actingAs($this->client);

        $response = $this->postJson('/api/cart/items', [
            'product_id' => $this->product->id,
            'quantidade' => 2,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.items.0.product_id', $this->product->id)
                 ->assertJsonPath('data.items.0.quantidade', 2);

        // Compara o total geral sem rigidez estrita de tipos decimais do PHPUnit
        $this->assertEquals(200, $response->json('data.total_geral'));

        $this->assertDatabaseHas('cart_product', [
            'product_id' => $this->product->id,
            'quantidade' => 2,
        ]);
    }

    public function test_client_can_remove_product_from_cart(): void
    {
        Sanctum::actingAs($this->client);

        // Adiciona o produto primeiro
        $cart = Cart::factory()->create(['client_id' => $this->client->id]);
        $cart->products()->attach($this->product->id, ['quantidade' => 1, 'preco_unitario' => 100.00]);

        $response = $this->deleteJson("/api/cart/items/{$this->product->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('cart_product', [
            'product_id' => $this->product->id,
        ]);
    }

    public function test_client_can_clear_cart(): void
    {
        Sanctum::actingAs($this->client);

        $cart = Cart::factory()->create(['client_id' => $this->client->id]);
        $cart->products()->attach($this->product->id, ['quantidade' => 3, 'preco_unitario' => 100.00]);

        $response = $this->deleteJson('/api/cart');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
        ]);
    }

    public function test_client_can_buy_cart_items_and_generate_shopping_history(): void
    {
        Sanctum::actingAs($this->client);

        $cart = Cart::factory()->create(['client_id' => $this->client->id]);
        $cart->products()->attach($this->product->id, [
            'quantidade'     => 3,
            'preco_unitario' => 100.00
        ]);

        $response = $this->postJson('/api/cart/buy');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        // Verifica se foi registrado no ShoppingHistory
        $this->assertDatabaseHas('shopping_histories', [
            'client_id'  => $this->client->id,
            'product_id' => $this->product->id,
            'quantidade' => 3,
            'subtotal'   => 300.00,
        ]);

        // Verifica se o estoque do produto diminuiu (10 - 3 = 7)
        $this->assertDatabaseHas('products', [
            'id'      => $this->product->id,
            'estoque' => 7,
        ]);

        // Verifica se o carrinho foi esvaziado após a compra
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
        ]);
    }
}