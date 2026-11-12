<?php

namespace App\Services;

use App\DTOs\CartProductDTO;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ShoppingHistory;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Obtém ou cria o carrinho ativo para um determinado cliente.
     */
    public function getOrCreateCart(int $clientId): Cart
    {
        return Cart::firstOrCreate(['client_id' => $clientId]);
    }

    /**
     * Retorna o carrinho completo do cliente com seus produtos e subtotais.
     */
    public function getCartDetails(int $clientId): array
    {
        $cart = Cart::with('products')->where('client_id', $clientId)->first();

        if (!$cart) {
            return [
                'success' => true,
                'data' => [
                    'items' => [],
                    'total_geral' => 0.00
                ]
            ];
        }

        $totalGeral = 0;
        $items = $cart->products->map(function ($product) use (&$totalGeral) {
            $quantidade = $product->pivot->quantidade;
            // Usa o preço unitário salvo na pivot (ou o preço atual do produto como fallback)
            $precoUnitario = (float) ($product->pivot->preco_unitario ?? $product->preco);
            $subtotal = $quantidade * $precoUnitario;

            $totalGeral += $subtotal;

            return [
                'product_id'     => $product->id,
                'nome'           => $product->nome,
                'preco_unitario' => $precoUnitario,
                'quantidade'     => $quantidade,
                'subtotal'       => $subtotal,
            ];
        });

        return [
            'success' => true,
            'data' => [
                'cart_id'     => $cart->id,
                'items'       => $items,
                'total_geral' => $totalGeral,
            ]
        ];
    }

    /**
     * Adiciona ou atualiza um produto no carrinho do cliente.
     */
    public function addProduct(int $clientId, CartProductDTO $dto): array
    {
        $product = Product::find($dto->productId);

        if (!$product || !$product->ativo) {
            return [
                'success' => false,
                'message' => 'Produto não encontrado ou indisponível.'
            ];
        }

        if ($product->estoque < $dto->quantidade) {
            return [
                'success' => false,
                'message' => 'Quantidade solicitada indisponível em estoque.'
            ];
        }

        $cart = $this->getOrCreateCart($clientId);

        // Verifica se o produto já está no carrinho
        $existingPivot = $cart->products()->where('product_id', $product->id)->first();

        $novaQuantidade = $dto->quantidade;
        if ($existingPivot) {
            $novaQuantidade += $existingPivot->pivot->quantidade;
        }

        // Sincroniza na tabela pivot salvando o preço unitário atual do produto
        $cart->products()->syncWithoutDetaching([
            $product->id => [
                'quantidade'     => $novaQuantidade,
                'preco_unitario' => $product->preco,
            ]
        ]);

        return [
            'success' => true,
            'message' => 'Produto adicionado ao carrinho com sucesso!',
            'data'    => $this->getCartDetails($clientId)['data']
        ];
    }

    /**
     * Remove um produto específico do carrinho.
     */
    public function removeProduct(int $clientId, int $productId): array
    {
        $cart = Cart::where('client_id', $clientId)->first();

        if (!$cart) {
            return [
                'success' => false,
                'message' => 'Carrinho não encontrado.'
            ];
        }

        $cart->products()->detach($productId);

        return [
            'success' => true,
            'message' => 'Produto removido do carrinho com sucesso!',
            'data'    => $this->getCartDetails($clientId)['data']
        ];
    }

    /**
     * Esvazia completamente o carrinho.
     */
    public function clearCart(int $clientId): array
    {
        $cart = Cart::where('client_id', $clientId)->first();

        if ($cart) {
            $cart->products()->detach();
        }

        return [
            'success' => true,
            'message' => 'Carrinho esvaziado com sucesso!',
        ];
    }

    /**
     * Realiza a compra dos itens do carrinho, enviando-os para o histórico de compras
     * e atualizando o estoque dos produtos.
     */
    public function buy(int $clientId): array
    {
        $cart = Cart::with('products')->where('client_id', $clientId)->first();

        if (!$cart || $cart->products->isEmpty()) {
            return [
                'success' => false,
                'message' => 'O carrinho está vazio ou não foi encontrado.'
            ];
        }

        try {
            return DB::transaction(function () use ($cart, $clientId) {
                foreach ($cart->products as $product) {
                    $quantidadeDesejada = $product->pivot->quantidade;

                    if ($product->estoque < $quantidadeDesejada) {
                        throw new \Exception("Produto '{$product->nome}' não possui estoque suficiente.");
                    }

                    $precoUnitario = (float) ($product->pivot->preco_unitario ?? $product->preco);
                    $subtotal = $quantidadeDesejada * $precoUnitario;

                    // Salva no histórico de compras
                    ShoppingHistory::create([
                        'client_id'      => $clientId,
                        'product_id'     => $product->id,
                        'quantidade'     => $quantidadeDesejada,
                        'preco_unitario' => $precoUnitario,
                        'subtotal'       => $subtotal,
                    ]);

                    // Decrementa o estoque do produto
                    $product->decrement('estoque', $quantidadeDesejada);
                }

                // Esvazia o carrinho após finalizar a compra
                $cart->products()->detach();

                return [
                    'success' => true,
                    'message' => 'Compra realizada com sucesso! Os produtos foram adicionados ao seu histórico.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}